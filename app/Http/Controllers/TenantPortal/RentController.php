<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\View\View;

class RentController extends Controller
{
    public function index(): View
    {
        $tenant = auth()->guard('tenant')->user();
        $lease  = $tenant->activeLease()->with('payments')->firstOrFail();

        $start   = Carbon::parse($lease->start_date)->startOfMonth();
        $current = now()->startOfMonth();
        $months  = [];

        $paymentsIndexed = $lease->payments->keyBy(fn ($p) => $p->month->format('Y-m'));

        while ($start->lte($current)) {
            $key     = $start->format('Y-m');
            $payment = $paymentsIndexed->get($key);

            $months[] = [
                'key'     => $key,
                'label'   => ucfirst($start->isoFormat('MMMM YYYY')),
                'amount'  => $lease->monthly_rent,
                'paid'    => $payment?->amount ?? 0,
                'rest'    => $payment ? max(0, $lease->monthly_rent - $payment->amount) : $lease->monthly_rent,
                'payment' => $payment,
                'status'  => $this->computeStatus($start->copy(), $lease->due_day, $payment, $lease->monthly_rent),
            ];

            $start->addMonth();
        }

        return view('tenant-portal.rents.index', [
            'tenant' => $tenant,
            'lease'  => $lease,
            'months' => array_reverse($months),
        ]);
    }

    public function notice(): \Illuminate\View\View
    {
        $tenant = auth()->guard('tenant')->user();
        $lease  = $tenant->activeLease()->firstOrFail();

        $nextDue = now()->day <= $lease->due_day
            ? now()->setDay($lease->due_day)
            : now()->addMonth()->setDay($lease->due_day);

        return view('tenant-portal.rents.notice', compact('tenant', 'lease', 'nextDue'));
    }

    private function computeStatus(Carbon $month, int $dueDay, $payment, int $monthlyRent): string
    {
        if ($payment) {
            return $payment->amount >= $monthlyRent ? 'paid' : 'partial';
        }

        $due = $month->copy()->setDay($dueDay);

        return now()->gt($due) ? 'late' : 'upcoming';
    }
}
