<?php

namespace App\Http\Controllers\TenantPortal;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\TenantPortal\StoreProofRequest;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProofController extends Controller
{
    public function create(): View
    {
        $tenant = auth()->guard('tenant')->user();
        $lease  = $tenant->activeLease()->firstOrFail();

        return view('tenant-portal.proofs.create', compact('tenant', 'lease'));
    }

    public function store(StoreProofRequest $request): RedirectResponse
    {
        $tenant = auth()->guard('tenant')->user();
        $lease  = $tenant->activeLease()->firstOrFail();

        $month = $request->month.'-01';
        $path  = $request->file('proof')->store('payments/proofs', 'public');

        $payment = $lease->payments()->where('month', $month)->first();

        if ($payment) {
            $payment->update(['proof_path' => $path]);
        } else {
            Payment::create([
                'lease_id'   => $lease->id,
                'month'      => $month,
                'amount'     => $request->amount,
                'paid_on'    => now()->toDateString(),
                'method'     => PaymentMethod::Cash,
                'proof_path' => $path,
                'reference'  => 'PREUVE-'.strtoupper(substr(md5($path), 0, 8)),
                'notes'      => 'Preuve envoyée via portail — en attente de vérification',
            ]);
        }

        return redirect()->route('tenant-portal.payments')
            ->with('success', 'Preuve envoyée. Elle sera vérifiée par votre gestionnaire.');
    }
}
