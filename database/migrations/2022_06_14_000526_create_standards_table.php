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
            $table->string('name', 100);
            $table->string('description', 200);
            $table->string('factor', 100);
            $table->string('dimension', 100);
            $table->string('related_standards', 550);
            $table->unsignedTinyInteger('nro_standard',);
            $table->string('document_id', 100)->nullable();
            $table->mediumText('narrative')->nullable();
            $table->boolean('narrative_is_active')->default(false);
            $table->timestamps();
            $table->foreignId('date_id')
                ->constrained('date_semesters');
            $table->foreignId('standard_status_id')
                ->constrained('standard_status');
            $table->foreignId('registration_status_id')
                ->constrained('registration_status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('standards');
    }
};
