<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('public.home', [
            'featuredVehicles' => Vehicle::with('vehicleType')->latest()->take(4)->get(),
            'vehiclesTotal' => Vehicle::count(),
        ]);
    }
}
