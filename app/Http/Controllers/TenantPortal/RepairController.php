<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;
use App\Http\Requests\TenantPortal\StoreRepairRequest;
use App\Models\RepairRequest;
use App\Notifications\PortalNotice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RepairController extends Controller
{
    public function index(): View
    {
        $tenant  = auth()->guard('tenant')->user();
        $repairs = RepairRequest::where('tenant_id', $tenant->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('tenant-portal.repairs.index', compact('tenant', 'repairs'));
    }

    /** Le formulaire est une fenêtre sur la liste, comme dans la maquette. */
    public function create(): RedirectResponse
    {
        return redirect()->route('tenant-portal.repairs');
    }

    public function store(StoreRepairRequest $request): RedirectResponse
    {
        $tenant = auth()->guard('tenant')->user();
        $lease  = $tenant->activeLease()->firstOrFail();

        $photos = [];
        foreach ($request->file('photos', []) as $photo) {
            $photos[] = $photo->store('repairs/photos', 'public');
        }

        $videoPath = $request->hasFile('video')
            ? $request->file('video')->store('repairs/videos', 'public')
            : null;

        $repair = RepairRequest::create([
            'tenant_id'   => $tenant->id,
            'lease_id'    => $lease->id,
            'type'        => $request->type,
            'description' => $request->description,
            'urgency'     => $request->urgency,
            'status'      => 'recu',
            'photos'      => $photos ?: null,
            'video_path'  => $videoPath,
        ]);

        $tenant->notify(PortalNotice::repairReceived($repair));

        return redirect()->route('tenant-portal.repairs')
            ->with('success', 'Signalement enregistré. Votre gestionnaire en a été informé.');
    }

    public function show(RepairRequest $repair): View
    {
        $tenant = auth()->guard('tenant')->user();

        abort_if($repair->tenant_id !== $tenant->id, 403);

        return view('tenant-portal.repairs.show', compact('repair', 'tenant'));
    }
}
