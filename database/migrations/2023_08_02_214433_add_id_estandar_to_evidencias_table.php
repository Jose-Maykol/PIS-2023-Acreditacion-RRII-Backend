<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evidencias', function (Blueprint $table) {
            $table->unsignedInteger('id_estandar')->after('id_tipo')->nullable();
            $table->foreign('id_estandar')->references('id')->on('estandars');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('evidencias', function (Blueprint $table) {
            $table->dropForeign(['id_estandar']);
            $table->dropColumn('id_estandar');
        });
    }
};
