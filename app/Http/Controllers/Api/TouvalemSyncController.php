<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HotelContactMessage;
use App\Models\HotelFaq;
use App\Models\HotelGallery;
use App\Models\HotelNewsletterSubscriber;
use App\Models\HotelPromoCode;
use App\Models\HotelTestimonial;
use App\Services\TouvalemImportService;
use Illuminate\Http\Request;

/**
 * Receives a single booking row, in the exact shape
 * TouvalemImportService::importBookings() already expects, each time a real
 * booking is created on residencetouvalem.com — a live, incremental version
 * of the one-off touvalem:import-hotel-data command. Lets staff manage new
 * site bookings from this dashboard without touching the site's own booking
 * flow (promo codes, PDF invoices, "Mes réservations" all stay untouched).
 *
 * For the six content modules, a push carries EITHER `id` (the row
 * originated on Touvalem — handled via TouvalemImportService, keyed on
 * external_source=residence_touvalem) OR `masterclays_id` (the row
 * originated here — updated directly by its own primary key, since it's
 * already our row and re-importing it would create a duplicate).
 */
class TouvalemSyncController extends Controller
{
    public function bookings(Request $request, TouvalemImportService $service)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $service->importBookings([$request->all()]);

        return response()->json(['synced' => true], 201);
    }

    public function bookingStatus(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $stay = \App\Models\HotelStay::where('external_source', 'residence_touvalem')
            ->where('external_id', $request->input('touvalem_id'))
            ->first();

        if (!$stay) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        if ($request->input('status') === 'cancelled') {
            $stay->update(['confirmation_status' => 'refused', 'status' => \App\Enums\HotelStayStatus::Cancelled]);
        } else {
            $stay->update(['confirmation_status' => $request->input('status')]);
        }

        return response()->json(['synced' => true]);
    }

    public function galleries(Request $request, TouvalemImportService $service)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->filled('masterclays_id')) {
            HotelGallery::findOrFail($request->input('masterclays_id'))
                ->fill($request->only(['image_path', 'title', 'category']))
                ->save();
        } else {
            $service->importGalleries([$request->all()]);
        }

        return response()->json(['synced' => true], 201);
    }

    public function galleriesDestroy(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->filled('masterclays_id')) {
            HotelGallery::where('id', $request->input('masterclays_id'))->delete();
        } else {
            HotelGallery::where('external_source', 'residence_touvalem')
                ->where('external_id', $request->input('id'))
                ->delete();
        }

        return response()->json(['deleted' => true]);
    }

    public function testimonials(Request $request, TouvalemImportService $service)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->filled('masterclays_id')) {
            $testimonial = HotelTestimonial::findOrFail($request->input('masterclays_id'));
            $testimonial->fill($request->only(['author_name', 'author_subtitle', 'author_image', 'rating', 'title', 'content']));
            $testimonial->is_active = $request->boolean('is_active');
            $testimonial->save();
        } else {
            $service->importTestimonials([$request->all()]);
        }

        return response()->json(['synced' => true], 201);
    }

    public function testimonialsDestroy(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->filled('masterclays_id')) {
            HotelTestimonial::where('id', $request->input('masterclays_id'))->delete();
        } else {
            HotelTestimonial::where('external_source', 'residence_touvalem')
                ->where('external_id', $request->input('id'))
                ->delete();
        }

        return response()->json(['deleted' => true]);
    }

    public function faqs(Request $request, TouvalemImportService $service)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->filled('masterclays_id')) {
            $faq = HotelFaq::findOrFail($request->input('masterclays_id'));
            $faq->fill($request->only(['question', 'answer', 'order']));
            $faq->is_active = $request->boolean('is_active');
            $faq->save();
        } else {
            $service->importFaqs([$request->all()]);
        }

        return response()->json(['synced' => true], 201);
    }

    public function faqsDestroy(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->filled('masterclays_id')) {
            HotelFaq::where('id', $request->input('masterclays_id'))->delete();
        } else {
            HotelFaq::where('external_source', 'residence_touvalem')
                ->where('external_id', $request->input('id'))
                ->delete();
        }

        return response()->json(['deleted' => true]);
    }

    public function contactMessages(Request $request, TouvalemImportService $service)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->filled('masterclays_id')) {
            $message = HotelContactMessage::findOrFail($request->input('masterclays_id'));
            $message->fill($request->only(['first_name', 'last_name', 'email', 'phone', 'subject', 'message']));
            $message->is_read = $request->boolean('is_read');
            $message->save();
        } else {
            $service->importContactMessages([$request->all()]);
        }

        return response()->json(['synced' => true], 201);
    }

    public function contactMessagesDestroy(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->filled('masterclays_id')) {
            HotelContactMessage::where('id', $request->input('masterclays_id'))->delete();
        } else {
            HotelContactMessage::where('external_source', 'residence_touvalem')
                ->where('external_id', $request->input('id'))
                ->delete();
        }

        return response()->json(['deleted' => true]);
    }

    public function newsletterSubscribers(Request $request, TouvalemImportService $service)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->filled('masterclays_id')) {
            HotelNewsletterSubscriber::findOrFail($request->input('masterclays_id'))
                ->fill($request->only(['email']))
                ->save();
        } else {
            $service->importNewsletterSubscribers([$request->all()]);
        }

        return response()->json(['synced' => true], 201);
    }

    public function newsletterSubscribersDestroy(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->filled('masterclays_id')) {
            HotelNewsletterSubscriber::where('id', $request->input('masterclays_id'))->delete();
        } else {
            HotelNewsletterSubscriber::where('external_source', 'residence_touvalem')
                ->where('external_id', $request->input('id'))
                ->delete();
        }

        return response()->json(['deleted' => true]);
    }

    public function promoCodes(Request $request, TouvalemImportService $service)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->filled('masterclays_id')) {
            $promoCode = HotelPromoCode::findOrFail($request->input('masterclays_id'));
            $promoCode->fill($request->only(['code', 'discount_type', 'discount_value', 'min_total', 'max_uses', 'starts_at', 'expires_at']));
            $promoCode->is_active = $request->boolean('is_active');
            $promoCode->save();
        } else {
            $service->importPromoCodes([$request->all()]);
        }

        return response()->json(['synced' => true], 201);
    }

    public function promoCodesDestroy(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($request->filled('masterclays_id')) {
            HotelPromoCode::where('id', $request->input('masterclays_id'))->delete();
        } else {
            HotelPromoCode::where('external_source', 'residence_touvalem')
                ->where('external_id', $request->input('id'))
                ->delete();
        }

        return response()->json(['deleted' => true]);
    }

    private function authorized(Request $request): bool
    {
        $configuredToken = config('services.touvalem_sync.token');

        return $configuredToken && $request->header('X-Sync-Token') === $configuredToken;
    }
}
