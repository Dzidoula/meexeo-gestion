<?php
namespace App\Http\Controllers\Public;

use App\Enums\FuelType;
use App\Enums\Transmission;
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
        $fuelType = in_array($request->query('fuel_type'), array_column(FuelType::cases(), 'value'), true)
            ? $request->query('fuel_type')
            : null;
        $transmission = in_array($request->query('transmission'), array_column(Transmission::cases(), 'value'), true)
            ? $request->query('transmission')
            : null;
        $priceMin = $request->filled('price_min') ? (int) $request->query('price_min') : null;
        $priceMax = $request->filled('price_max') ? (int) $request->query('price_max') : null;
        $search = trim((string) $request->query('q'));

        $vehicles = Vehicle::query()
            ->with('vehicleType')
            ->when($type, fn ($q) => $q->where('vehicle_type_id', $type))
            ->when($fuelType, fn ($q) => $q->where('fuel_type', $fuelType))
            ->when($transmission, fn ($q) => $q->where('transmission', $transmission))
            ->when($priceMin, fn ($q) => $q->where('price', '>=', $priceMin))
            ->when($priceMax, fn ($q) => $q->where('price', '<=', $priceMax))
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
            'fuelTypes' => FuelType::options(),
            'transmissions' => Transmission::options(),
        ]);
    }

    public function show(Vehicle $vehicle): View
    {
        return view('public.vehicles.show', ['vehicle' => $vehicle]);
    }
}
