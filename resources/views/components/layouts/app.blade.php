<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'MEEXEO Immobilier' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:wght@400;500&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-sable font-ui text-lagune antialiased" x-data="{ menu: false }">
<div class="flex min-h-screen">
    {{-- Barre latérale : repliée en tiroir sous 1024 px --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 w-[248px] shrink-0 bg-lagune text-sable transition-transform lg:static lg:translate-x-0"
        :class="menu ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex h-[68px] items-center px-6">
            <span class="font-titre text-xl text-papier">MEEXEO</span>
        </div>
        <nav class="mt-2 px-3">
            @php($nav = [
                ['properties.index', 'Biens'],
                ['tenants.index', 'Locataires'],
            ])
            @foreach ($nav as [$route, $label])
                @continue(! \Illuminate\Support\Facades\Route::has($route))
                <a href="{{ route($route) }}"
                   class="flex min-h-[44px] items-center rounded-meexeo px-3 text-sm {{ request()->routeIs($route) ? 'bg-lagune-actif text-papier' : 'text-sable/75 hover:text-papier' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
        @auth
            <div class="absolute inset-x-0 bottom-0 border-t border-white/10 p-4">
                <p class="text-sm text-papier">{{ auth()->user()->name }}</p>
                <p class="text-xs text-sable/60">{{ auth()->user()->role->label() }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button class="min-h-[44px] text-xs text-sable/70 hover:text-papier">Se déconnecter</button>
                </form>
            </div>
        @endauth
    </aside>

    <div class="min-w-0 flex-1">
        <header class="flex h-[68px] items-center gap-3 border-b border-lin bg-papier px-5">
            <button class="min-h-[44px] px-2 lg:hidden" @click="menu = ! menu" aria-label="Ouvrir le menu">☰</button>
            <div class="ml-auto flex items-center gap-2">{{ $topbar ?? '' }}</div>
        </header>
        <main class="p-6 lg:p-8">{{ $slot }}</main>
    </div>
</div>
</body>
</html>
