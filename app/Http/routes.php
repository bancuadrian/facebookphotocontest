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

    Route::get('/getMyPhoto','PhotoController@getMyPhoto');
    Route::post('/removeMyPhoto','PhotoController@removeMyPhoto');

    Route::post('/getAllPhotos','PhotoController@getAllPhotos');

    Route::get('/friendsPhoto','PhotoController@getFriendsPhotos');

    Route::post('/votePhoto','PhotoController@votePhoto');

    Route::get('/t2',function(){
        dd(\Session::all());
    });
});

Route::get('login/{redirect_path?}','UserController@login');

Route::get('auth/login/{redirect_path?}','UserController@redirectToFacebook');

Route::get('auth/get-scope/{scope}','UserController@getScope');

Route::get('/cata',function(){
    \Session::flush();
    \Auth::logout();
    $user = App\User::find(127);

    \Auth::login($user,true);

    return redirect('/');
});

Route::get('auth/logout','UserController@logout');
