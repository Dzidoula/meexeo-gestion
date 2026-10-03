<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

/**
 * Le design fusionne loyers et paiements en un seul écran « Loyers & paiements ».
 * Ces routes restent pour ne pas casser les liens déjà envoyés aux locataires.
 */
class RentController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('tenant-portal.payments');
    }

    public function notice(): RedirectResponse
    {
        return redirect()->route('tenant-portal.payments');
    }
}
