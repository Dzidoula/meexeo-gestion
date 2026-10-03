<x-layouts.tenant-portal-auth title="Vérification">

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 sm:p-10">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Entrez le code</h2>
            <p class="text-sm text-gray-500 mt-2">
                Un code à 6 chiffres vous a été envoyé. Il est valable 10 minutes.
            </p>
        </div>

        @if(\App\Http\Controllers\TenantPortal\Auth\LoginController::displaysOtp() && session('_otp_dev_code'))
            <div class="mb-6 rounded-xl border border-amber-300 bg-amber-50 p-4">
                <p class="text-[11px] font-bold uppercase tracking-wide text-amber-800">
                    Démonstration — envoi SMS non activé
                </p>
                <p class="mt-1.5 font-mono text-2xl font-bold tracking-[0.2em] text-amber-900">
                    {{ session('_otp_dev_code') }}
                </p>
                <p class="mt-1.5 text-[11px] leading-snug text-amber-700">
                    Le code s'affiche ici faute de SMS. N'utilisez aucune donnée réelle dans cet
                    environnement : quiconque connaît un numéro peut ouvrir le compte correspondant.
                </p>
            </div>
        @endif

        <form method="POST" action="{{ route('tenant-portal.verify.submit') }}" class="space-y-5">
            @csrf

            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700 mb-1.5">Code de vérification</label>
                <input id="otp" type="text" name="otp" required autofocus
                       inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="000000"
                       class="w-full px-4 py-3 rounded-lg border bg-gray-50 text-center font-mono text-2xl tracking-[0.4em] text-gray-900 focus:outline-none focus:ring-2 focus:ring-pl-500 focus:border-transparent transition @error('otp') border-red-300 @else border-gray-200 @enderror">
                @error('otp')
                    <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                        <x-tenant-portal.icon name="alert-circle" class="w-3.5 h-3.5" />
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-pl-600 hover:bg-pl-700 text-white text-sm font-semibold px-4 py-3 rounded-lg transition-all hover:shadow-lg hover:shadow-pl-500/25 flex items-center justify-center gap-2">
                Se connecter
                <x-tenant-portal.icon name="arrow-right" class="w-4 h-4" />
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-gray-100 text-center">
            <a href="{{ route('tenant-portal.login') }}"
               class="text-sm text-pl-600 hover:text-pl-700 font-medium">
                Changer de numéro ou demander un nouveau code
            </a>
        </div>
    </div>

</x-layouts.tenant-portal-auth>
