<?php
namespace App\Http\Requests;

use App\Enums\EventStatus;
use App\Models\Equipment;
use App\Models\EventEquipmentReservation;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventEquipmentReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'equipment_id' => [
                'required',
                'exists:equipment,id',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $equipment = Equipment::find($value);
                    $event = $this->route('event');

                    if (! $equipment || ! $event) {
                        return;
                    }

                    $quantity = (int) $this->input('quantity');

                    // Chevauchement INCLUSIF sur les deux bornes : un
                    // équipement est occupé toute une journée calendaire,
                    // contrairement à une chambre d'hôtel qui se libère
                    // le jour du départ. Ne pas copier les opérateurs
                    // stricts (<, >) utilisés pour les séjours hôtel.
                    $alreadyReserved = (int) EventEquipmentReservation::query()
                        ->where('equipment_id', $value)
                        ->whereHas('event', function ($query) use ($event) {
                            $query->whereIn('status', [EventStatus::Pending->value, EventStatus::Confirmed->value])
                                ->whereDate('start_date', '<=', $event->end_date)
                                ->whereDate('end_date', '>=', $event->start_date);
                        })
                        ->sum('quantity');

                    if ($alreadyReserved + $quantity > $equipment->quantity_total) {
                        $available = max(0, $equipment->quantity_total - $alreadyReserved);
                        $fail("Il ne reste que {$available} unité(s) disponible(s) de « {$equipment->name} » sur cette période.");
                    }
                },
            ],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'equipment_id.required' => 'Choisissez un équipement.',
            'equipment_id.exists' => "Cet équipement n'existe pas.",
            'quantity.required' => 'Indiquez la quantité à réserver.',
            'quantity.min' => 'La quantité doit être d\'au moins 1.',
        ];
    }
}
