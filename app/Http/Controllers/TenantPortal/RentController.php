<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use App\Support\RentDueDate;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class RentController extends Controller
{
    public function index(): View
    {
        $tenant = auth()->guard('tenant')->user();
        $lease  = $tenant->activeLease()->with('payments')->firstOrFail();

        $cursor  = Carbon::parse($lease->start_date)->startOfMonth();
        $current = now()->startOfMonth();
        $months  = [];

        // A month may hold several payments (instalments), so group rather than index.
        $byMonth = $lease->payments->groupBy(fn ($p) => $p->month->format('Y-m'));

        while ($cursor->lte($current)) {
            $key      = $cursor->format('Y-m');
            $payments = $byMonth->get($key, new Collection);

            $verified   = $payments->whereNull('portal_status');
            $hasPending = $payments->where('portal_status', 'pending')->isNotEmpty();
            $paid       = (int) $verified->sum('amount');

            $months[] = [
                'key'      => $key,
                'label'    => ucfirst($cursor->isoFormat('MMMM YYYY')),
                'amount'   => $lease->monthly_rent,
                'paid'     => $paid,
                'rest'     => max(0, $lease->monthly_rent - $paid),
                'payments' => $verified->values(),
                'status'   => $this->computeStatus($cursor, $lease, $paid, $hasPending),
            ];

            $cursor->addMonth();
        }

        return view('tenant-portal.rents.index', [
            'tenant' => $tenant,
            'lease'  => $lease,
            'months' => array_reverse($months),
        ]);
    }

    public function notice(): View
    {
        $tenant = auth()->guard('tenant')->user();
        $lease  = $tenant->activeLease()->firstOrFail();

        $nextDue = RentDueDate::next($lease->due_day);

        return view('tenant-portal.rents.notice', compact('tenant', 'lease', 'nextDue'));
    }

    private function computeStatus(Carbon $month, $lease, int $paid, bool $hasPending): string
    {
        if ($paid >= $lease->monthly_rent) {
            return 'paid';
        }

        if ($paid > 0) {
            return 'partial';
        }

        // An unverified upload is not a payment: it never reads as paid.
        if ($hasPending) {
            return 'pending';
        }

        return now()->gt(RentDueDate::forMonth($month, $lease->due_day)) ? 'late' : 'upcoming';
    }
}
