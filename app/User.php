<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
	// TODO: remove CapeCodMa from table name when using postgres container
	protected $table = 'CapeCodMa.Scenario_Users';
	protected $primaryKey = 'user_id';


	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	/**
	 * 
	 *  The user can have multiple Scenarios
	 * 
	 */
	public function scenarios()
	{
		return $this->hasMany('App\Scenario')->orderBy('CreateDate', 'desc');
	}

}
