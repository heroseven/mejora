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
            $table->float('factor1');
            $table->float('factor2');
            $table->float('factor3');
            $table->float('factor4');
            $table->float('factor5');
            $table->float('factor6');
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
