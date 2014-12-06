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

		// set HTTP header
		$headers = array(
		    'Content-Type: application/json',
		);

		$mealname = 'taco?';

		// query string
		$fields = array(
		    'results' => '0:1',
		    'fields' => 'nf_calories',
		    'appId' => '65a327b9',
		    'appKey' => '7506bb427b7a5c989c48d64d68c27421',
		);
		$url = 'https://api.nutritionix.com/v1_1/search/' . $mealname . http_build_query($fields);

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
		$result_arr = json_decode($result, true);

		$calories = $result_arr->hits[0]->fields->nf_calories;

		return Response::json(['result' => $calories]);
	}

}