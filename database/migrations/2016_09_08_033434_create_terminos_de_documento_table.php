<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTerminosDeDocumentoTable extends Migration {

	public function up()
	{
		Schema::create('terminos_de_documento', function(Blueprint $table) {
			$table->increments('id');
			$table->string('termino');
			$table->integer('frecuencia');
			$table->float('peso');
			$table->integer('id_documento')->unsigned()->nullable();
		});
	}

	public function down()
	{
		Schema::drop('terminos_de_documento');
	}
}