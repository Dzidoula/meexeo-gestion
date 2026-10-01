<?php
namespace App\Http\Controllers;

use App\Models\HotelContactMessage;
use App\Services\TouvalemSyncPusher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HotelContactMessageController extends Controller
{
    public function index(): View
    {
        return view('hotel-contact-messages.index', [
            'messages' => HotelContactMessage::latest()->paginate(15),
        ]);
    }

    public function show(HotelContactMessage $hotelContactMessage, TouvalemSyncPusher $pusher): View
    {
        if (!$hotelContactMessage->is_read) {
            $hotelContactMessage->update(['is_read' => true]);
            $pusher->push('contact-messages', $hotelContactMessage, [
                'first_name' => $hotelContactMessage->first_name,
                'last_name' => $hotelContactMessage->last_name,
                'email' => $hotelContactMessage->email,
                'phone' => $hotelContactMessage->phone,
                'subject' => $hotelContactMessage->subject,
                'message' => $hotelContactMessage->message,
                'is_read' => true,
            ]);
        }

        return view('hotel-contact-messages.show', ['message' => $hotelContactMessage]);
    }

    public function destroy(HotelContactMessage $hotelContactMessage, TouvalemSyncPusher $pusher): RedirectResponse
    {
        $pusher->pushDelete('contact-messages', $hotelContactMessage);
        $hotelContactMessage->delete();

        return redirect()->route('hotel-contact-messages.index')->with('status', 'Message supprimé.');
    }
}
