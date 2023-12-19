<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('faculty_staff')->insert([
            'date_id' => 1,
            'number_extraordinary_professor' => 4,
            'number_ordinary_professor_main' => 2,
            'number_ordinary_professor_associate' => 3,
            'number_ordinary_professor_assistant' => 1,
            'number_contractor_professor' => 0,
            'ordinary_professor_exclusive_dedication' => 2,
            'ordinary_professor_fulltime' => 4,
            'ordinary_professor_parttime' => 6,
            'contractor_professor_fulltime' => 2,
            'contractor_professor_parttime' => 4,
            //
            'distinguished_researcher' => 2,
            'researcher_level_i' => 1,
            'researcher_level_ii' => 1,
            'researcher_level_iii' => 2,
            'researcher_level_iv' => 1,
            'researcher_level_v' => 3,
            'researcher_level_vi' => 1,
            'researcher_level_vii' => 2,
            //
            'number_publications_indexed' => 50,
            'intellectual_property_indecopi' => 19,
            'number_research_project_inexecution' => 15,
            'number_research_project_completed' => 4,
            'number_professor_inperson_academic_movility' => 2,
            'number_professor_virtual_academic_movility' => 5,
            //
            'number_vacancies' => 155,
            'number_applicants' => 50,
            'number_admitted_candidates' => 12,
            'number_enrolled_students' => 95,
            'number_graduates' => 350,
            'number_alumni' => 43,
            'number_degree_recipients' => 12
        ]);
        DB::table('faculty_staff')->insert([
            'date_id' => 2,
            'number_extraordinary_professor' => 5,
            'number_ordinary_professor_main' => 3,
            'number_ordinary_professor_associate' => 4,
            'number_ordinary_professor_assistant' => 2,
            'number_contractor_professor' => 0,
            'ordinary_professor_exclusive_dedication' => 2,
            'ordinary_professor_fulltime' => 3,
            'ordinary_professor_parttime' => 4,
            'contractor_professor_fulltime' => 5,
            'contractor_professor_parttime' => 1,
            //
            'distinguished_researcher' => 1,
            'researcher_level_i' => 1,
            'researcher_level_ii' => 2,
            'researcher_level_iii' => 2,
            'researcher_level_iv' => 1,
            'researcher_level_v' => 0,
            'researcher_level_vi' => 1,
            'researcher_level_vii' => 2,
            //
            'number_publications_indexed' => 50,
            'intellectual_property_indecopi' => 20,
            'number_research_project_inexecution' => 48,
            'number_research_project_completed' => 4,
            'number_professor_inperson_academic_movility' => 2,
            'number_professor_virtual_academic_movility' => 5,
            //
            'number_vacancies' => 200,
            'number_applicants' => 120,
            'number_admitted_candidates' => 220,
            'number_enrolled_students' => 80,
            'number_graduates' => 50,
            'number_alumni' => 15,
            'number_degree_recipients' => 25
        ]);
        DB::table('faculty_staff')->insert([
            'date_id' => 3,
            'number_extraordinary_professor' => 5,
            'number_ordinary_professor_main' => 3,
            'number_ordinary_professor_associate' => 4,
            'number_ordinary_professor_assistant' => 2,
            'number_contractor_professor' => 0,
            'ordinary_professor_exclusive_dedication' => 2,
            'ordinary_professor_fulltime' => 3,
            'ordinary_professor_parttime' => 4,
            'contractor_professor_fulltime' => 5,
            'contractor_professor_parttime' => 1,
            //
            'distinguished_researcher' => 3,
            'researcher_level_i' => 1,
            'researcher_level_ii' => 2,
            'researcher_level_iii' => 2,
            'researcher_level_iv' => 1,
            'researcher_level_v' => 0,
            'researcher_level_vi' => 1,
            'researcher_level_vii' => 2,
            //
            'number_publications_indexed' => 50,
            'intellectual_property_indecopi' => 19,
            'number_research_project_inexecution' => 15,
            'number_research_project_completed' => 4,
            'number_professor_inperson_academic_movility' => 2,
            'number_professor_virtual_academic_movility' => 5,
            //
            'number_vacancies' => 255,
            'number_applicants' => 150,
            'number_admitted_candidates' => 120,
            'number_enrolled_students' => 100,
            'number_graduates' => 450,
            'number_alumni' => 45,
            'number_degree_recipients' => 15
        ]);
    }
}
