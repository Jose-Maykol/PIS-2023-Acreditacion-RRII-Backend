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
        Schema::create('date_semesters', function (Blueprint $table) {
            $table->id();
            $table->year('year');// 1 - 2022 - A
            $table->char('semester', 1);
            $table->date('closing_date')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->foreignId('registration_status_id')
                ->constrained('registration_status');
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
        Schema::dropIfExists('date_semesters');
    }
};
