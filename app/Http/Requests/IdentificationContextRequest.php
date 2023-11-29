<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IdentificationContextRequest extends CustomFormRequest
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
    public function validationForCreateIdentificationContext()
    {
        return [
            'name_institution' => 'present|required|string',
            'address_headquarters' => 'present|required|string',
            'region_province_district' => [
                'required',
                'json',
                'array',
                'size:3',
                Rule::in([
                    'region',
                    'province',
                    'district',
                ]),
            ],
            'institutional_telephone' => 'present|required|string',
            'web_page' => 'present|nullable|url',
            'resolution_authorizes_institution' => 'present|required|string',
            'date_resolution' => 'present|required|date|date_format:Y-m-d',
            'highest_authority_institution' => 'present|required|string',
            'highest_authority_institution_email' => 'present|required|email',
            'highest_authority_institution_telephone' => 'present|required|string',
            //
            'resolution_authorizing_offering_program' => 'present|required|string',
            'academic_level' => 'present|required|string',
            'cui' => 'present|required|integer|digits:8',
            'grade_denomination' => 'present|required|string',
            'title_denomination' => 'present|required|string',
            'authorized_offer' => 'present|required|string',
            'highest_authority_study_program' => 'present|required|string',
            'highest_authority_study_program_email' => 'present|required|email',
            'highest_authority_study_program_telephone' => 'required|string',

            'members_quality_committee' => 'present|required|array',
            'members_quality_committee.*' => 'present|required|json',
            'members_quality_committee.*.name' => 'present|required|string',
            'members_quality_committee.*.lastname' => 'present|required|string',
            'members_quality_committee.*.position' => 'present|required|string',
            'members_quality_committee.*.email' => 'present|required|email',
            'members_quality_committee.*.telephone' => 'present|required|string',

            'interest_groups_study_program' => 'present|required|array',
            'interest_groups_study_program.*' => 'present|required|json',
            'interest_groups_study_program.*.interested' => 'present|required|string',
            'interest_groups_study_program.*.main_requirement_study_program' => 'present|required|string',
            'interest_groups_study_program.*.type' => 'present|required|string',

        ];
    }
    public function validationForUpdateIdentificationContext()
    {
        return [
            'name_institution' => 'present|required|string',
            'address_headquarters' => 'present|required|string',
            'region_province_district' => [
                'required',
                'json',
                'array',
                'size:3',
                Rule::in([
                    'region',
                    'province',
                    'district',
                ]),
            ],
            'institutional_telephone' => 'present|required|string',
            'web_page' => 'present|nullable|url',
            'resolution_authorizes_institution' => 'present|required|string',
            'date_resolution' => 'present|required|date|date_format:Y-m-d',
            'highest_authority_institution' => 'present|required|string',
            'highest_authority_institution_email' => 'present|required|email',
            'highest_authority_institution_telephone' => 'present|required|string',
            //
            'resolution_authorizing_offering_program' => 'present|required|string',
            'academic_level' => 'present|required|string',
            'cui' => 'present|required|integer|digits:8',
            'grade_denomination' => 'present|required|string',
            'title_denomination' => 'present|required|string',
            'authorized_offer' => 'present|required|string',
            'highest_authority_study_program' => 'present|required|string',
            'highest_authority_study_program_email' => 'present|required|email',
            'highest_authority_study_program_telephone' => 'required|string',

            'members_quality_committee' => 'present|required|array',
            'members_quality_committee.*' => 'present|required|json',
            'members_quality_committee.*.name' => 'present|required|string',
            'members_quality_committee.*.lastname' => 'present|required|string',
            'members_quality_committee.*.position' => 'present|required|string',
            'members_quality_committee.*.email' => 'present|required|email',
            'members_quality_committee.*.telephone' => 'present|required|string',

            'interest_groups_study_program' => 'present|required|array',
            'interest_groups_study_program.*' => 'present|required|json',
            'interest_groups_study_program.*.interested' => 'present|required|string',
            'interest_groups_study_program.*.main_requirement_study_program' => 'present|required|string',
            'interest_groups_study_program.*.type' => 'present|required|string',

        ];
    }
}
