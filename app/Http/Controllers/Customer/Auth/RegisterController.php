<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRegisterRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function show(): View|RedirectResponse
    {
        return Auth::guard('customer')->check()
            ? redirect()->route('customer.account')
            : view('customer.auth.register');
    }

    public function store(CustomerRegisterRequest $request): RedirectResponse
    {
        $customer = Customer::create($request->validated());

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        \App\Support\Cart::mergeIntoCustomer($customer);

        return redirect()->route('customer.account');
    }
}
