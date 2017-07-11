<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use App\Models\Preferencias;
class Mape extends Model {

	protected $table = 'mape';
	protected $fillable= ['id','id_usuario','mape'];
	public $timestamps = true;
	
	


}