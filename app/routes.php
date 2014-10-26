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

Route::get('/', function(){
	if (Auth::check()) return Redirect::to('/admin');
	return View::make('admin.landing');
}); 

Route::resource('users','UsersController');
Route::get('profile', 'UsersController@show');

Route::resource('sessions','SessionsController');
Route::get('login', 'SessionsController@create');
Route::get('logout', 'SessionsController@destroy');

Route::get('admin', 'HomeController@handleHome');

Route::post('mobcreate','MobileController@store');

Route::resource('photos','PhotosController');
