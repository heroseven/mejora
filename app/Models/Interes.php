<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use App\Models\Preferencias;
class Interes extends Model {

	protected $table = 'interes';
	protected $fillable= ['id','id_articulo','id_usuario','interes','prediccion'];
	public $timestamps = true;
	
	public function articulo(){
		return $this->belongsTo('App\Models\Documento','id_articulo','id');
	}


}