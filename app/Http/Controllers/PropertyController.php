<?php
// app/Http/Controllers/PropertyController.php
namespace App\Http\Controllers;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        // Un filtre dont la valeur n'est pas connue est ignoré : une URL bricolée
        // ne doit ni planter ni vider la liste silencieusement.
        $status = PropertyStatus::tryFrom((string) $request->query('status'));
        $type = PropertyType::tryFrom((string) $request->query('type'));
        $search = trim((string) $request->query('q'));

        $properties = Property::query()
            ->when($request->filled('city'), fn ($q) => $q->where('city', $request->query('city')))
            ->when($request->filled('commune'), fn ($q) => $q->where('commune', $request->query('commune')))
            ->when($status, fn ($q) => $q->where('status', $status->value))
            ->when($type, fn ($q) => $q->where('type', $type->value))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('lot_number', 'like', "%{$search}%")
                        ->orWhere('block_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('city')
            ->orderBy('commune')
            ->orderBy('title')
            ->paginate(25)
            ->withQueryString();

        return view('properties.index', [
            'properties' => $properties,
            'communes' => Property::query()->whereNotNull('commune')->distinct()->orderBy('commune')->pluck('commune'),
            'types' => PropertyType::options(),
            'statuses' => PropertyStatus::options(),
        ]);
    }
}
