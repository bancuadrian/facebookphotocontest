<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model{

	protected $table = 'votes';

    public function user()
    {
        return $this->hasOne('App\User');
    }

    public function photo()
    {
        return $this->hasOne('App\Models\UserPhoto');
    }

}
