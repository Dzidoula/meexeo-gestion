<?php

namespace App\Http\Requests;

use App\Enums\FuelType;
use App\Enums\Transmission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'fuel_type' => ['required', Rule::enum(FuelType::class)],
            'transmission' => ['required', Rule::enum(Transmission::class)],
            'seats' => ['required', 'integer', 'min:1', 'max:100'],
            'price' => ['required', 'integer', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_type_id.required' => 'Choisissez un type de véhicule.',
            'vehicle_type_id.exists' => 'Ce type de véhicule n\'existe pas.',
            'brand.required' => 'Indiquez la marque.',
            'model.required' => 'Indiquez le modèle.',
            'price.integer' => 'Le prix doit être un montant entier en francs CFA.',
            'price.min' => 'Le prix ne peut pas être négatif.',
        ];
    }
}
