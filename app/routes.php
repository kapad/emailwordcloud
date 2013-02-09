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


Route::any( '/push', 'PushController@store');

Route::get('/', function()
{
	return View::make('hello');
});

Route::get('/saveemail','HomeController@saveSendGridEmail');

Route::get('/showwordcloud','HomeController@showWordCloud');

Route::get('/getwords','HomeController@getWords');

