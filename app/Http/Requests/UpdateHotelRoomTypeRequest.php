<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHotelRoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('hotel_room_types', 'name')->ignore($this->route('hotelRoomType'))],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Donnez un nom au type de chambre.',
            'name.unique' => 'Ce type de chambre existe déjà.',
        ];
    }
}
