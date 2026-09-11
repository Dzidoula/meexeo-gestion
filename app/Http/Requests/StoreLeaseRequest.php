<?php
// app/Http/Requests/StoreLeaseRequest.php
namespace App\Http\Requests;

use App\Models\Property;
use App\Services\LeaseService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreLeaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Rôle filtré par le middleware de la route.
    }

    public function rules(): array
    {
        return [
            'property_id' => ['required', 'exists:properties,id'],
            'tenant_id' => ['required', 'exists:tenants,id'],
            'start_date' => ['required', 'date'],
            'expected_end_date' => ['nullable', 'date', 'after:start_date'],
            'monthly_rent' => ['required', 'integer', 'min:0'],
            'deposit_paid' => ['required', 'integer', 'min:0'],
            'due_day' => ['required', 'integer', 'min:1', 'max:31'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (! $this->filled('property_id')) {
                    return;
                }

                $property = Property::find($this->input('property_id'));

                if ($property && app(LeaseService::class)->hasActiveLease($property)) {
                    $validator->errors()->add(
                        'property_id',
                        'Ce bien est déjà occupé par un bail en cours. Terminez ce bail avant de réaffecter le bien.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'expected_end_date.after' => 'La date de fin prévue doit suivre la date de début.',
            'due_day.max' => "Le jour d'échéance doit être compris entre 1 et 31.",
        ];
    }
}
