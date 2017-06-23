<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Df extends Model {

	protected $table = 'df';
	protected $fillable= ['id','factor1','factor2','factor3','factor4','factor5','factor6'];
	public $timestamps = true;



}