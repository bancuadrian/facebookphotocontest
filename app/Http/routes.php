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

    Route::get('/t2',function(){
        \Facebook\FacebookSession::setDefaultApplication('440229369435697','6cd4598aace14b4b1aa74d68ddbe6aa6');
        $session = new \Facebook\FacebookSession(\Auth::user()->token);
        //$session = new \Facebook\FacebookSession(\Auth::user()->token);

        $request = new \Facebook\FacebookRequest($session, 'GET', '/me/permissions');
        $response = $request->execute();
        $graphObject = $response->getGraphObject();

        dd($graphObject);
        //return Socialize::with('facebook')->scopes(['user_photos'])->redirect();
    });
});

Route::get('login/{redirect_path?}','UserController@login');

Route::get('auth/login/{redirect_path?}','UserController@redirectToFacebook');

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
