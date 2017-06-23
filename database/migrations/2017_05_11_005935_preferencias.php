<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Preferencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preferencias', function(Blueprint $table) {
            $table->increments('id');
            $table->float('identificacion');
			$table->float('factor1',8,2);
			$table->float('factor2',8,2);
		    $table->float('factor3',8,2);
		    $table->float('factor4',8,2);
		    $table->float('factor5',8,2);
		    $table->float('factor6',8,2);
		    $table->integer('total_atributos');
		    $table->integer('user1');
		    $table->integer('user2');
		    $table->integer('user3');
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
        Schema::drop('preferencias');
    }
}
