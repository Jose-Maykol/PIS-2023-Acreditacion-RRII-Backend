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
            $table->string('name', 255);
            $table->string('path')->nullable();
            $table->string('file')->nullable();
            $table->string('type')->nullable();
            $table->string('size')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('plan_id')->nullable()->constrained('plans');
            $table->foreignId('folder_id')->constrained('folders');
            $table->foreignId('evidence_type_id')->constrained('evidence_types');
            $table->foreignId('standard_id')->constrained('standards');
            $table->foreignId('date_id')->constrained('date_semesters');
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
