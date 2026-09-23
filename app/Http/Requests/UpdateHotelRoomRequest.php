<?php
namespace App\Http\Requests;

use App\Enums\HotelRoomStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHotelRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotel_room_type_id' => ['required', 'exists:hotel_room_types,id'],
            'number' => ['required', 'string', 'max:50', Rule::unique('hotel_rooms', 'number')->ignore($this->route('room'))],
            'nightly_rate' => ['required', 'integer', 'min:0'],
            'amenities' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(HotelRoomStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'hotel_room_type_id.required' => 'Choisissez un type de chambre.',
            'hotel_room_type_id.exists' => "Ce type de chambre n'existe pas.",
            'number.required' => 'Indiquez le numéro de la chambre.',
            'number.unique' => 'Ce numéro de chambre existe déjà.',
            'nightly_rate.integer' => 'Le tarif doit être un montant entier en francs CFA.',
            'nightly_rate.min' => 'Le tarif ne peut pas être négatif.',
        ];
    }
}
