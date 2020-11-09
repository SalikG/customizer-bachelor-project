<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Register extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'companyName' => ['required', 'max:50'],
            'firstname' => ['required', 'max:50'],
            'lastname' => ['required', 'max:50'],
            'email' => ['required', 'max:100', 'unique:users,email'],
            'password' => ['required', 'max:30', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*?[0-9]).{8,}$/'],
        ];
    }
}
