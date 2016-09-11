<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model {

	protected $table = 'documento';
	public $timestamps = true;

	public function terminos()
	{
		return $this->hasMany('App\Modelos\Terminos_de_documento', 'id');
	}

}