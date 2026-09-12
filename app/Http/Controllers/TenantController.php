<?php
// app/Http/Controllers/TenantController.php
namespace App\Http\Controllers;

use App\Enums\MaritalStatus;
use App\Enums\TenantDocumentType;
use App\Enums\TenantStatus;
use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(Request $request): View
    {
        $status = TenantStatus::tryFrom((string) $request->query('status'));
        $search = trim((string) $request->query('q'));

        $tenants = Tenant::query()
            ->when($status, fn ($q) => $q->where('status', $status->value))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('last_name', 'like', "%{$search}%")
                        ->orWhere('first_names', 'like', "%{$search}%")
                        ->orWhere('phone1', 'like', "%{$search}%")
                        ->orWhere('phone2', 'like', "%{$search}%")
                        ->orWhere('id_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_names')
            ->paginate(25)
            ->withQueryString();

        return view('tenants.index', [
            'tenants' => $tenants,
            'statuses' => TenantStatus::options(),
        ]);
    }

    public function create(): View
    {
        return view('tenants.form', [
            'tenant' => new Tenant(['status' => TenantStatus::Pending]),
            'statuses' => TenantStatus::options(),
            'maritalStatuses' => MaritalStatus::options(),
        ]);
    }

    public function store(StoreTenantRequest $request): RedirectResponse
    {
        $tenant = Tenant::create($request->validated());

        return redirect()
            ->route('tenants.show', $tenant)
            ->with('status', "La fiche {$tenant->reference} a été créée.");
    }

    public function show(Tenant $tenant): View
    {
        $tenant->load(['documents', 'activeLease.property', 'payments.lease']);

        return view('tenants.show', [
            'tenant' => $tenant,
            'documentTypes' => TenantDocumentType::options(),
        ]);
    }

    public function edit(Tenant $tenant): View
    {
        return view('tenants.form', [
            'tenant' => $tenant,
            'statuses' => TenantStatus::options(),
            'maritalStatuses' => MaritalStatus::options(),
        ]);
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $tenant->update($request->validated());

        return redirect()
            ->route('tenants.show', $tenant)
            ->with('status', 'La fiche a été mise à jour.');
    }
}
