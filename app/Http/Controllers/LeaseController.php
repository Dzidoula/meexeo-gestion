<?php
// app/Http/Controllers/LeaseController.php
namespace App\Http\Controllers;

use App\Enums\PropertyStatus;
use App\Http\Requests\StoreLeaseRequest;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Tenant;
use App\Services\LeaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaseController extends Controller
{
    public function __construct(private readonly LeaseService $leases)
    {
    }

    public function create(Request $request): View
    {
        $property = $request->filled('property')
            ? Property::find($request->query('property'))
            : null;

        return view('leases.form', [
            'property' => $property,
            // On ne propose que les biens réellement affectables.
            'properties' => Property::query()
                ->where('status', '!=', PropertyStatus::Occupied->value)
                ->orderBy('title')
                ->get(),
            'tenants' => Tenant::query()->orderBy('last_name')->orderBy('first_names')->get(),
        ]);
    }

    public function store(StoreLeaseRequest $request): RedirectResponse
    {
        $lease = $this->leases->create($request->validated());

        return redirect()
            ->route('properties.show', $lease->property)
            ->with('status', "Le bien a été affecté à {$lease->tenant->full_name}.");
    }

    public function end(Request $request, Lease $lease): RedirectResponse
    {
        $validated = $request->validate([
            'actual_end_date' => ['required', 'date', 'after_or_equal:'.$lease->start_date->toDateString()],
        ], [
            'actual_end_date.after_or_equal' => 'La date de fin ne peut pas précéder le début du bail.',
        ]);

        $this->leases->end($lease, $validated['actual_end_date']);

        return redirect()
            ->route('properties.show', $lease->property)
            ->with('status', 'Le bail a été clôturé et le bien est de nouveau libre.');
    }
}
