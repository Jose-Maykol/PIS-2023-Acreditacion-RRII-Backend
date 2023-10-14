<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StandardRequest extends FormRequest
{
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

        if(method_exists($this, $methodName)){
            return $this->{'validationFor'.$methodName};
        }

        return [];
    }

    public function validationForRegister(){
        return [
            'role'=> 'required|string|in:administrador,docente',
            'email' => 'required|email'
        ];
    }
    

}
