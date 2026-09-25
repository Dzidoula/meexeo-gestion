<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreEventPaymentRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;

class EventPaymentController extends Controller
{
    public function store(StoreEventPaymentRequest $request, Event $event): RedirectResponse
    {
        $event->payments()->create($request->validated());

        return redirect()->route('events.show', $event)->with('status', 'Le paiement a été enregistré.');
    }
}
