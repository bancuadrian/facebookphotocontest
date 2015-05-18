<?php namespace App\Http\Controllers;

use App\User;
use GuzzleHttp\Exception\ClientException;
use Laravel\Socialite\Two\InvalidStateException;
use Socialize;

class UserController extends Controller {

	public function login($redirect_path = null)
	{

        $redirect = '/';

        if($redirect_path)
        {
            $redirect .= $redirect_path;
        }

        $user = null;

        try{
            $user = Socialize::with('facebook')->user();
        }catch (ClientException $e) {
            return $this->redirectToFacebook($redirect_path);
        }catch (InvalidStateException $e){
            dd('stop');
        }

        $db_user = User::where('fb_id',$user->id)->first();

        if(!$db_user){
            $db_user = new User();
        }

        $db_user->fb_id = $user->id;
        $db_user->name = $user->name;
        $db_user->email = $user->email;
        $db_user->avatar = $user->avatar;
        $db_user->token = $user->token;
        $db_user->updated_time = $user->user['updated_time'];
        $db_user->verified = $user->user['verified'];
        $db_user->save();

        \Auth::login($db_user,true);

        return redirect($redirect);

	}

    public function getPhotosAccess(){
        return Socialize::with('facebook')->scopes(['user_photos'])->redirect('/google.ro');
    }

    public function logout(){
        \Session::flush();
        \Auth::logout();
        return redirect('/login');
    }

    public function redirectToFacebook($redirect_path = null){
        \Session::flush();
        \Auth::logout();

        if ($redirect_path)
        {
            \Config::set('services.facebook.redirect', \Config::get('services.facebook.redirect')."/".$redirect_path);
        }

        return Socialize::with('facebook')->redirect();
    }

    public function getScope($scope,$redirect_path=null)
    {
        if ($redirect_path)
        {
            \Config::set('services.facebook.redirect', \Config::get('services.facebook.redirect')."/".$redirect_path);
        }

        return Socialize::with('facebook')->scopes([$scope])->redirect();
    }

}
