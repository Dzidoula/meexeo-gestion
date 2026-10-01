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
        $configuredToken = config('services.touvalem_sync.token');

        if (!$configuredToken || $request->header('X-Sync-Token') !== $configuredToken) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $service->importBookings([$request->all()]);

        return response()->json(['synced' => true], 201);
    }
}
