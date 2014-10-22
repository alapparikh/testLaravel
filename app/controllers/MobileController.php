<?php

class MobileController extends \BaseController {

	public function store(){

		//$input = Input::all();

		User::create([
			'username' => Input::get('username'),
			'email' => Input::get('email'),
			'password' => Hash::make(Input::get('password'))
			]);


		$statusCode = 200;
		//$contents = array['bloo','blaa'];
		$value = "json";

		$response = Response::make($contents, $statusCode);

		$response->header('Content-Type', $value);

		return Response::json(['status'=>'OK']);
	}
}