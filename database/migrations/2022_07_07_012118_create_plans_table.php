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

    //hacer null todo menos codigo, user, estandar


    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 11);
            $table->string('nombre', 255)->nullable();
            $table->string('oportunidad_plan')->nullable();
            $table->string('semestre_ejecucion', 8)->nullable();
            $table->integer('avance');
            $table->integer('duracion')->nullable();
            $table->string('estado', 30);
            $table->boolean('evaluacion_eficacia')->nullable();;
            $table->foreignId('id_estandar')
                ->constrained('estandars');
            $table->foreignId('id_user')
                ->constrained('users');
            $table->unique(['codigo', 'id_estandar']);
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
        Schema::dropIfExists('plans');
    }
};
