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

}
