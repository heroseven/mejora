<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Preferencias extends Model {
	


	protected $table = 'preferencias';
	protected $fillable= ['id','identificacion','factor1','factor2','factor3','factor4','factor5','factor6','total_atributos', 'user1','user2','user3'];
	public $timestamps = true;
    
     /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
   
    public function articulo(){
		return $this->belongsTo('App\Models\Documento','identificacion');
	}
    
}
