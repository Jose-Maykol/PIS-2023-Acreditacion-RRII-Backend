<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DateSemesterRequest extends CustomFormRequest
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
    public function validationForCreateDateSemester()
    {
        return [
            'year' => "present|required|numeric|digits:4",
            'semester' => "present|required|string|size:1"
        ];
    }
    public function validationForUpdateDateSemester()
    {
        return [
            'id' => "present|required|numeric",
            'year' => "present|required|numeric|digits:4",
            'semester' => "present|required|string|size:1"
        ];
    }
    public function validationForCloseDateSemester()
    {
        return [
            'closing_date' => "present|required|date",
        ];
    }
}
