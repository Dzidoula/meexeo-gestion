<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Support\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function show(): View
    {
        return view('customer.account.show', [
            'customer' => Auth::guard('customer')->user(),
            'vehicles' => Cart::items(),
        ]);
    }
}
