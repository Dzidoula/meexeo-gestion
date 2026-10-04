<?php
// app/Http/Controllers/PortalProofController.php
namespace App\Http\Controllers;

use App\Models\Payment;
use App\Notifications\PortalNotice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Validation des preuves de paiement envoyées depuis le portail locataire.
 * Une preuve arrive avec portal_status = 'pending' et ne compte nulle part
 * comme réglée ; la valider la transforme en paiement ordinaire.
 */
class PortalProofController extends Controller
{
    public function index(): View
    {
        $pending = Payment::where('portal_status', 'pending')
            ->with(['lease.tenant', 'lease.property'])
            ->orderBy('created_at')
            ->get();

        $recent = Payment::whereIn('portal_status', ['rejected'])
            ->with(['lease.tenant'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('portal-proofs.index', compact('pending', 'recent'));
    }

    public function approve(Payment $payment): RedirectResponse
    {
        abort_unless($payment->portal_status === 'pending', 404);

        // portal_status à null : le paiement devient un règlement ordinaire,
        // compté dans les loyers du locataire et dans les recettes.
        $payment->update([
            'portal_status' => null,
            'notes'         => trim(($payment->notes ? $payment->notes."\n" : '')
                .'Preuve validée le '.now()->format('d/m/Y').' par '.auth()->user()->name),
        ]);

        $payment->lease->tenant->notify(PortalNotice::proofApproved($payment));

        return back()->with('status', 'Preuve validée : le paiement est enregistré.');
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        abort_unless($payment->portal_status === 'pending', 404);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'reason.required' => 'Indiquez au locataire pourquoi la preuve est refusée.',
            'reason.min'      => 'Le motif doit être un minimum explicite.',
        ]);

        $payment->update([
            'portal_status' => 'rejected',
            'notes'         => trim(($payment->notes ? $payment->notes."\n" : '')
                .'Preuve refusée le '.now()->format('d/m/Y').' par '.auth()->user()->name
                .' — '.$validated['reason']),
        ]);

        $payment->lease->tenant->notify(PortalNotice::proofRejected($validated['reason']));

        return back()->with('status', 'Preuve refusée : le locataire en est informé.');
    }

    /** Le reçu est servi par une route authentifiée, jamais par une URL publique. */
    public function proof(Payment $payment): StreamedResponse
    {
        abort_unless($payment->proof_path, 404);
        abort_unless(Storage::disk('public')->exists($payment->proof_path), 404);

        return Storage::disk('public')->response($payment->proof_path);
    }
}
