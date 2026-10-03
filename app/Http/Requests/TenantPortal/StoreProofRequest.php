<?php

namespace App\Http\Requests\TenantPortal;

use Illuminate\Foundation\Http\FormRequest;

class StoreProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('tenant')->check();
    }

    public function rules(): array
    {
        return [
            'month'  => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'amount' => ['required', 'integer', 'min:1'],
            'proof'  => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'proof.mimes' => 'Le fichier doit être une image (JPEG, PNG, WebP, GIF) ou un PDF.',
            'proof.max'   => 'Le fichier ne doit pas dépasser 5 Mo.',
            'month.regex' => 'Mois invalide.',
        ];
    }
}
