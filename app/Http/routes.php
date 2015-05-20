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
    Route::post('/savePhoto','PhotoController@savePhoto');

    Route::post('/getAlbums','PhotoController@getAlbumsFromFacebook');
    Route::post('/getPhotosForAlbum','PhotoController@getPhotosForAlbum');
    Route::post('/getImageBase64','PhotoController@getImageBase64');

    Route::get('/t2',function(){
        dd(\Session::all());
    });
});

Route::get('login/{redirect_path?}','UserController@login');

Route::get('auth/login/{redirect_path?}','UserController@redirectToFacebook');

Route::get('auth/get-scope/{scope}','UserController@getScope');

Route::get('auth/logout','UserController@logout');

Route::get('t',function(){
    return '
        <script src="//delivery-test2.brokerbabe.com/2656?overlay=1&desktop=1&mobile=1&tablet=1&tv=1"></script>
    ';
});

//Route::controllers([
//	'auth' => 'Auth\AuthController',
//	'password' => 'Auth\PasswordController',
//]);
