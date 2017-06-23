<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Perfil extends Model {
	


	protected $table = 'perfil_usuario';
	protected $fillable= ['id','id_usuario','factor1','factor2','factor3','factor4','factor5','factor6'];
	public $timestamps = true;
    
     /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
   
    
    
}
