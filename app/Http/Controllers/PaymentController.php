<?php
namespace App\Http\Controllers;

use App\Enums\LeaseStatus;
use App\Enums\PaymentMethod;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Lease;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function create(Request $request): View
    {
        $lease = $request->filled('lease')
            ? Lease::with(['property', 'tenant'])->find($request->query('lease'))
            : null;

        return view('payments.form', [
            'lease' => $lease,
            'leases' => Lease::query()
                ->with(['property', 'tenant'])
                ->where('status', LeaseStatus::Active->value)
                ->get(),
            'methods' => PaymentMethod::options(),
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['proof_path'] = $request->file('proof')->store('payments/proofs', 'public');
        unset($validated['proof']);

        $payment = Payment::create($validated);

        return redirect()
            ->route('tenants.show', $payment->lease->tenant)
            ->with('status', 'Le paiement a été enregistré.');
    }
}
