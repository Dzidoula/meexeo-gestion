<?php
namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Http\Requests\StoreEventEquipmentReservationRequest;
use App\Models\Event;
use App\Models\EventEquipmentReservation;
use Illuminate\Http\RedirectResponse;

class EventEquipmentReservationController extends Controller
{
    public function store(StoreEventEquipmentReservationRequest $request, Event $event): RedirectResponse
    {
        $event->reservations()->create($request->validated());

        return redirect()->route('events.show', $event)->with('status', "L'équipement a été réservé.");
    }

    public function destroy(Event $event, EventEquipmentReservation $reservation): RedirectResponse
    {
        if (in_array($event->status, [EventStatus::Completed, EventStatus::Cancelled], true)) {
            return back()->with('error', "Cet événement est déjà terminé ou annulé, sa liste d'équipement ne peut plus être modifiée.");
        }

        $reservation->delete();

        return redirect()->route('events.show', $event)->with('status', "La réservation d'équipement a été retirée.");
    }
}
