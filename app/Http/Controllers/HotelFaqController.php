<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelFaqRequest;
use App\Http\Requests\UpdateHotelFaqRequest;
use App\Models\HotelFaq;
use App\Services\TouvalemSyncPusher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HotelFaqController extends Controller
{
    public function index(): View
    {
        return view('hotel-faqs.index', [
            'faqs' => HotelFaq::orderBy('order')->get(),
        ]);
    }

    public function create(): View
    {
        return view('hotel-faqs.form', ['hotelFaq' => new HotelFaq()]);
    }

    public function store(StoreHotelFaqRequest $request, TouvalemSyncPusher $pusher): RedirectResponse
    {
        $faq = HotelFaq::create($request->validated());
        $pusher->push('faqs', $faq, $request->validated());

        return redirect()->route('hotel-faqs.index')->with('status', 'FAQ créée.');
    }

    public function edit(HotelFaq $hotelFaq): View
    {
        return view('hotel-faqs.form', ['hotelFaq' => $hotelFaq]);
    }

    public function update(UpdateHotelFaqRequest $request, HotelFaq $hotelFaq, TouvalemSyncPusher $pusher): RedirectResponse
    {
        $hotelFaq->update($request->validated());
        $pusher->push('faqs', $hotelFaq, $request->validated());

        return redirect()->route('hotel-faqs.index')->with('status', 'FAQ mise à jour.');
    }

    public function destroy(HotelFaq $hotelFaq, TouvalemSyncPusher $pusher): RedirectResponse
    {
        $pusher->pushDelete('faqs', $hotelFaq);
        $hotelFaq->delete();

        return redirect()->route('hotel-faqs.index')->with('status', 'FAQ supprimée.');
    }
}
