<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
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
        // La route nommée properties.index n'existe qu'à partir de la Tâche 7 : on cible
        // le chemin littéral pour ne pas dépendre d'un nom de route pas encore déclaré.
        return Auth::check() ? redirect('/biens') : view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $key = 'login:'.$request->ip();

        // Cinq essais par minute et par IP : une tentative d'énumération devient inutilisable.
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Trop de tentatives. Réessayez dans une minute.',
            ]);
        }

        if (! Auth::attempt($request->only('email', 'password'), true)) {
            RateLimiter::hit($key, 60);

            // Message unique : ne jamais révéler si l'adresse existe.
            throw ValidationException::withMessages([
                'email' => 'Ces identifiants ne correspondent à aucun compte.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended('/biens');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
