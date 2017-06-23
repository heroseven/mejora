<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CraateTableDf extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('df', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('factor1');
            $table->integer('factor2');
            $table->integer('factor3');
            $table->integer('factor4');
            $table->integer('factor5');
            $table->integer('factor6');
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
         Schema::drop('df');
    }
}
