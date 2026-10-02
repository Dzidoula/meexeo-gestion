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
            'type' => ['nullable', 'in:chambre,privatisation'],
            'hotel_room_id' => [
                'required_if:type,chambre',
                'nullable',
                'exists:hotel_rooms,id',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || ! $this->filled(['arrival_date', 'departure_date'])) {
                        return;
                    }

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

    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator): void {
            if (! $this->filled(['arrival_date', 'departure_date'])) {
                return;
            }

            $overlapping = fn ($query) => $query
                ->whereIn('status', [HotelStayStatus::Reserved->value, HotelStayStatus::InProgress->value])
                ->whereDate('arrival_date', '<', $this->input('departure_date'))
                ->whereDate('departure_date', '>', $this->input('arrival_date'));

            if ($this->input('type', 'chambre') === 'privatisation') {
                $conflict = $overlapping(HotelStay::query()->where('type', 'chambre'))->exists();
                if ($conflict) {
                    $validator->errors()->add('arrival_date', "Impossible de privatiser la résidence : des chambres sont déjà réservées sur cette période.");
                }
            } else {
                $conflict = $overlapping(HotelStay::query()->where('type', 'privatisation'))->exists();
                if ($conflict) {
                    $validator->errors()->add('arrival_date', "Impossible de réserver cette chambre : la résidence entière est privatisée sur cette période.");
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'hotel_room_id.required_if' => 'Choisissez une chambre.',
            'hotel_room_id.exists' => "Cette chambre n'existe pas.",
            'guest_name.required' => 'Indiquez le nom du client.',
            'guest_phone.required' => 'Indiquez le téléphone du client.',
            'departure_date.after' => "La date de départ doit être après la date d'arrivée.",
            'total_amount.integer' => 'Le montant doit être un entier en francs CFA.',
            'total_amount.min' => 'Le montant ne peut pas être négatif.',
            'deposit_amount.min' => "L'acompte ne peut pas être négatif.",
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['type' => $this->input('type', 'chambre')]);
    }
}
