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
            $table->string('code', 12);
            $table->string('name', 255)->nullable();
            $table->string('opportunity_for_improvement')->nullable();
            $table->string('semester_execution', 8)->nullable();
            $table->unsignedTinyInteger('advance');
            $table->unsignedTinyInteger('duration')->nullable();
            $table->boolean('efficacy_evaluation')->nullable();
            $table->foreignId('plan_status_id')
                ->constrained('plan_status');//
            $table->foreignId('standard_id')
                ->constrained('standards');
            $table->foreignId('user_id')
                ->constrained('users');
            $table->foreignId('date_id')
                ->constrained('date_semesters');
            //$table->unique(['code', 'standard_id']);
            $table->timestamps();
            $table->foreignId('registration_status_id')
                ->constrained('registration_status');
        }); //
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
