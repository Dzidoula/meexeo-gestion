<x-layouts.tenant-portal-auth title="Connexion">

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 sm:p-10">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Connexion</h2>
            <p class="text-sm text-gray-500 mt-2">
                Saisissez votre numéro de téléphone. Nous vous envoyons un code à 6 chiffres.
            </p>
        </div>

        <form method="POST" action="{{ route('tenant-portal.send-otp') }}" class="space-y-5">
            @csrf

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Numéro de téléphone</label>
                <div class="relative">
                    <x-tenant-portal.icon name="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autofocus
                           inputmode="tel" autocomplete="tel" placeholder="07 00 00 00 00"
                           class="w-full pl-10 pr-3 py-2.5 rounded-lg border bg-gray-50 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-pl-500 focus:border-transparent transition @error('phone') border-red-300 @else border-gray-200 @enderror">
                </div>
                @error('phone')
                    <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                        <x-tenant-portal.icon name="alert-circle" class="w-3.5 h-3.5" />
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-pl-600 hover:bg-pl-700 text-white text-sm font-semibold px-4 py-3 rounded-lg transition-all hover:shadow-lg hover:shadow-pl-500/25 flex items-center justify-center gap-2">
                Recevoir mon code
                <x-tenant-portal.icon name="arrow-right" class="w-4 h-4" />
            </button>
        </form>

        {{-- Pas d'inscription : c'est le gestionnaire qui ouvre les comptes. --}}
        <p class="text-center text-sm text-gray-500 mt-6">
            Vous n'avez pas encore d'accès ?<br>
            <span class="text-gray-400">Contactez votre gestionnaire pour qu'il active votre espace.</span>
        </p>
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">
        En vous connectant, vous acceptez les conditions d'utilisation du portail.
    </p>

</x-layouts.tenant-portal-auth>
