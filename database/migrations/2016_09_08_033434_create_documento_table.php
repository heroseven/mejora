<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentoTable extends Migration {

	public function up()
	{
		Schema::create('documento', function(Blueprint $table) {
			$table->increments('id');
			$table->string('titulo');
			$table->text('descripcion');
			$table->text('contenido');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('documento');
	}
}