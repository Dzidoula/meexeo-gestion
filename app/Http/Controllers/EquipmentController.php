<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use App\Models\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    public function index(): View
    {
        return view('equipment.index', [
            'equipment' => Equipment::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('equipment.form', ['equipment' => new Equipment()]);
    }

    public function store(StoreEquipmentRequest $request): RedirectResponse
    {
        Equipment::create($request->validated());

        return redirect()->route('equipment.index')->with('status', "L'équipement a été créé.");
    }

    public function edit(Equipment $equipment): View
    {
        return view('equipment.form', ['equipment' => $equipment]);
    }

    public function update(UpdateEquipmentRequest $request, Equipment $equipment): RedirectResponse
    {
        $equipment->update($request->validated());

        return redirect()->route('equipment.index')->with('status', "L'équipement a été mis à jour.");
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        if ($equipment->reservations()->exists()) {
            return back()->with('error', 'Cet équipement a des réservations, il ne peut pas être supprimé.');
        }

        $equipment->delete();

        return redirect()->route('equipment.index')->with('status', "L'équipement a été supprimé.");
    }
}
