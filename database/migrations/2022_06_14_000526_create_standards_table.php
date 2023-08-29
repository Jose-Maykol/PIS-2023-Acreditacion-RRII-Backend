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
        Schema::create('standards', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //cambiar el name por nombre
			$table->json('headboard');
            $table->unsignedInteger('nro_standard');
            $table->timestamps();
            //$table->foreign('id_user')->references('id')->on('users');
            $table->foreignId('id_user')
                  ->constrained('users');
            $table->foreignId('id_date')
                ->constrained('date_semesters');
            $table->foreignId('id_registration_status')
                ->constrained('registration_status');
        });
    }


    public function down()
    {
        Schema::dropIfExists('standards');
    }
};
