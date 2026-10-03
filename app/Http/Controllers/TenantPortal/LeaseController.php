<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LeaseController extends Controller
{
    public function show(): View
    {
        $tenant = auth()->guard('tenant')->user();
        $lease  = $tenant->activeLease()->with('property')->firstOrFail();

        return view('tenant-portal.lease.show', compact('tenant', 'lease'));
    }
}
