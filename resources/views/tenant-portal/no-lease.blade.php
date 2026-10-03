<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace Locataire — MEEXEO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-sable flex items-center justify-center p-4">
    <div class="w-full max-w-sm text-center">
        <div class="text-6xl mb-4">🏠</div>
        <h1 class="text-xl font-semibold text-lagune mb-2">Pas de bail actif</h1>
        <p class="text-ardoise text-sm mb-6">
            Votre compte locataire n'est pas encore associé à un bail actif.
            Contactez votre gestionnaire.
        </p>
        <form method="POST" action="{{ route('tenant-portal.logout') }}">
            @csrf
            <button type="submit" class="text-sm text-ardoise underline">Se déconnecter</button>
        </form>
    </div>
</body>
</html>
