<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use App\Support\RentDueDate;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tenant   = auth()->guard('tenant')->user();
        $lease    = $tenant->activeLease()->with('property.primaryPhoto')->firstOrFail();
        $property = $lease->property;

        $thisMonth = $lease->payments()
            ->whereYear('month', now()->year)
            ->whereMonth('month', now()->month)
            ->get();

        $paidThisMonth  = (int) $thisMonth->whereNull('portal_status')->sum('amount');
        $hasPendingProof = $thisMonth->where('portal_status', 'pending')->isNotEmpty();

        $nextDue = RentDueDate::next($lease->due_day);

        $rentStatus = match (true) {
            $paidThisMonth >= $lease->monthly_rent => 'paid',
            $paidThisMonth > 0                     => 'partial',
            $hasPendingProof                       => 'pending',
            now()->gt(RentDueDate::forMonth(now(), $lease->due_day)) => 'late',
            default                                => 'upcoming',
        };

        return view('tenant-portal.dashboard', compact(
            'tenant', 'lease', 'property', 'rentStatus', 'nextDue'
        ));
    }
}
