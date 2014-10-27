<?php

class MobileController extends \BaseController {

	public function store(){

		//$input = Input::all();

		User::create([
			'username' => Input::get('username'),
			'email' => Input::get('email'),
			'password' => Hash::make(Input::get('password'))
			]);

		return Response::json(['status'=>'OK','token'=>'xxxxxxxxxx']);
	}

	public function attemptlogin(){

		if (Auth::attempt(['email'=>Input::get('email'),'password'=>Input::get('password')] ))
		{
			// Generate token and set it in alternate database. Don't use Eloquent
			return Response::json(['status'=>'success','token'=>'xxxxxxxxxx']);
		}
		return Response::json(['status'=>'failed']);
	}
}