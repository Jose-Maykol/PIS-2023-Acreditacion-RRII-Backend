<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacultyStaffModel extends Model
{
    use HasFactory;

    protected $table ='faculty_staff';
    protected $fillable = [
        'date_id',
        'number_extraordinary_professor',
        'number_contractor_professor',
        'number_ordinary_professor_main',
        'number_ordinary_professor_assistant',
        'ordinary_professor_exclusive_dedication',
        'ordinary_professor_fulltime',
		'ordinary_professor_parttime',
        'contractor_professor_fulltime',
        'contractor_professor_parttime',
        //
        'distinguished_researcher',
        'researcher_level_i',
        'researcher_level_ii',
        'researcher_level_iii',
        'researcher_level_iv',
        'researcher_level_v',
        'researcher_level_vi',
        'researcher_level_vii',
        //
        'number_publications_indexed',
        'intellectual_property_indecopi',
        'number_research_project_inexecution',
        'number_research_project_completed',
        'number_professor_inperson_academic_movility',
        'number_professor_virtual_academic_movility',
        //
        'number_vacancies',
        'number_applicants',
        'number_admitted_candidates',
        'number_enrolled_students',
        'number_graduates',
        'number_alumni',
        'number_degree_recipients'
    ];
    
}
