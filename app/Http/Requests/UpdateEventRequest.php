<?php
namespace App\Http\Requests;

use App\Enums\EventStatus;
use App\Models\EventEquipmentReservation;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:30'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'venue' => ['required', 'string', 'max:255'],
            'budget_total' => ['required', 'integer', 'min:0'],
            'deposit_amount' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'Indiquez le nom du client.',
            'client_phone.required' => 'Indiquez le téléphone du client.',
            'end_date.after_or_equal' => 'La date de fin ne peut pas être avant la date de début.',
            'budget_total.min' => 'Le budget ne peut pas être négatif.',
            'deposit_amount.min' => "L'acompte ne peut pas être négatif.",
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $event = $this->route('event');

            if (! $event) {
                return;
            }

            $newStartDate = $this->input('start_date');
            $newEndDate = $this->input('end_date');

            if (! $newStartDate || ! $newEndDate) {
                return;
            }

            foreach ($event->reservations as $reservation) {
                $equipment = $reservation->equipment;

                if (! $equipment) {
                    continue;
                }

                // Chevauchement INCLUSIF sur les deux bornes, identique à
                // StoreEventEquipmentReservationRequest : un équipement est
                // occupé toute une journée calendaire, contrairement à une
                // chambre d'hôtel. Ne pas copier les opérateurs stricts
                // (<, >) utilisés pour les séjours hôtel.
                $otherEventsReserved = (int) EventEquipmentReservation::query()
                    ->where('equipment_id', $reservation->equipment_id)
                    ->where('event_id', '!=', $event->id)
                    ->whereHas('event', function ($query) use ($newStartDate, $newEndDate) {
                        $query->whereIn('status', [EventStatus::Pending->value, EventStatus::Confirmed->value])
                            ->whereDate('start_date', '<=', $newEndDate)
                            ->whereDate('end_date', '>=', $newStartDate);
                    })
                    ->sum('quantity');

                if ($otherEventsReserved + $reservation->quantity > $equipment->quantity_total) {
                    $validator->errors()->add(
                        'start_date',
                        "Cette modification de dates dépasserait la disponibilité de « {$equipment->name} » sur la nouvelle période."
                    );
                }
            }
        });
    }
}
