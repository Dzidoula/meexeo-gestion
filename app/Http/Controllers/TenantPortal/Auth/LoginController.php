<?php

namespace App\Http\Controllers\TenantPortal\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showForm() { return view('tenant-portal.auth.login'); }
    public function sendOtp(Request $request) { return back(); }
    public function showVerify() { return view('tenant-portal.auth.verify'); }
    public function verifyOtp(Request $request) { return back(); }
    public function logout(Request $request) { return redirect()->route('tenant-portal.login'); }
}
