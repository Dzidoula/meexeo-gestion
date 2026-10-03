<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use App\Models\RepairRequest;
use App\Support\RentSchedule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tenant = auth()->guard('tenant')->user();
        $lease  = $tenant->activeLease()->with(['property', 'payments'])->firstOrFail();

        $schedule = RentSchedule::forLease($lease);

        $repairs = RepairRequest::where('tenant_id', $tenant->id)
            ->latest()
            ->take(5)
            ->get();

        return view('tenant-portal.dashboard', [
            'tenant'      => $tenant,
            'lease'       => $lease,
            'property'    => $lease->property,
            'schedule'    => $schedule,
            'nextDue'     => $schedule->firstWhere(fn ($r) => in_array($r['status'], ['pending', 'overdue', 'partial'], true)),
            'paidCount'   => $schedule->where('status', 'paid')->count(),
            'openRepairs' => $repairs->whereIn('status', ['recu', 'en_cours'])->count(),
            'repairs'     => $repairs,
        ]);
    }
}
