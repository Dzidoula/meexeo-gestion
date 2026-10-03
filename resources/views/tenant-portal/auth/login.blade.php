<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — Espace Locataire</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-sable flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-lagune">Espace Locataire</h1>
            <p class="text-ardoise mt-1 text-sm">Connectez-vous avec votre numéro de téléphone</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('tenant-portal.send-otp') }}">
                @csrf

                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-lagune mb-2">
                        Numéro de téléphone
                    </label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="07 00 00 00 00"
                        autocomplete="tel"
                        autofocus
                        class="w-full px-4 py-3 rounded-xl border @error('phone') border-red-400 bg-red-50 @else border-gray-200 @enderror text-lagune text-lg focus:outline-none focus:ring-2 focus:ring-acier focus:border-transparent"
                    >
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-lagune text-white py-3 rounded-xl font-medium text-base hover:bg-opacity-90 transition-colors">
                    Recevoir mon code
                </button>
            </form>
        </div>
    </div>
</body>
</html>
