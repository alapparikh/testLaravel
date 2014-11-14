<?php

class MobileController extends \BaseController {

	public function store(){

		//$input = Input::all();

		try { User::create([
			'username' => Input::get('username'),
			'email' => Input::get('email'),
			'password' => Hash::make(Input::get('password'))
			]);
		} catch (Exception $e){
			return Response::json(['status'=>'failed','token'=>'']);
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
			$id = DB::table('mobiletokens')->select('user_id')->where('token','=',Input::get('token'))->get();
			//$id = DB::select('select user_id from mobiletokens where token = ?', array(Input::get('token')));
			//$links = DB::table('photos')->select('link'/*,'description','latitude','longitude','created_at'*/)->where('user_id','=',$id)->get();
			$links = DB::table('photos')->select('link')->where('user_id','=',$id['user_id'])->orderBy('created_at','desc')->get();
		} catch (Exception $e){
			return Response::json(['status' => 'failed']);
		}
		return $links;
	}
}