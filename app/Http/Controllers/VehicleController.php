<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->filled('type') ? (int) $request->query('type') : null;
        $status = in_array($request->query('status'), ['disponible', 'epuise'], true) ? $request->query('status') : null;
        $search = trim((string) $request->query('q'));

        $vehicles = Vehicle::query()
            ->with('vehicleType')
            ->when($type, fn ($q) => $q->where('vehicle_type_id', $type))
            ->when($status === 'epuise', fn ($q) => $q->where('stock_quantity', 0))
            ->when($status === 'disponible', fn ($q) => $q->where('stock_quantity', '>', 0))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('brand', 'like', "%{$search}%")->orWhere('model', 'like', "%{$search}%");
                });
            })
            ->orderBy('brand')->orderBy('model')
            ->paginate(25)
            ->withQueryString();

        return view('vehicles.index', [
            'vehicles' => $vehicles,
            'vehicleTypes' => VehicleType::orderBy('name')->get(),
            'vehiclesTotal' => Vehicle::count(),
            'stockValue' => (int) Vehicle::query()->selectRaw('COALESCE(SUM(price * stock_quantity), 0) as total')->value('total'),
            'outOfStockCount' => Vehicle::where('stock_quantity', 0)->count(),
        ]);
    }

    public function create(): View
    {
        return view('vehicles.form', [
            'vehicle' => new Vehicle(),
            'vehicleTypes' => VehicleType::orderBy('name')->get(),
            'fuelTypes' => FuelType::options(),
            'transmissions' => Transmission::options(),
        ]);
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $vehicle = Vehicle::create($request->validated());

        return redirect()->route('vehicles.show', $vehicle)->with('status', 'Le véhicule a été créé.');
    }

    public function show(Vehicle $vehicle): View
    {
        return view('vehicles.show', ['vehicle' => $vehicle]);
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('vehicles.form', [
            'vehicle' => $vehicle,
            'vehicleTypes' => VehicleType::orderBy('name')->get(),
            'fuelTypes' => FuelType::options(),
            'transmissions' => Transmission::options(),
        ]);
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($request->validated());

        return redirect()->route('vehicles.show', $vehicle)->with('status', 'Le véhicule a été mis à jour.');
    }
}
