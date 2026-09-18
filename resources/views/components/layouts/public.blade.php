@props(['title' => 'MASTERCLAYS'])
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:var(--color-mc-store-surface);color:var(--color-mc-store-ink);font-family:-apple-system,'Helvetica Neue',Helvetica,Arial,sans-serif">
    <header style="background:var(--color-mc-store-navy);padding:16px 24px">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4">
            <a href="{{ route('public.home') }}" class="inline-flex items-center" style="border-radius:var(--radius-mc-store-sm);background:#fff;padding:4px 10px">
                <img src="{{ asset('images/mc-store/logo_masterclays001.png') }}" alt="MASTERCLAYS" style="height:28px;width:auto">
            </a>
            <nav class="flex flex-wrap items-center gap-5" style="font-size:13.5px">
                <a href="{{ route('public.home') }}" style="color:#fff">Accueil</a>
                <a href="{{ route('public.vehicles.index') }}" style="color:#fff">Véhicules</a>
                <a href="{{ route('public.taxis') }}" style="color:#fff">Taxis</a>
                <a href="{{ route('public.sonorisation') }}" style="color:#fff">Sonorisation</a>
                <a href="{{ route('public.podiums') }}" style="color:#fff">Podiums</a>
                <a href="{{ route('cart.show') }}" style="color:#fff">Panier ({{ \App\Support\Cart::count() }})</a>
                @auth('customer')
                    <a href="{{ route('customer.account') }}" style="color:#fff">Mon compte</a>
                @else
                    <a href="{{ route('customer.login') }}" style="color:#fff">Connexion</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>{{ $slot }}</main>

    <footer style="background:var(--color-mc-store-navy);color:#fff;padding:40px 24px 24px">
        <div class="mx-auto grid max-w-6xl gap-8 sm:grid-cols-3" style="font-size:13px">
            <div>
                <div class="inline-flex items-center" style="border-radius:var(--radius-mc-store-sm);background:#fff;padding:5px 12px">
                    <img src="{{ asset('images/mc-store/logo_masterclays001.png') }}" alt="MASTERCLAYS" style="height:26px;width:auto">
                </div>
                <p class="mt-3" style="color:#C3C9D3">Vente de véhicules, taxis, sonorisation et podiums — un seul partenaire pour vos besoins de mobilité et d'événements en Côte d'Ivoire.</p>
            </div>
            <div>
                <p style="font-weight:700;color:#fff">Nos activités</p>
                <ul class="mt-2 space-y-1.5" style="color:#C3C9D3">
                    <li><a href="{{ route('public.vehicles.index') }}" style="color:#C3C9D3">Véhicules</a></li>
                    <li><a href="{{ route('public.taxis') }}" style="color:#C3C9D3">Taxis</a></li>
                    <li><a href="{{ route('public.sonorisation') }}" style="color:#C3C9D3">Sonorisation</a></li>
                    <li><a href="{{ route('public.podiums') }}" style="color:#C3C9D3">Podiums</a></li>
                </ul>
            </div>
            <div>
                <p style="font-weight:700;color:#fff">Informations</p>
                <ul class="mt-2 space-y-1.5" style="color:#C3C9D3">
                    <li>Contact — bientôt disponible</li>
                    <li>Conditions générales de vente — bientôt disponible</li>
                    <li>Politique de confidentialité — bientôt disponible</li>
                </ul>
            </div>
        </div>
        <div class="mx-auto mt-8 max-w-6xl border-t pt-5 text-center" style="border-color:rgba(255,255,255,.1);font-size:12.5px;color:var(--color-mc-store-ink-soft)">
            © {{ now()->year }} MASTERCLAYS. Tous droits réservés.
        </div>
    </footer>
</body>
</html>
