<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['middleware' => ['auth']], function()
{
    Route::get('/', 'WelcomeController@index');
});

Route::get('login','UserController@login');

Route::get('auth/login','UserController@redirectToFacebook');

Route::get('auth/logout','UserController@logout');

//Route::controllers([
//	'auth' => 'Auth\AuthController',
//	'password' => 'Auth\PasswordController',
//]);
