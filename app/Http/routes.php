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

\Facebook\FacebookSession::setDefaultApplication(env('FACEBOOK_APP_ID'), env('FACEBOOK_APP_SECRET'));

Route::group(['middleware' => ['auth']], function()
{
    Route::get('/', 'WelcomeController@index');
    Route::post('/savePhoto','PhotoController@savePhoto');

    Route::get('/t2',function(){
        $session = new \Facebook\FacebookSession(\Auth::user()->token);
        $me = (new \Facebook\FacebookRequest(
            $session, 'GET', '/me'
        ))->execute()->getGraphObject(GraphUser::className());

        return $me;
        //return Socialize::with('facebook')->scopes(['user_photos'])->redirect();
    });
});

Route::get('login','UserController@login');

Route::get('auth/login','UserController@redirectToFacebook');

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
