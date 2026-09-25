<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('equipment', 'name')->ignore($this->route('equipment'))],
            'quantity_total' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Donnez un nom à cet équipement.',
            'name.unique' => 'Cet équipement existe déjà.',
            'quantity_total.min' => 'La quantité ne peut pas être négative.',
        ];
    }
}
