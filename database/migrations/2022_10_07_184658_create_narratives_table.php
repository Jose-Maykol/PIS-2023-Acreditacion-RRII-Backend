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
        Schema::create('narratives', function (Blueprint $table) {
            $table->id();
            $table->string('semester', 8);
            $table->mediumText('content');
            $table->foreignId('id_standard')
                ->constrained('standards');
            $table->foreignId('id_date')->constrained('date_semesters');
            $table->foreignId('id_registration_status')
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
        Schema::dropIfExists('narratives');
    }
};
