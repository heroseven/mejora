<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CraateTableInteres extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_articulo')->unsigned();;
            $table->integer('id_usuario')->unsigned();;
            $table->integer('interes');
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
        Schema::drop('interes');
    }
}
