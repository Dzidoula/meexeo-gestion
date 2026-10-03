<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use App\Support\RentSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $tenant = auth()->guard('tenant')->user();
        $lease  = $tenant->activeLease()->with('payments')->firstOrFail();

        $schedule = RentSchedule::forLease($lease);

        $rows = match ($request->get('filter')) {
            'pending' => $schedule->whereIn('status', ['pending', 'pending_proof']),
            'paid'    => $schedule->where('status', 'paid'),
            'overdue' => $schedule->where('status', 'overdue'),
            default   => $schedule,
        };

        return view('tenant-portal.payments.index', [
            'tenant'      => $tenant,
            'lease'       => $lease,
            'rows'        => $rows->values(),
            'filter'      => $request->get('filter', ''),
            'totalPaid'   => (int) $schedule->sum('paid'),
            'pendingCount'=> $schedule->whereIn('status', ['pending', 'pending_proof', 'partial'])->count(),
            'overdueCount'=> $schedule->where('status', 'overdue')->count(),
        ]);
    }

    /**
     * Le paiement en ligne n'est pas branché : le geste réel du locataire est
     * d'envoyer sa preuve après un virement mobile money.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('tenant-portal.proofs.create');
    }
}
