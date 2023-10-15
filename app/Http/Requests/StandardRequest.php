<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StandardRequest extends CustomFormRequest
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
    

    public function validationForChangeStandardAssignment(){
        return [
            'users' => 'present|required|array',
            'users.*' => 'integer|exists:users,id'
        ];
    }

    public function validationForUpdateStandardHeader(){
        return [
            'description' => 'present|required|string',
            'factor' => 'present|required|string',
            'dimension' => 'present|required|string',
            'related_standards' => 'present|required|string'
        ];
    }
    

}
