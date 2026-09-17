<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Renseignez votre nom complet.',
            'phone.required' => 'Renseignez votre numéro de téléphone.',
            'email.required' => 'Renseignez votre adresse email.',
            'email.email' => "Cette adresse email n'est pas valide.",
            'email.unique' => 'Un compte existe déjà avec cette adresse email.',
            'password.required' => 'Renseignez un mot de passe.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }
}
