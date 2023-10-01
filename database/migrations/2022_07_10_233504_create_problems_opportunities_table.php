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
        Schema::create('problems_opportunities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('description');
            $table->foreignId('plan_id')
                  ->constrained('plans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('problems_opportunities');
    }
};
