<?php

namespace App\Http\Controllers\TenantPortal;

use App\Enums\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\TenantPortal\StoreProofRequest;
use App\Models\Payment;
use Carbon\Carbon;
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

        $month = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();

        // A manager-recorded payment is financial evidence: the portal never
        // modifies one. An unverified upload always lands in its own pending row.
        $alreadyVerified = $lease->payments()
            ->whereDate('month', $month->toDateString())
            ->whereNull('portal_status')
            ->exists();

        if ($alreadyVerified) {
            return redirect()->route('tenant-portal.payments')
                ->with('info', 'Ce mois est déjà enregistré comme payé par votre gestionnaire.');
        }

        $path = $request->file('proof')->store('payments/proofs', 'public');

        Payment::create([
            'lease_id'      => $lease->id,
            'month'         => $month->toDateString(),
            'amount'        => $request->amount,
            'paid_on'       => now()->toDateString(),
            'method'        => PaymentMethod::Cash,
            'proof_path'    => $path,
            'portal_status' => 'pending',
            'reference'     => 'PREUVE-'.strtoupper(substr(md5($path), 0, 8)),
            'notes'         => 'Preuve envoyée via portail — en attente de vérification',
        ]);

        return redirect()->route('tenant-portal.payments')
            ->with('success', 'Preuve envoyée. Elle sera vérifiée par votre gestionnaire.');
    }
}
