<?php

namespace App;

use App\Task;


use Illuminate\Database\Eloquent\Model;

class User extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get all of the tasks for the user.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    
    public function perfiles()
	{
		return $this->hasMany('App\Modelos\Perfiles', 'id');
	}
}
