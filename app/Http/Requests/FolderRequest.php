<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FolderRequest extends CustomFormRequest
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


    public function validationForCreateFolder()
    {
        return [
            'name' => 'present|required|string',
            'standard_id' => 'present|required|integer',
            'type_evidence_id' => 'present|required|integer',
            'plan_id' => 'integer',
            'folder_id' => 'nullable|integer',
            'path' => 'string',
            'is_evidence' => 'present|required|boolean'
        ];
    }
    public function validationForRenameFolder()
    {
        return [
            'new_name' => 'present|required|string',
        ];
    }

    public function validationForMoveFolder()
    {
        return [
            'parent_id' => 'present|required|integer',
        ];
    }

    public function validationForListFolder()
    {
        return [
            'folder_id' => 'nullable|integer',
            'standard_id' => 'present|required|integer',
            'evidence_type_id' => 'present|required|integer'
        ];
    }
}
