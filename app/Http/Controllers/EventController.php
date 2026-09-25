<?php
namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->filled('status') ? $request->query('status') : null;

        $events = Event::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('start_date')
            ->paginate(25)
            ->withQueryString();

        return view('events.index', ['events' => $events]);
    }

    public function create(): View
    {
        return view('events.form', ['event' => new Event()]);
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $event = Event::create($request->validated() + ['status' => EventStatus::Pending]);

        return redirect()->route('events.show', $event)->with('status', "L'événement a été créé.");
    }

    public function show(Event $event): View
    {
        return view('events.show', ['event' => $event]);
    }

    public function edit(Event $event): View|RedirectResponse
    {
        if (in_array($event->status, [EventStatus::Completed, EventStatus::Cancelled], true)) {
            return redirect()->route('events.show', $event)->with('error', 'Cet événement est déjà terminé ou annulé, il ne peut plus être modifié.');
        }

        return view('events.form', ['event' => $event]);
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        if (in_array($event->status, [EventStatus::Completed, EventStatus::Cancelled], true)) {
            return back()->with('error', 'Cet événement est déjà terminé ou annulé, il ne peut plus être modifié.');
        }

        $event->update($request->validated());

        return redirect()->route('events.show', $event)->with('status', "L'événement a été mis à jour.");
    }

    public function confirm(Event $event): RedirectResponse
    {
        if ($event->status !== EventStatus::Pending) {
            return back()->with('error', 'Seul un événement en attente peut être confirmé.');
        }

        $event->update(['status' => EventStatus::Confirmed]);

        return redirect()->route('events.show', $event)->with('status', 'Événement confirmé.');
    }

    public function complete(Event $event): RedirectResponse
    {
        if ($event->status !== EventStatus::Confirmed) {
            return back()->with('error', 'Seul un événement confirmé peut être marqué comme terminé.');
        }

        $event->update(['status' => EventStatus::Completed]);

        return redirect()->route('events.show', $event)->with('status', 'Événement marqué comme terminé.');
    }

    public function cancel(Event $event): RedirectResponse
    {
        if (in_array($event->status, [EventStatus::Completed, EventStatus::Cancelled], true)) {
            return back()->with('error', 'Cet événement est déjà terminé ou annulé.');
        }

        $event->update(['status' => EventStatus::Cancelled]);

        return redirect()->route('events.show', $event)->with('status', 'Événement annulé.');
    }
}
