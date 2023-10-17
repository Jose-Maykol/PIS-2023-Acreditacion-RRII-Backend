<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomFormRequest extends FormRequest
{
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json(['errors' => $validator->errors()], 400);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $methodName = $this->route()->getActionMethod();
        if (method_exists(StandardRequest::class, 'validationFor' . $methodName)) {
            return $this->{'validationFor' . $methodName}();
        }
        return [];
    }
}
