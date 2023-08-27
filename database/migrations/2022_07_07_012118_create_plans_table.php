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
            $table->string('code', 11);
            $table->string('name', 255)->nullable();
            $table->string('opportunity_for_improvement')->nullable();
            $table->string('semester_execution', 8)->nullable();
            $table->integer('advance');
            $table->integer('duration')->nullable();
            $table->string('status', 30);
            $table->boolean('efficacy_evaluation')->nullable();;
            $table->foreignId('id_standard')
                ->constrained('standards');
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
