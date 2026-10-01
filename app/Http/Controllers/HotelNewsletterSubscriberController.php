<?php
namespace App\Http\Controllers;

use App\Models\HotelNewsletterSubscriber;
use App\Services\TouvalemSyncPusher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HotelNewsletterSubscriberController extends Controller
{
    public function index(): View
    {
        return view('hotel-newsletter-subscribers.index', [
            'subscribers' => HotelNewsletterSubscriber::latest()->paginate(20),
        ]);
    }

    public function destroy(HotelNewsletterSubscriber $hotelNewsletterSubscriber, TouvalemSyncPusher $pusher): RedirectResponse
    {
        $pusher->pushDelete('newsletter-subscribers', $hotelNewsletterSubscriber);
        $hotelNewsletterSubscriber->delete();

        return redirect()->route('hotel-newsletter-subscribers.index')->with('status', 'Abonné supprimé.');
    }
}
