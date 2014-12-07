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

//USER CONTROLLER
Route::resource('users','UsersController');
Route::get('profile', 'UsersController@show');

//SESSIONS CONTROLLER
Route::resource('sessions','SessionsController');
Route::get('login', 'SessionsController@create');
Route::get('logout', 'SessionsController@destroy');

//OTHER ROUTES
Route::get('admin', 'HomeController@handleHome');
Route::get('dataexport', 'HomeController@dataExport');
//Route::get('testtoken', 'HomeController@testtoken');
Route::get('datadownload', 'HomeController@dataDownload');

//MOBILE REQUEST ROUTES
Route::post('mobcreate','MobileController@store');
Route::post('moblogin','MobileController@attemptlogin');
Route::get('getphotos','MobileController@getphotos');
Route::delete('logout','MobileController@logout');

//PHOTO CONTROLLER
Route::resource('photos','PhotosController');

//PASSWORD RESET ROUTES
Route::get('password/reset', array(
  'uses' => 'RemindersController@getRemind',
  'as' => 'password.remind'
));
Route::post('password/reset', array(
  'uses' => 'RemindersController@postRemind',
  'as' => 'password.reset'
));
Route::post('password/postreset', array(
  'uses' => 'RemindersController@postRemind',
  'as' => 'password.postreset'
));
Route::post('photos/webstore', array(
  'uses' => 'PhotosController@webstore',
  'as' => 'photos.webstore'
));

//TEST NUTRITIONIX API REQUEST
Route::get('getstatus','MobileController@getstatus');
//Route::get('getscores','MobileController@update_score_table');
