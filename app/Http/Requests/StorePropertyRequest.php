<?php
// app/Http/Requests/StorePropertyRequest.php
namespace App\Http\Requests;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Le filtrage par rôle est porté par le middleware de la route.
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(PropertyType::class)],
            'status' => ['required', Rule::enum(PropertyStatus::class)],
            'city' => ['required', 'string', 'max:255'],
            'commune' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'lot_number' => ['nullable', 'string', 'max:30'],
            'block_number' => ['nullable', 'string', 'max:30'],
            'rooms' => ['nullable', 'integer', 'min:0', 'max:200'],
            'area_sqm' => ['nullable', 'integer', 'min:0', 'max:100000'],
            // Le franc CFA n'a pas de centimes : entier obligatoire.
            'monthly_rent' => ['required', 'integer', 'min:0'],
            'deposit' => ['required', 'integer', 'min:0'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Donnez un nom au bien.',
            'city.required' => 'Indiquez la ville.',
            'type.required' => 'Choisissez le type de bien.',
            'monthly_rent.integer' => 'Le loyer doit être un montant entier en francs CFA.',
            'deposit.integer' => 'La caution doit être un montant entier en francs CFA.',
        ];
    }
}
