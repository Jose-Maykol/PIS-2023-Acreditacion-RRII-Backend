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
        Schema::create('estandars', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //cambiar el name por nombre
			$table->mediumText('cabecera');
            $table->timestamps();
            //$table->foreign('id_user')->references('id')->on('users');
            $table->foreignId('id_user')
                  ->constrained('users');
        });
    }


    public function down()
    {
        Schema::dropIfExists('estandars');
    }
};
