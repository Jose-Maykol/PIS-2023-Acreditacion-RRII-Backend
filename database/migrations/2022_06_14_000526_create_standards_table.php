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
            $table->string('name');
            $table->string('factor');
            $table->string('dimension');
            $table->string('related_standards');
            $table->unsignedInteger('nro_standard');
            $table->timestamps();
            $table->foreignId('date_id')
                ->constrained('date_semesters');
            $table->foreignId('registration_status_id')
                ->constrained('registration_status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('standards');
    }
};
