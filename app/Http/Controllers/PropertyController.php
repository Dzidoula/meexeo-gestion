<?php
// app/Http/Controllers/PropertyController.php
namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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

    public function create(): View
    {
        return view('properties.form', [
            'property' => new Property(['status' => PropertyStatus::Vacant, 'city' => 'Abidjan']),
            'types' => PropertyType::options(),
            'statuses' => PropertyStatus::options(),
        ]);
    }

    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $property = Property::create($request->validated());

        // Chemin littéral et non route('properties.show', ...) : cette route n'existe
        // qu'à partir de la Task 10. Son URI sera exactement /biens/{id}, donc ce lien
        // restera correct sans modification une fois la route déclarée.
        return redirect("/biens/{$property->id}")
            ->with('status', "Le bien {$property->reference} a été enregistré.");
    }

    public function show(Property $property): View
    {
        $property->load(['photos', 'documents']);

        return view('properties.show', [
            'property' => $property,
            'documentTypes' => DocumentType::options(),
        ]);
    }

    public function edit(Property $property): View
    {
        return view('properties.form', [
            'property' => $property,
            'types' => PropertyType::options(),
            'statuses' => PropertyStatus::options(),
        ]);
    }

    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $property->update($request->validated());

        // Même remarque que dans store() : chemin littéral en attendant la Task 10.
        return redirect("/biens/{$property->id}")
            ->with('status', 'Les informations du bien ont été mises à jour.');
    }
}
