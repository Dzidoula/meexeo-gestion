<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vérification — Espace Locataire</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-sable flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-lagune">Entrez le code</h1>
            <p class="text-ardoise mt-1 text-sm">Un code à 6 chiffres vous a été envoyé</p>
            @if(\App\Http\Controllers\TenantPortal\Auth\LoginController::displaysOtp() && session('_otp_dev_code'))
                <div class="mt-3 rounded-xl border border-amber-300 bg-amber-50 px-3 py-2.5 text-left">
                    <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-800">
                        Démonstration — envoi SMS non activé
                    </p>
                    <p class="mt-1 font-mono text-lg font-semibold text-amber-900">
                        {{ session('_otp_dev_code') }}
                    </p>
                    <p class="mt-1 text-[11px] leading-snug text-amber-700">
                        Le code s'affiche ici faute de SMS. Ne mettez aucune donnée réelle
                        dans cet environnement : n'importe qui connaissant un numéro de
                        téléphone peut ouvrir le compte correspondant.
                    </p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('tenant-portal.verify.submit') }}">
                @csrf

                <div class="mb-4">
                    <label for="otp" class="block text-sm font-medium text-lagune mb-2">
                        Code de vérification
                    </label>
                    <input
                        type="text"
                        id="otp"
                        name="otp"
                        inputmode="numeric"
                        pattern="\d{6}"
                        maxlength="6"
                        autocomplete="one-time-code"
                        autofocus
                        class="w-full px-4 py-3 rounded-xl border @error('otp') border-red-400 bg-red-50 @else border-gray-200 @enderror text-lagune text-2xl font-mono tracking-widest text-center focus:outline-none focus:ring-2 focus:ring-acier focus:border-transparent"
                        placeholder="000000"
                    >
                    @error('otp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-lagune text-white py-3 rounded-xl font-medium text-base hover:bg-opacity-90 transition-colors">
                    Valider
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('tenant-portal.login') }}" class="text-sm text-ardoise hover:text-acier">
                    ← Changer de numéro
                </a>
            </div>
        </div>
    </div>
</body>
</html>
