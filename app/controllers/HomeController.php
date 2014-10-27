<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function handleHome()
	{
		if (Auth::check()) return View::make('admin.admin');
		return View::make('admin.landing');
	}

	public function dataExport() {
		if (Auth::attempt(['email'=>Input::get('email'),'password'=>Input::get('password')] ))
		{
			$id = Auth::id();
			$results = DB::select('select * from users where id = ?', $id;
			//$bum = DB::table('users')->where('id', $id);
			//$photos = DB::table('photos')->where('user_id', $id);
			Auth::logout();
			return Response::json($results);
		}
		return Response::json(['status'=>'failed']);
	}

}
