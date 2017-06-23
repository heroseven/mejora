<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CraateTablePerfil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('perfil_usuario', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_usuario');
			$table->float('factor1',8,2);
			$table->float('factor2',8,2);
		    $table->float('factor3',8,2);
		    $table->float('factor4',8,2);
		    $table->float('factor5',8,2);
		    $table->float('factor6',8,2);
		    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::drop('perfil_usuario');
    }
}
