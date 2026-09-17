<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Renseignez votre adresse email.',
            'email.email' => "Cette adresse email n'est pas valide.",
            'password.required' => 'Renseignez votre mot de passe.',
        ];
    }
}
