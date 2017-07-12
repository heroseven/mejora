<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Perfil extends Model {
	


	protected $table = 'perfil_usuario';
	protected $fillable= ['id','id_usuario','f1','f2','f3','f4','f5','f6','f7','f8','f9','f10','f11','f12','f13','f14','f15','f16','f17','f18'];
	public $timestamps = true;
    
     /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
   
    
    
}
