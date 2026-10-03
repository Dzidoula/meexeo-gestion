<?php

namespace App\Http\Controllers\TenantPortal\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /** Whether the code is logged and kept in the session for later display. */
    public static function exposesOtp(): bool
    {
        return app()->environment('local', 'testing') || self::displaysOtp();
    }

    /**
     * Whether the verification page renders the code instead of relying on SMS.
     * Deliberately excludes a bare `testing` environment so that both the on and
     * off paths stay testable.
     */
    public static function displaysOtp(): bool
    {
        return app()->environment('local')
            || (bool) config('tenant-portal.show_otp_on_screen');
    }

    public function showForm(Request $request): View|RedirectResponse
    {
        return Auth::guard('tenant')->check()
            ? redirect()->route('tenant-portal.dashboard')
            : view('tenant-portal.auth.login');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate(['phone' => 'required|string']);

        $ipKey    = 'otp-generate:'.$request->ip();
        $phoneKey = 'otp-generate-phone:'.sha1((string) $request->phone);

        foreach ([$ipKey, $phoneKey] as $key) {
            if (RateLimiter::tooManyAttempts($key, 5)) {
                abort(429, 'Trop de tentatives. Réessayez dans une heure.');
            }
        }

        // Counted before the lookup: an unknown number must cost an attempt too,
        // otherwise the budget never applies to enumeration probes.
        RateLimiter::hit($ipKey, 3600);
        RateLimiter::hit($phoneKey, 3600);

        $tenant = Tenant::where('phone1', $request->phone)->first();

        if (! $tenant) {
            throw ValidationException::withMessages([
                'phone' => 'Aucun locataire trouvé avec ce numéro.',
            ]);
        }

        $code = $tenant->generateOtp();

        if (self::exposesOtp()) {
            Log::info("[TenantPortal OTP] {$tenant->phone1} → code: {$code}");
            session(['_otp_dev_code' => $code]);
        }

        $request->session()->put('tenant_otp_id', $tenant->id);

        return redirect()->route('tenant-portal.verify');
    }

    public function showVerify(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('tenant_otp_id')) {
            return redirect()->route('tenant-portal.login');
        }

        return view('tenant-portal.auth.verify');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        if (! $request->session()->has('tenant_otp_id')) {
            return redirect()->route('tenant-portal.login');
        }

        $request->validate(['otp' => 'required|string|size:6']);

        $key = 'otp-verify:'.$request->session()->get('tenant_otp_id');

        if (RateLimiter::tooManyAttempts($key, 3)) {
            // Burn the code itself: clearing only the session would leave a
            // still-valid OTP for a fresh session to brute-force.
            Tenant::find($request->session()->get('tenant_otp_id'))?->invalidateOtp();

            $request->session()->forget('tenant_otp_id');
            throw ValidationException::withMessages([
                'otp' => 'Trop de tentatives. Demandez un nouveau code.',
            ]);
        }

        $tenant = Tenant::find($request->session()->get('tenant_otp_id'));

        if (! $tenant || ! $tenant->verifyOtp($request->otp)) {
            RateLimiter::hit($key, 600);
            throw ValidationException::withMessages([
                'otp' => 'Code incorrect ou expiré.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->forget('tenant_otp_id');
        $request->session()->regenerate();

        Auth::guard('tenant')->login($tenant, true);

        return redirect()->intended(route('tenant-portal.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('tenant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tenant-portal.login');
    }
}
