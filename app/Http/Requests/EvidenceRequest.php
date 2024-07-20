<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvidenceRequest extends CustomFormRequest
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
    

    public function validationForCreateEvidences(){
        return [
            'standard_id' => 'present|required|integer',
            'type_evidence_id' => 'present|required|integer',
            'plan_id' => 'integer',
            'files' => 'present|required|array',
            'files.*' => 'file',
            'path' => 'string'
        ];
    }

    public function validationForCreateFileEvidence(){
        return [
            'standard_id' => 'present|required|integer',
            'type_evidence_id' => 'present|required|integer',
            'plan_id' => 'nullable|integer',
            'file' => 'present|required|file|max:20480',
            'path' => 'string',
            'folder_id' => 'nullable|integer'
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
    
    public function validationForRenameFile(){
        return [
            'new_filename' => 'present|required|string',
        ];
    }

}