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
        Schema::create('evidences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path')->nullable();
            $table->string('file')->nullable();
            $table->string('type')->nullable();
            $table->string('size')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('folder_id')->constrained('folders');
            $table->foreignId('evidenceType_id')->constrained('evidencias_tipo');
            $table->foreignId('standard_id')->constrained('estandars');
            $table->foreignId('id_date')->constrained('date_semesters');
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
        Schema::dropIfExists('evidences');
    }
};
