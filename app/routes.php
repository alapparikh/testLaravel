<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	//return View::make('hello');
	/*$user = new User;
	$user->username = 'newuser';
	$user->password = Hash::make('kjgkla');
	$user->save();*/

	/*User::create([
		'username' => 'newuser',
		'password' => Hash::make('jaglkaj')
		]);*/

	return User::all();
}); 

Route::resource('users','UsersController');

Route::resource('sessions','SessionsController');
Route::get('login', 'SessionsController@create');
Route::get('logout', 'SessionsController@destroy');
