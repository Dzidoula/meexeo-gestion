<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:equipment,name'],
            'quantity_total' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Donnez un nom à cet équipement.',
            'name.unique' => 'Cet équipement existe déjà.',
            'quantity_total.required' => 'Indiquez la quantité totale disponible.',
            'quantity_total.min' => 'La quantité ne peut pas être négative.',
        ];
    }
}
