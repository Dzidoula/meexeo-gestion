<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $tenant   = auth()->guard('tenant')->user();
        $lease    = $tenant->activeLease()->firstOrFail();
        $payments = $lease->payments()->orderByDesc('paid_on')->paginate(15);

        return view('tenant-portal.payments.index', compact('tenant', 'lease', 'payments'));
    }

    public function create(): View
    {
        $tenant = auth()->guard('tenant')->user();
        $lease  = $tenant->activeLease()->firstOrFail();

        $paidMonths = $lease->payments()->pluck('month')->map(fn ($m) => $m->format('Y-m'))->toArray();
        $start      = Carbon::parse($lease->start_date)->startOfMonth();
        $current    = now()->startOfMonth();
        $unpaid     = [];

        while ($start->lte($current)) {
            $key = $start->format('Y-m');
            if (! in_array($key, $paidMonths, true)) {
                $unpaid[] = ['key' => $key, 'label' => ucfirst($start->isoFormat('MMMM YYYY'))];
            }
            $start->addMonth();
        }

        return view('tenant-portal.payments.create', compact('tenant', 'lease', 'unpaid'));
    }

    public function store(): \Illuminate\Http\RedirectResponse
    {
        return back();
    }
}
