<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->filled('type') ? (int) $request->query('type') : null;
        $search = trim((string) $request->query('q'));

        $vehicles = Vehicle::query()
            ->with('vehicleType')
            ->when($type, fn ($q) => $q->where('vehicle_type_id', $type))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('brand', 'like', "%{$search}%")->orWhere('model', 'like', "%{$search}%");
                });
            })
            ->orderBy('brand')->orderBy('model')
            ->paginate(12)
            ->withQueryString();

        return view('public.vehicles.index', [
            'vehicles' => $vehicles,
            'vehicleTypes' => VehicleType::orderBy('name')->get(),
        ]);
    }

    public function show(Vehicle $vehicle): View
    {
        return view('public.vehicles.show', ['vehicle' => $vehicle]);
    }
}
