<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('terminos_de_documento', function(Blueprint $table) {
			$table->foreign('id_documento')->references('id')->on('documento')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
	}

	public function down()
	{
		Schema::table('terminos_de_documento', function(Blueprint $table) {
			$table->dropForeign('terminos_de_documento_id_documento_foreign');
		});
	}
}