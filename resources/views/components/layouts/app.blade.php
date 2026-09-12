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
            {{-- Icônes : trait de 1,6 px sur grille 24 px, recolorées par currentColor (spec §7). --}}
            @php
                $iconBiens = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5 shrink-0" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>';
                $iconLocataires = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5 shrink-0" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>';
                $nav = [
                    ['properties.index', 'Biens', $iconBiens],
                    ['tenants.index', 'Locataires', $iconLocataires],
                ];
            @endphp
            @foreach ($nav as [$route, $label, $icon])
                @continue(! \Illuminate\Support\Facades\Route::has($route))
                <a href="{{ route($route) }}"
                   class="flex min-h-[44px] items-center gap-3 rounded-meexeo px-3 text-sm {{ request()->routeIs($route) ? 'bg-lagune-actif text-papier' : 'text-sable/75 hover:text-papier' }}">
                    {!! $icon !!}
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
