<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255', 'unique:vehicle_types,name']];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Donnez un nom au type de véhicule.',
            'name.unique' => 'Ce type de véhicule existe déjà.',
        ];
    }
}
