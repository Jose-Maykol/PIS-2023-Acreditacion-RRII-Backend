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
        Schema::create('identification_context', function (Blueprint $table) {
            $table->id();
            $table->foreignId('date_id')
                ->constrained('date_semesters');
            $table->string('name_institution');
            $table->string('address_headquarters');
            $table->json('region_province_district');
            $table->string('institutional_telephone');
            $table->string('web_page');
            $table->string('resolution_authorizes_institution');
            $table->date('date_resolution');
            $table->string('highest_authority_institution');
            $table->string('highest_authority_institution_email');
            $table->string('highest_authority_institution_telephone');
            //
            $table->string('resolution_authorizing_offering_program');
            $table->string('academic_level');
            $table->unsignedInteger('cui');
            $table->string('grade_denomination');
            $table->string('title_denomination');
            $table->string('authorized_offer');
            $table->string('highest_authority_study_program');
            $table->string('highest_authority_study_program_email');
            $table->string('highest_authority_study_program_telephone');
            
            $table->json('members_quality_committee');
            $table->json('interest_groups_study_program');
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
        Schema::dropIfExists('identification_context');
    }
};
