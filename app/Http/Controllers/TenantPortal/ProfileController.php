<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use App\Http\Requests\TenantPortal\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $tenant = auth()->guard('tenant')->user();

        return view('tenant-portal.profile.show', compact('tenant'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $tenant = auth()->guard('tenant')->user();

        $tenant->update($request->only('email', 'phone2', 'occupation'));

        return redirect()->route('tenant-portal.profile')
            ->with('success', 'Profil mis à jour.');
    }
}
