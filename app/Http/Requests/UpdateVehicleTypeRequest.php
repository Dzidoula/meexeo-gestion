<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('vehicle_types', 'name')->ignore($this->route('vehicleType'))],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Donnez un nom au type de véhicule.',
            'name.unique' => 'Ce type de véhicule existe déjà.',
        ];
    }
}
