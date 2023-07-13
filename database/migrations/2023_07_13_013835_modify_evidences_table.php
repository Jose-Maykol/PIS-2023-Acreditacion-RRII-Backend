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
            $table->unsignedBigInteger('id_tipo')->default(1);

            $table->foreign('id_tipo')
                ->references('id')
                ->on('evidencias_tipo')
                ->onDelete('cascade');
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
            Schema::table('evidencias', function (Blueprint $table) {
                $table->dropForeign(['id_tipo']);
                $table->dropColumn('id_tipo');
            });
        });
    }
};
