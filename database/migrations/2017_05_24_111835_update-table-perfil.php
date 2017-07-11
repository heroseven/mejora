<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTablePerfil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interes', function (Blueprint $table) {
           $table->float('prediccion');
           $table->float('satisfaccion');
           $table->float('error');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interes', function (Blueprint $table) {
            $table->dropColumn('prediccion');
            $table->dropColumn('satisfaccion');
            $table->dropColumn('error');
        });
    }
}
