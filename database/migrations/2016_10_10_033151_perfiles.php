<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Perfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perfiles', function(Blueprint $table) {
			$table->increments('id');
			$table->text('data');
			$table->integer('id_usuario')->unsigned()->nullable();
			$table->foreign('id_usuario')->references('id')->on('users')
						->onDelete('restrict')
						->onUpdate('restrict');
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
        Schema::drop('perfiles');
    }
}
