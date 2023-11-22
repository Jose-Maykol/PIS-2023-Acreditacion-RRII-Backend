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
        Schema::create('faculty_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('date_id')
                ->constrained('date_semesters');
            $table->unsignedSmallInteger('number_extraordinary_professor');
            $table->unsignedSmallInteger('number_contractor_professor');
            $table->unsignedSmallInteger('number_ordinary_professor_main');
            $table->unsignedSmallInteger('number_ordinary_professor_assistant');
            $table->unsignedSmallInteger('ordinary_professor_exclusive_dedication');
            $table->unsignedSmallInteger('ordinary_professor_fulltime');
            $table->unsignedSmallInteger('ordinary_professor_parttime');
            $table->unsignedSmallInteger('contractor_professor_fulltime');
            $table->unsignedSmallInteger('contractor_professor_parttime');
            //
            $table->unsignedSmallInteger('distinguished_researcher');
            $table->unsignedSmallInteger('researcher_level_i');
            $table->unsignedSmallInteger('researcher_level_ii');
            $table->unsignedSmallInteger('researcher_level_iii');
            $table->unsignedSmallInteger('researcher_level_iv');
            $table->unsignedSmallInteger('researcher_level_v');
            $table->unsignedSmallInteger('researcher_level_vi');
            $table->unsignedSmallInteger('researcher_level_vii');
            //
            $table->unsignedSmallInteger('number_publications_indexed');
            $table->unsignedSmallInteger('intellectual_property_indecopi');
            $table->unsignedSmallInteger('number_research_project_inexecution');
            $table->unsignedSmallInteger('number_research_project_completed');
            $table->unsignedSmallInteger('number_professor_inperson_academic_movility');
            $table->unsignedSmallInteger('number_professor_virtual_academic_movility');
            //
            $table->unsignedSmallInteger('number_vacancies');
            $table->unsignedSmallInteger('number_applicants');
            $table->unsignedSmallInteger('number_admitted_candidates');
            $table->unsignedSmallInteger('number_enrolled_students');
            $table->unsignedSmallInteger('number_graduates');
            $table->unsignedSmallInteger('number_alumni');
            $table->unsignedSmallInteger('number_degree_recipients');
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
        Schema::dropIfExists('faculty_staff');
    }
};
