<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(): View
    {
        return view('public.vehicles.index');
    }
}
