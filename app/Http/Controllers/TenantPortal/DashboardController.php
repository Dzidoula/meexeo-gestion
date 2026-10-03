<?php

namespace App\Http\Controllers\TenantPortal;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index() { return view('tenant-portal.dashboard'); }
}
