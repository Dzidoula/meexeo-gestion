<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View|RedirectResponse
    {
        return Auth::guard('customer')->check()
            ? redirect()->route('customer.account')
            : view('customer.auth.login');
    }

    public function store(CustomerLoginRequest $request): RedirectResponse
    {
        $key = 'customer-login:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Trop de tentatives. Réessayez dans une minute.',
            ]);
        }

        if (! Auth::guard('customer')->attempt($request->only('email', 'password'), true)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'Ces identifiants ne correspondent à aucun compte.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        \App\Support\Cart::mergeIntoCustomer(Auth::guard('customer')->user());

        return redirect()->intended(route('customer.account'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('public.home');
    }
}
