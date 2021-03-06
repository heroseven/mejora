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
			$table->float('f1',8,2);
			$table->float('f2',8,2);
		    $table->float('f3',8,2);
		    $table->float('f4',8,2);
		    $table->float('f5',8,2);
		    $table->float('f6',8,2);
		    $table->float('f7',8,2);
		    $table->float('f8',8,2);
		    $table->float('f9',8,2);
		    $table->float('f10',8,2);
		    $table->float('f11',8,2);
		    $table->float('f12',8,2);
		    $table->float('f13',8,2);
		    $table->float('f14',8,2);
		    $table->float('f15',8,2);
		    $table->float('f16',8,2);
		    $table->float('f17',8,2);
		    $table->float('f18',8,2);
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
