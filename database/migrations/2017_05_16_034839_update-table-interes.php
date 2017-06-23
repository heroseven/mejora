<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableInteres extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    //     Schema::table('interes', function (Blueprint $table) {
    //         $table->foreign('id_usuario')->references('id')->on('perfil_usuario')
				// 		->onDelete('restrict')
				// 		->onUpdate('restrict');
    //     });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//         Schema::table('interes', function(Blueprint $table) {
// 			$table->dropForeign('interes_id_usuario_foreign');
// 		});
    }
}
