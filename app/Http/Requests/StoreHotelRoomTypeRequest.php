<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255', 'unique:hotel_room_types,name']];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Donnez un nom au type de chambre.',
            'name.unique' => 'Ce type de chambre existe déjà.',
        ];
    }
}
