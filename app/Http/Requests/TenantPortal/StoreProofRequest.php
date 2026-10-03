<?php

namespace App\Http\Requests\TenantPortal;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
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
            'month'  => ['required', 'string', 'date_format:Y-m'],
            'amount' => ['required', 'integer', 'min:1', 'max:100000000'],
            'proof'  => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf', 'max:5120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->has('month')) {
                return;
            }

            $lease = auth()->guard('tenant')->user()?->activeLease;

            if (! $lease) {
                return;
            }

            $month = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
            $first = Carbon::parse($lease->start_date)->startOfMonth();

            if ($month->lt($first)) {
                $validator->errors()->add('month', 'Ce mois précède le début de votre bail.');
            }

            if ($month->gt(now()->startOfMonth())) {
                $validator->errors()->add('month', 'Vous ne pouvez pas envoyer une preuve pour un mois à venir.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'proof.mimes'       => 'Le fichier doit être une image (JPEG, PNG, WebP, GIF) ou un PDF.',
            'proof.max'         => 'Le fichier ne doit pas dépasser 5 Mo.',
            'month.date_format' => 'Mois invalide.',
        ];
    }
}
