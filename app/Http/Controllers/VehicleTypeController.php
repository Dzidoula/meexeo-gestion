<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleTypeRequest;
use App\Http\Requests\UpdateVehicleTypeRequest;
use App\Models\VehicleType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VehicleTypeController extends Controller
{
    public function index(): View
    {
        return view('vehicle-types.index', [
            'vehicleTypes' => VehicleType::withCount('vehicles')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('vehicle-types.form', ['vehicleType' => new VehicleType()]);
    }

    public function store(StoreVehicleTypeRequest $request): RedirectResponse
    {
        VehicleType::create($request->validated());

        return redirect()->route('vehicle-types.index')->with('status', 'Le type de véhicule a été créé.');
    }

    public function edit(VehicleType $vehicleType): View
    {
        return view('vehicle-types.form', ['vehicleType' => $vehicleType]);
    }

    public function update(UpdateVehicleTypeRequest $request, VehicleType $vehicleType): RedirectResponse
    {
        $vehicleType->update($request->validated());

        return redirect()->route('vehicle-types.index')->with('status', 'Le type de véhicule a été mis à jour.');
    }

    public function destroy(VehicleType $vehicleType): RedirectResponse
    {
        if ($vehicleType->vehicles()->exists()) {
            return back()->with('error', 'Ce type de véhicule contient des véhicules, il ne peut pas être supprimé.');
        }

        $vehicleType->delete();

        return redirect()->route('vehicle-types.index')->with('status', 'Le type de véhicule a été supprimé.');
    }
}
