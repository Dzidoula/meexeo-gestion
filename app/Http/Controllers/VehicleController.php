<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Enums\FuelType;
use App\Enums\Transmission;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VehicleController extends Controller
{
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
