@props(['title' => 'SONOR LOCATION'])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:var(--color-sonor-surface);color:var(--color-sonor-ink);font-family:-apple-system,'Helvetica Neue',Helvetica,Arial,sans-serif">
    <header style="background:var(--color-sonor-navy);padding:16px 24px">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4">
            <a href="{{ route('public.home') }}" style="color:#fff;font-weight:800;font-size:18px;letter-spacing:.5px">SONOR LOCATION</a>
            <nav class="flex flex-wrap items-center gap-5" style="font-size:13.5px">
                <a href="{{ route('public.home') }}" style="color:#fff">Accueil</a>
                <a href="{{ route('public.vehicles.index') }}" style="color:#fff">Véhicules</a>
                <a href="{{ route('public.taxis') }}" style="color:#fff">Taxis</a>
                <a href="{{ route('public.sonorisation') }}" style="color:#fff">Sonorisation</a>
                <a href="{{ route('public.podiums') }}" style="color:#fff">Podiums</a>
            </nav>
        </div>
    </header>

    <main>{{ $slot }}</main>

    <footer style="background:var(--color-sonor-navy);color:#fff;padding:32px 24px;margin-top:48px">
        <div class="mx-auto max-w-6xl text-center" style="font-size:12.5px;color:var(--color-sonor-ink-soft)">
            © {{ now()->year }} SONOR LOCATION. Tous droits réservés.
        </div>
    </footer>
</body>
</html>
