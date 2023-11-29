<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FacultyStaffRequest extends CustomFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     * public function authorize() {return true;}
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     * public function rules() {}
     */

    /*
    public function validationFor{method}(){
        return [
           
        ];
    }
*/
    public function validationForCreateFacultyStaff()
    {
        return [
            'number_extraordinary_professor' => 'present|required|numeric|min:0',
            'number_ordinary_professor_main' => 'present|required|numeric|min:0',
            'number_ordinary_professor_associate' => 'present|required|numeric|min:0',
            'number_ordinary_professor_assistant' => 'present|required|numeric|min:0',
            'number_contractor_professor' => 'present|required|numeric|min:0',
            'ordinary_professor_exclusive_dedication' => 'present|required|numeric|min:0',
            'ordinary_professor_fulltime' => 'present|required|numeric|min:0',
            'ordinary_professor_parttime' => 'present|required|numeric|min:0',
            'contractor_professor_fulltime' => 'present|required|numeric|min:0',
            'contractor_professor_parttime' => 'present|required|numeric|min:0',

            'distinguished_researcher' => 'present|required|numeric|min:0',
            'researcher_level_i' => 'present|required|numeric|min:0',
            'researcher_level_ii' => 'present|required|numeric|min:0',
            'researcher_level_iii' => 'present|required|numeric|min:0',
            'researcher_level_iv' => 'present|required|numeric|min:0',
            'researcher_level_v' => 'present|required|numeric|min:0',
            'researcher_level_vi' => 'present|required|numeric|min:0',
            'researcher_level_vii' => 'present|required|numeric|min:0',

            'number_publications_indexed' => 'present|required|numeric|min:0',
            'intellectual_property_indecopi' => 'present|required|numeric|min:0',
            'number_research_project_inexecution' => 'present|required|numeric|min:0',
            'number_research_project_completed' => 'present|required|numeric|min:0',
            'number_professor_inperson_academic_movility' => 'present|required|numeric|min:0',
            'number_professor_virtual_academic_movility' => 'present|required|numeric|min:0',

            'number_vacancies' => 'present|required|numeric|min:0',
            'number_applicants' => 'present|required|numeric|min:0',
            'number_admitted_candidates' => 'present|required|numeric|min:0',
            'number_enrolled_students' => 'present|required|numeric|min:0',
            'number_graduates' => 'present|required|numeric|min:0',
            'number_alumni' => 'present|required|numeric|min:0',
            'number_degree_recipients' => 'present|required|numeric|min:0'
        ];
    }
    public function validationForUpdateFacultyStaff()
    {
        return [
            'number_extraordinary_professor' => 'present|required|numeric|min:0',
            'number_ordinary_professor_main' => 'present|required|numeric|min:0',
            'number_ordinary_professor_associate' => 'present|required|numeric|min:0',
            'number_ordinary_professor_assistant' => 'present|required|numeric|min:0',
            'number_contractor_professor' => 'present|required|numeric|min:0',
            'ordinary_professor_exclusive_dedication' => 'present|required|numeric|min:0',
            'ordinary_professor_fulltime' => 'present|required|numeric|min:0',
            'ordinary_professor_parttime' => 'present|required|numeric|min:0',
            'contractor_professor_fulltime' => 'present|required|numeric|min:0',
            'contractor_professor_parttime' => 'present|required|numeric|min:0',

            'distinguished_researcher' => 'present|required|numeric|min:0',
            'researcher_level_i' => 'present|required|numeric|min:0',
            'researcher_level_ii' => 'present|required|numeric|min:0',
            'researcher_level_iii' => 'present|required|numeric|min:0',
            'researcher_level_iv' => 'present|required|numeric|min:0',
            'researcher_level_v' => 'present|required|numeric|min:0',
            'researcher_level_vi' => 'present|required|numeric|min:0',
            'researcher_level_vii' => 'present|required|numeric|min:0',

            'number_publications_indexed' => 'present|required|numeric|min:0',
            'intellectual_property_indecopi' => 'present|required|numeric|min:0',
            'number_research_project_inexecution' => 'present|required|numeric|min:0',
            'number_research_project_completed' => 'present|required|numeric|min:0',
            'number_professor_inperson_academic_movility' => 'present|required|numeric|min:0',
            'number_professor_virtual_academic_movility' => 'present|required|numeric|min:0',

            'number_vacancies' => 'present|required|numeric|min:0',
            'number_applicants' => 'present|required|numeric|min:0',
            'number_admitted_candidates' => 'present|required|numeric|min:0',
            'number_enrolled_students' => 'present|required|numeric|min:0',
            'number_graduates' => 'present|required|numeric|min:0',
            'number_alumni' => 'present|required|numeric|min:0',
            'number_degree_recipients' => 'present|required|numeric|min:0'
        ];
    }
}
