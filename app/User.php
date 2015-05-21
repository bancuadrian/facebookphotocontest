<?php namespace App;

use App\Models\Vote;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['remember_token'];


    public static function canVote($photo_id)
    {
        $canVote = true;

        $oneHourAgo = date('Y-m-d H:i:s',time() - (60 * 60));

        $votes = Vote::
            where('user_id',\Auth::user()->id)
            ->where('photo_id',$photo_id)
            ->where('created_at','>',$oneHourAgo)
            ->get();


        if($votes->count() > 0)
        {
            $canVote = false;
        }

        return $canVote;
    }

}
