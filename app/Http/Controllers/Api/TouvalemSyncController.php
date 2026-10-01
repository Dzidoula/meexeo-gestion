<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TouvalemImportService;
use Illuminate\Http\Request;

/**
 * Receives a single booking row, in the exact shape
 * TouvalemImportService::importBookings() already expects, each time a real
 * booking is created on residencetouvalem.com — a live, incremental version
 * of the one-off touvalem:import-hotel-data command. Lets staff manage new
 * site bookings from this dashboard without touching the site's own booking
 * flow (promo codes, PDF invoices, "Mes réservations" all stay untouched).
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

    public function galleries(Request $request, TouvalemImportService $service)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $service->importGalleries([$request->all()]);

        return response()->json(['synced' => true], 201);
    }

    public function galleriesDestroy(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        \App\Models\HotelGallery::where('external_source', 'residence_touvalem')
            ->where('external_id', $request->input('id'))
            ->delete();

        return response()->json(['deleted' => true]);
    }

    public function testimonials(Request $request, TouvalemImportService $service)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $service->importTestimonials([$request->all()]);

        return response()->json(['synced' => true], 201);
    }

    public function testimonialsDestroy(Request $request)
    {
        if (!$this->authorized($request)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        \App\Models\HotelTestimonial::where('external_source', 'residence_touvalem')
            ->where('external_id', $request->input('id'))
            ->delete();

        return response()->json(['deleted' => true]);
    }

    private function authorized(Request $request): bool
    {
        $configuredToken = config('services.touvalem_sync.token');

        return $configuredToken && $request->header('X-Sync-Token') === $configuredToken;
    }
}
