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

Route::get('/', 'HomeController@handleHome'); 

Route::resource('users','UsersController');
Route::get('profile', 'UsersController@show');

Route::resource('sessions','SessionsController');
Route::get('login', 'SessionsController@create');
Route::get('logout', 'SessionsController@destroy');

Route::get('admin', 'HomeController@handleHome');
Route::get('dataexport', 'HomeController@dataExport');

Route::post('mobcreate','MobileController@store');
Route::post('moblogin','MobileController@attemptlogin');

Route::resource('photos','PhotosController');
