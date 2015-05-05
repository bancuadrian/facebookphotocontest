<?php namespace App\Http\Controllers;

use App\User;
use GuzzleHttp\Exception\ClientException;
use Laravel\Socialite\Two\InvalidStateException;
use Socialize;

class UserController extends Controller {

	public function login()
	{

        if(\Auth::check()){
            return redirect('/');
        }

        $user = null;

        try{
            $user = Socialize::with('facebook')->user();
        }catch (ClientException $e) {
            return $this->redirectToFacebook();
        }catch (InvalidStateException $e){
            dd('stop');
        }

        $db_user = User::where('fb_id',$user->id)->first();

        if(!$db_user){
            $db_user = new User();
            $db_user->fb_id = $user->id;
            $db_user->name = $user->name;
            $db_user->email = $user->email;
            $db_user->avatar = $user->avatar;
            $db_user->token = $user->token;
            $db_user->updated_time = $user->user['updated_time'];
            $db_user->verified = $user->user['verified'];

            $db_user->save();
        }

        \Auth::login($db_user,true);

        return redirect('/');

	}

    public function logout(){
        \Session::flush();
        \Auth::logout();
        return redirect('/login');
    }

    public function redirectToFacebook(){
        \Session::flush();
        \Auth::logout();
        return Socialize::with('facebook')->redirect();
    }

}
