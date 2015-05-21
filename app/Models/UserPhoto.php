<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPhoto extends Model{

	protected $table = 'userphotos';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['user_id', 'filename'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function votes()
    {
        return $this->hasMany('App\Models\Vote','photo_id','id');
    }

    public function votesCount()
    {
        return $this->hasOne('App\Models\Vote','photo_id')
            ->selectRaw('photo_id, count(*) as aggregate')
            ->groupBy('photo_id');
    }
}
