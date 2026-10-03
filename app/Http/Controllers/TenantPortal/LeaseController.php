<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LeaseController extends Controller
{
    public function show(): View
    {
        $tenant = auth()->guard('tenant')->user();
        $lease  = $tenant->activeLease()->with('property.primaryPhoto')->firstOrFail();

        return view('tenant-portal.lease.show', [
            'tenant'    => $tenant,
            'lease'     => $lease,
            'property'  => $lease->property,
            'documents' => $tenant->portalDocuments()->where('lease_id', $lease->id)->get(),
        ]);
    }
}
