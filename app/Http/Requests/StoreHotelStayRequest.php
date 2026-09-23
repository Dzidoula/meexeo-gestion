<?php
namespace App\Http\Requests;

use App\Enums\HotelStayStatus;
use App\Models\HotelStay;
use Illuminate\Foundation\Http\FormRequest;

class StoreHotelStayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotel_room_id' => [
                'required',
                'exists:hotel_rooms,id',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $overlaps = HotelStay::query()
                        ->where('hotel_room_id', $value)
                        ->whereIn('status', [HotelStayStatus::Reserved->value, HotelStayStatus::InProgress->value])
                        ->whereDate('arrival_date', '<', $this->input('departure_date'))
                        ->whereDate('departure_date', '>', $this->input('arrival_date'))
                        ->exists();

                    if ($overlaps) {
                        $fail('Cette chambre est déjà réservée sur cette période.');
                    }
                },
            ],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:30'],
            'arrival_date' => ['required', 'date'],
            'departure_date' => ['required', 'date', 'after:arrival_date'],
            'total_amount' => ['required', 'integer', 'min:0'],
            'deposit_amount' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'hotel_room_id.required' => 'Choisissez une chambre.',
            'hotel_room_id.exists' => "Cette chambre n'existe pas.",
            'guest_name.required' => 'Indiquez le nom du client.',
            'guest_phone.required' => 'Indiquez le téléphone du client.',
            'departure_date.after' => "La date de départ doit être après la date d'arrivée.",
            'total_amount.integer' => 'Le montant doit être un entier en francs CFA.',
            'total_amount.min' => 'Le montant ne peut pas être négatif.',
            'deposit_amount.min' => "L'acompte ne peut pas être négatif.",
        ];
    }
}
