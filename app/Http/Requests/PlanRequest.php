<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends CustomFormRequest
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
    public function validationForCreatePlan(){
        return [
            'code' => [
                'present','required','string',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^OM\d{2}-\d{2}-\d{4}$/', $value)) {
                        $fail('El formato del código no es válido. Debe ser OMxx-zz-yyyy');
                    }
                }
            ],
            "name" => "present|string|max:255",
            "opportunity_for_improvement" => "present|string|max:255",
            "semester_execution" => "present|string|max:8", //aaaa-A/B/C/AB
            "advance" => "present|integer",
            "duration" => "present|integer",
            "efficacy_evaluation" => "present|boolean",
            "standard_id" => "present|required|integer",
            "plan_status_id" => "present|required|integer",
            "sources" => "present|array|min:1",
            "sources.*.description" => "required|string|min:1",
            "problems_opportunities" => "present|array|min:1",
            "problems_opportunities.*.description" => "required|string|min:1",
            "root_causes" => "present|array|min:1",
            "root_causes.*.description" => "required|string|min:1",
            "improvement_actions" => "present|array|min:1",
            "improvement_actions.*.description" => "required|string|min:1",
            "resources" => "present|array|min:1",
            "resources.*.description" => "required|string|min:1",
            "goals" => "present|array|min:1",
            "goals.*.description" => "required|string|min:1",
            "responsibles" => "present|array|min:1",
            "responsibles.*.description" => "required|string|min:1",
            "observations" => "present|array|min:1",
            "observations.*.description" => "required|string|min:1"
        ];
    }

    public function validationForUpdatePlan(){
        return [
            'code' => [
                'present','required','string',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^OM\d{2}-\d{2}-\d{4}$/', $value)) {
                        $fail('El formato del código no es válido. Debe ser OMxx-zz-yyyy');
                    }
                }
            ],
            "name" => "present|string|max:255",
            "opportunity_for_improvement" => "present|string|max:255",
            "semester_execution" => "present|string|max:8", //aaaa-A/B/C/AB
            "advance" => "present|integer",
            "duration" => "present|integer",
            "efficacy_evaluation" => "present|boolean",
            "standard_id" => "present|required|integer",
            "plan_status_id" => "present|required|integer",
            "sources" => "present|array|min:1",
            "sources.*.description" => "required|string|min:1",
            "problems_opportunities" => "present|array|min:1",
            "problems_opportunities.*.description" => "required|string|min:1",
            "root_causes" => "present|array|min:1",
            "root_causes.*.description" => "required|string|min:1",
            "improvement_actions" => "present|array|min:1",
            "improvement_actions.*.description" => "required|string|min:1",
            "resources" => "present|array|min:1",
            "resources.*.description" => "required|string|min:1",
            "goals" => "present|array|min:1",
            "goals.*.description" => "required|string|min:1",
            "responsibles" => "present|array|min:1",
            "responsibles.*.description" => "required|string|min:1",
            "observations" => "present|array|min:1",
            "observations.*.description" => "required|string|min:1"
        ];
    }

    public function validationForListPlan(){
        return [
            'standard_id' => "present|required|numeric"
        ];
    }
}
