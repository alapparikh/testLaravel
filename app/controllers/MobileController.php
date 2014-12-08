<?php

class MobileController extends \BaseController {

	public function store(){

		$input = Input::all();

		if (! User::isValid($input)){
			return Response::json(['status'=>'failed','errors'=>User::$errors]);
		}

		try { User::create([
			'username' => Input::get('username'),
			'email' => Input::get('email'),
			'password' => Hash::make(Input::get('password'))
			]);
		} catch (Exception $e){
			return Response::json(['status'=>'failed','errors'=>json(['database'=>'failed to create user'])]);
		}

		$user_id = DB::table('users')->where('email',Input::get('email'))->pluck('id');
		DB::table('meal_scores')->insert(array('user_id' => $user_id));

		// Get token 
		$token = Hash::make(Input::get('email').time());
		DB::insert('insert into mobiletokens (user_id, token) values (?, ?)', array($user_id, $token));

		return Response::json(['status'=>'success','token'=>$token]);
	}

	public function attemptlogin(){

		if (Auth::attempt(['email'=>Input::get('email'),'password'=>Input::get('password')] ))
		{
			// Generate token and set it in alternate database
			$token = Hash::make(Input::get('email').time());
			DB::delete('delete from mobiletokens where user_id = ?', array(Auth::id()));
			DB::insert('insert into mobiletokens (user_id, token) values (?, ?)', array(Auth::id(), $token));

			Auth::logout();

			return Response::json(['status'=>'success','token'=>$token]);
		}
		return Response::json(['status'=>'failed','token'=>'']);
	}

	public function logout() {
		try {
			DB::delete('delete from mobiletokens where token = ?', array(Input::get('token')));

		} catch (Exception $e){
			return Response::json(['status' => 'failed']);
		}
		return Response::json(['status'=>'success']);
	}

	/*
	Returns links to all photos uploaded by given user
	*/
	public function getphotos(){
		try{
			$user_id = DB::table('mobiletokens')->where('token', Input::get('token'))->pluck('user_id');
			//$id = DB::select('select user_id from mobiletokens where token = ?', array(Input::get('token')));
			//$links = DB::table('photos')->select('link'/*,'description','latitude','longitude','created_at'*/)->where('user_id','=',$id)->get();
			$links = DB::table('photos')->select('link')->where('user_id','=',$user_id)->orderBy('created_at','desc')->get();
		} catch (Exception $e){
			return Response::json(['status' => 'failed']);
		}
		return Response::json(['status' => 'success', 'links' => $links]);
	}

	/*
	Returns new status for user based on latest meal photo
	*/
	public function getstatus(){

		// Get corresponding User ID for token
		$user_id = DB::table('mobiletokens')->where('token', Input::get('token'))->pluck('user_id');
		//$user_id = 15;

		// Get meal name of most recently uploaded photo
		$mealname = DB::table('photos')->where('user_id','=',$user_id)->orderBy('created_at','desc')->pluck('description');
		//$mealname = 'cheese pizza';

		// Get data from Nutritionix API
		$result = $this->query_api($mealname);

		// Get the result and parse to JSON
		$result_arr = json_decode($result);

		// Get calories, cholesterol, fat, and serving size from Nutritionix API response
		$items = $result_arr->hits;
		foreach ($items as $item) {
			$calories = $item->fields->nf_calories;
			$cholesterol = $item->fields->nf_cholesterol;
			$fat = $item->fields->nf_total_fat;
			$serving_size = $item->fields->nf_serving_weight_grams;
		}

		// Insufficient data to calculate meal score
		if ($serving_size == null) { 
			$score = 0.0;
			$function_status = 'failed';
		} else {
			// Get score based on calories, cholesterol, fat, and serving size
			$score = $this->calculate_score($calories,$cholesterol,$fat,$serving_size);
			$function_status = 'success';
		}

		// Update meal_scores_table with new score and status
		$status = $this->update_score_table($score,$user_id);

		// Update photos table with meal score
		$this->update_photos_table($score,$user_id);

		return Response::json(['status' => $function_status, 'userstatus' => $status]);
	}

	/*
	Returns score assuming salad to be the healthiest and fried chicken the unhealthiest foods to eat per gram
	*/
	public function calculate_score ($calories,$cholesterol,$fat,$serving_size){
		// Normalize calories, cholesterol and fat for given meal
		$calories = $calories/$serving_size;
		$cholesterol = $cholesterol/$serving_size;
		$fat = $fat/$serving_size;

		// Scale calories, cholesterol and fat between 0.5 and 3.5 for a uniform score across all meals
		$scaled_calories = 0.5 + (($calories - 0.17)*3)/2.86;
		$scaled_fat = 0.5 + (($fat - 0.003)*3)/0.177;
		$scaled_cholesterol = 0.5 + ($cholesterol*3);

		$score = 0.4*$scaled_calories + 0.4*$scaled_fat + 0.2*$scaled_cholesterol; 

		return $score;
	}

	/*
	Returns result of API query to Nutrionix database with meal name/description as the search parameter
	*/
	public function query_api ($mealname){
		// set HTTP header
		$headers = array('Content-Type: application/json');

		// query string
		$fields = array(
		    'results' => '0:1',
		    'fields' => 'nf_calories,nf_cholesterol,nf_total_fat,nf_serving_weight_grams',
		    'appId' => '65a327b9',
		    'appKey' => '7506bb427b7a5c989c48d64d68c27421',
		);

		// Build URL
		$mealname = rawurlencode($mealname);
		$url = 'https://api.nutritionix.com/v1_1/search/' . $mealname . '?' . http_build_query($fields);

		// Open connection
		$ch = curl_init();

		// Set the url, number of GET vars, GET data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// Execute request
		$result = curl_exec($ch);

		// Close connection
		curl_close($ch);

		return $result;
	}

	/*
	Updates meal_scores table to reflect the scores for the most recent 5 meals, and calculates and 
	updates current status of user.

	Returns current status.
	*/
	public function update_score_table ($score,$user_id) {
		
		if ($score > 0) {
			$scores = DB::table('meal_scores')->select('meal_1','meal_2','meal_3','meal_4','meal_5')->where('user_id',$user_id)->get();
			$scores = array_values($scores);
			$mealscores = array($scores[0]->meal_1,$scores[0]->meal_2,$scores[0]->meal_3,$scores[0]->meal_4,$scores[0]->meal_5);
			$count = 0;
			$sum = 0.0;
			foreach ($mealscores as $mealscore){
				if ($mealscore > 0) {
					$sum = $sum + $mealscore;
					$count = $count + 1;
				}
			}
			$sum = $sum + $score;
			$status = $sum/($count + 1);
			if ($status < 0.5) {
				$status = 0.5;
			}
			elseif ($status >= 3.5) {
				$status = 3.499999;
			}

			// Update database table with new meal score value
			$mealscores = array_values($mealscores);
			DB::table('meal_scores')
            ->where('user_id', $user_id)
            ->update(array('meal_1' => $score,'meal_2' => $mealscores[0],'meal_3' => $mealscores[1],'meal_4' => $mealscores[2],'meal_5' => $mealscores[3],'current_status' => $status));
		} 
		else {
			$status = DB::table('meal_scores')->where('user_id',$user_id)->pluck('current_status');
		}
		
		$status = round($status);
		return $status;
	}

	/*
	Inserts calculated score for most recent meal photo
	*/
	public function update_photos_table ($score, $user_id) {

		$link = DB::table('photos')->where('user_id',$user_id)->orderBy('created_at','desc')->pluck('link');
		DB::table('photos')->where('link',$link)->update(array('meal_score' => $score));
	}

}