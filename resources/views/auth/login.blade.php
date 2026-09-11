<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — MEEXEO Immobilier</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:wght@400;500&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
</head>
<body class="flex min-h-screen items-center justify-center bg-sable font-ui text-lagune p-6">
    <div class="w-full max-w-[380px]">
        <div class="text-center">
            <p class="font-titre text-3xl">MEEXEO</p>
            <p class="surtitre mt-1">Gestion immobilière</p>
        </div>

        <div class="mt-7 rounded-meexeo border border-lin-clair bg-papier p-7">
            <h1 class="font-titre text-xl">Connexion</h1>
            <p class="mt-1 text-xs text-brume">Accès réservé à l'équipe MEEXEO.</p>

            <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="email" class="text-xs font-semibold text-ardoise">Adresse email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin bg-papier px-3 text-sm focus:border-cuivre focus:outline-none">
                </div>
                <div>
                    <label for="password" class="text-xs font-semibold text-ardoise">Mot de passe</label>
                    <input id="password" name="password" type="password" required
                           class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin bg-papier px-3 text-sm focus:border-cuivre focus:outline-none">
                </div>

                @if ($errors->any())
                    <p class="rounded-meexeo border border-impaye-bord bg-impaye-bg px-3 py-2 text-xs text-impaye-texte">
                        {{ $errors->first() }}
                    </p>
                @endif

                <button type="submit" class="min-h-[44px] w-full rounded-meexeo bg-cuivre text-sm font-semibold text-papier">
                    Se connecter
                </button>
            </form>
        </div>
    </div>
</body>
</html>
