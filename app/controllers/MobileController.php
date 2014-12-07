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
		$token = Hash::make(Input::get('email').time());

		return Response::json(['status'=>'success','token'=>$token]);
	}

	public function attemptlogin(){

		if (Auth::attempt(['email'=>Input::get('email'),'password'=>Input::get('password')] ))
		{
			// Generate token and set it in alternate database. Don't use Eloquent
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

	public function getstatus(){

		// Get corresponding User ID for token
		//$user_id = DB::table('mobiletokens')->where('token', Input::get('token'))->pluck('user_id');

		// Get meal name of most recently uploaded photo


		// set HTTP header
		$headers = array(
		    'Content-Type: application/json',
		);

		$mealname = 'salad';

		// query string
		$fields = array(
		    'results' => '0:1',
		    'fields' => 'nf_calories,nf_cholesterol,nf_total_fat,nf_serving_weight_grams',
		    'appId' => '65a327b9',
		    'appKey' => '7506bb427b7a5c989c48d64d68c27421',
		);
		$url = 'https://api.nutritionix.com/v1_1/search/' . $mealname . '?' . http_build_query($fields);
		//$url = 'https://api.nutritionix.com/v1_1/search/taco?results=0%3A2&fields=nf_calories%2Cnf_sugars&appId=65a327b9&appKey=7506bb427b7a5c989c48d64d68c27421'

		// Open connection
		$ch = curl_init();

		// Set the url, number of GET vars, GET data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, false);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// Execute request
		$result = curl_exec($ch);

		// Close connection
		curl_close($ch);

		// get the result and parse to JSON
		$result_arr = json_decode($result);

		/*$items = $result_arr->hits;

		// Get calories, cholesterol, fat, and serving size from Nutritionix API response
		foreach ($items as $item) {
			$calories = $item->fields->nf_calories;
			$cholesterol = $item->fields->nf_cholesterol;
			$fat = $item->fields->nf_total_fat;
			$serving_size = $item->fields->nf_serving_weight_grams;
		}

		// Insufficient data to calculate meal score
		if ($serving_size == null || $calories == null || $cholesterol == null || $fat == null) {
			return Response::json(['status' => 'failed', 'reason' => 'Serving size not found', 'calories'=>$calories, 'cholesterol'=>$cholesterol, 'fat'=>$fat]);
		}

		// Normalize calories, cholesterol and fat for given meal
		$calories = $calories/$serving_size;
		$cholesterol = $cholesterol/$serving_size;
		$fat = $fat/$serving_size;

		// Scale calories, cholesterol and fat between 0.5 and 3.5 for a uniform score across all meals
		$scaled_calories = 0.5 + (($calories - 0.17)*3)/2.86;
		$scaled_fat = 0.5 + (($fat - 0.003)*3)/0.177;
		$scaled_cholesterol = 0.5 + ($cholesterol*3);

		$score = 0.2*$scaled_calories + 0.4*$scaled_fat + 0.4*$scaled_cholesterol; */

		return Response::json(['status' => 'success', 'score' => $result_arr]);
	}

}