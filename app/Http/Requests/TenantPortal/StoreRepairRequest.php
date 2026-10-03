<?php

namespace App\Http\Requests\TenantPortal;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepairRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('tenant')->check();
    }

    public function rules(): array
    {
        return [
            'type'        => ['required', 'in:plomberie,electricite,serrure,peinture,climatisation,autre'],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
            'urgency'     => ['required', 'in:faible,moyenne,urgente'],
            'photos.*'    => ['nullable', 'image', 'max:5120'],
            'video'       => ['nullable', 'mimetypes:video/mp4,video/quicktime', 'max:51200'],
        ];
    }
}
