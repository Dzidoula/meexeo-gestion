<?php

namespace App\Http\Requests\TenantPortal;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('tenant')->check();
    }

    public function rules(): array
    {
        return [
            'email'      => ['nullable', 'email', 'max:255'],
            'phone2'     => ['nullable', 'string', 'max:30'],
            'occupation' => ['nullable', 'string', 'max:255'],
        ];
    }
}
