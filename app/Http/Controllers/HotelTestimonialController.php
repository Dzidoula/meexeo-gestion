<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelTestimonialRequest;
use App\Http\Requests\UpdateHotelTestimonialRequest;
use App\Models\HotelTestimonial;
use App\Services\TouvalemSyncPusher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HotelTestimonialController extends Controller
{
    public function index(): View
    {
        return view('hotel-testimonials.index', [
            'testimonials' => HotelTestimonial::latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('hotel-testimonials.form', ['hotelTestimonial' => new HotelTestimonial()]);
    }

    public function store(StoreHotelTestimonialRequest $request, TouvalemSyncPusher $pusher): RedirectResponse
    {
        $testimonial = HotelTestimonial::create($request->validated());
        $pusher->push('testimonials', $testimonial, $request->validated());

        return redirect()->route('hotel-testimonials.index')->with('status', 'Témoignage créé.');
    }

    public function edit(HotelTestimonial $hotelTestimonial): View
    {
        return view('hotel-testimonials.form', ['hotelTestimonial' => $hotelTestimonial]);
    }

    public function update(UpdateHotelTestimonialRequest $request, HotelTestimonial $hotelTestimonial, TouvalemSyncPusher $pusher): RedirectResponse
    {
        $hotelTestimonial->update($request->validated());
        $pusher->push('testimonials', $hotelTestimonial, $request->validated());

        return redirect()->route('hotel-testimonials.index')->with('status', 'Témoignage mis à jour.');
    }

    public function destroy(HotelTestimonial $hotelTestimonial, TouvalemSyncPusher $pusher): RedirectResponse
    {
        $pusher->pushDelete('testimonials', $hotelTestimonial);
        $hotelTestimonial->delete();

        return redirect()->route('hotel-testimonials.index')->with('status', 'Témoignage supprimé.');
    }
}
