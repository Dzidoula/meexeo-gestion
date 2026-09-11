<?php
// app/Http/Controllers/TenantController.php
namespace App\Http\Controllers;

use App\Enums\TenantStatus;
use App\Models\Tenant;
use Illuminate\Http\Request;
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
}
