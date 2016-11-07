<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model {

	protected $table = 'perfiles';
	protected $fillable= ['id','data','id_usuario'];
	public $timestamps = true;



}