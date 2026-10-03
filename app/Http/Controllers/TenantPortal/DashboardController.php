<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tenant   = auth()->guard('tenant')->user();
        $lease    = $tenant->activeLease()->with('property.photos')->firstOrFail();
        $property = $lease->property;

        $currentPayment = $lease->payments()
            ->whereYear('month', now()->year)
            ->whereMonth('month', now()->month)
            ->first();

        $nextDue = now()->day <= $lease->due_day
            ? now()->setDay($lease->due_day)
            : now()->addMonth()->setDay($lease->due_day);

        return view('tenant-portal.dashboard', compact(
            'tenant', 'lease', 'property', 'currentPayment', 'nextDue'
        ));
    }
}
