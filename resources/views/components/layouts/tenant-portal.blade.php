@props(['title' => null, 'unreadMessages' => 0, 'notifications' => null])

@php
    $tenant = auth()->guard('tenant')->user();

    $initials = mb_strtoupper(
        mb_substr(trim((string) $tenant?->first_names), 0, 1).
        mb_substr(trim((string) $tenant?->last_name), 0, 1)
    );

    $nav = [
        ['route' => 'tenant-portal.dashboard', 'label' => 'Tableau de bord',   'icon' => 'grid'],
        ['route' => 'tenant-portal.lease',     'label' => 'Mon logement',      'icon' => 'home'],
        ['route' => 'tenant-portal.payments',  'label' => 'Loyers & paiements','icon' => 'card'],
        ['route' => 'tenant-portal.repairs',   'label' => 'Entretien',         'icon' => 'wrench'],
        ['route' => 'tenant-portal.documents', 'label' => 'Documents',         'icon' => 'file'],
        ['route' => 'tenant-portal.messages',  'label' => 'Messagerie',        'icon' => 'chat', 'badge' => $unreadMessages],
        ['route' => 'tenant-portal.profile',   'label' => 'Mon profil',        'icon' => 'user'],
    ];

    $notifications ??= collect();
    $unreadNotifications = $notifications->whereNull('read_at')->count();

    $current = collect($nav)->first(fn ($i) => request()->routeIs($i['route']));
    $heading = $title ?? $current['label'] ?? 'Espace locataire';
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $heading }} — MEEXEO IMMOBILIER</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: var(--font-pl); line-height: 1.5; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex antialiased"
      x-data="{ mobileOpen: false, notifOpen: false }">

    {{-- ===================== Barre latérale — bureau ===================== --}}
    <aside class="hidden lg:flex w-64 flex-col fixed inset-y-0 bg-pl-950 text-white z-30">
        <div class="flex items-center gap-3 px-6 h-16 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0">
                <x-tenant-portal.icon name="building" class="w-5 h-5 text-gold-300" />
            </div>
            <div class="min-w-0">
                <h1 class="text-sm font-bold tracking-tight truncate">MEEXEO IMMOBILIER</h1>
                <p class="text-[10px] text-white/50 tracking-widest uppercase">Portail Locataire</p>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-thin">
            @foreach($nav as $item)
                @php $active = request()->routeIs($item['route']) @endphp
                <a href="{{ route($item['route']) }}"
                   class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 relative
                          {{ $active ? 'bg-white/10 text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    @if($active)
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-gold-400"></span>
                    @endif
                    <x-tenant-portal.icon :name="$item['icon']" class="w-4 h-4 shrink-0 {{ $active ? 'text-gold-300' : '' }}" />
                    <span class="flex-1 text-left">{{ $item['label'] }}</span>
                    @if(($item['badge'] ?? 0) > 0)
                        <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-gold-400 text-pl-950">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="px-3 py-4 border-t border-white/10">
            <a href="{{ route('tenant-portal.profile') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/5 transition">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-sm font-bold text-pl-950 shrink-0">
                    {{ $initials ?: 'L' }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium truncate">{{ $tenant?->fullName ?: 'Locataire' }}</p>
                    <p class="text-xs text-white/40 truncate">Mon profil</p>
                </div>
            </a>
            <form method="POST" action="{{ route('tenant-portal.logout') }}" class="mt-2">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-white/60 hover:text-white hover:bg-white/5 transition">
                    <x-tenant-portal.icon name="logout" class="w-4 h-4" />
                    Déconnexion
                </button>
            </form>
        </div>
    </aside>

    {{-- ===================== Barre latérale — mobile ===================== --}}
    <div x-show="mobileOpen" x-cloak class="lg:hidden fixed inset-0 z-40"
         @keydown.escape.window="mobileOpen = false">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm animate-fade-in" @click="mobileOpen = false"></div>
        <aside class="absolute inset-y-0 left-0 w-72 bg-pl-950 text-white flex flex-col animate-slide-in">
            <div class="flex items-center justify-between px-6 h-16 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center">
                        <x-tenant-portal.icon name="building" class="w-5 h-5 text-gold-300" />
                    </div>
                    <h1 class="text-sm font-bold">MEEXEO</h1>
                </div>
                <button type="button" @click="mobileOpen = false" class="text-white/60 hover:text-white" aria-label="Fermer">
                    <x-tenant-portal.icon name="close" class="w-5 h-5" />
                </button>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                @foreach($nav as $item)
                    @php $active = request()->routeIs($item['route']) @endphp
                    <a href="{{ route($item['route']) }}"
                       class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition
                              {{ $active ? 'bg-white/10 text-white' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                        <x-tenant-portal.icon :name="$item['icon']" class="w-4 h-4" />
                        <span class="flex-1 text-left">{{ $item['label'] }}</span>
                        @if(($item['badge'] ?? 0) > 0)
                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-gold-400 text-pl-950">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>
            <div class="px-3 py-4 border-t border-white/10">
                <form method="POST" action="{{ route('tenant-portal.logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-white/60 hover:text-white hover:bg-white/5 transition">
                        <x-tenant-portal.icon name="logout" class="w-4 h-4" />
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>
    </div>

    {{-- ===================== Contenu ===================== --}}
    <div class="flex-1 lg:ml-64 flex flex-col min-w-0">

        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-lg border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6">
            <div class="flex items-center gap-3">
                <button type="button" @click="mobileOpen = true"
                        class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition" aria-label="Ouvrir le menu">
                    <x-tenant-portal.icon name="menu" class="w-5 h-5 text-gray-700" />
                </button>
                <h2 class="text-lg font-semibold text-gray-900">{{ $heading }}</h2>
            </div>

            <div class="flex items-center gap-2">
                <div class="relative">
                    <button type="button" @click="notifOpen = !notifOpen"
                            class="relative p-2 rounded-lg hover:bg-gray-100 transition" aria-label="Notifications">
                        <x-tenant-portal.icon name="bell" class="w-5 h-5 text-gray-600" />
                        @if($unreadNotifications > 0)
                            <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-gold-400 animate-pulse-soft"></span>
                        @endif
                    </button>

                    <template x-if="notifOpen">
                        <div>
                            <div class="fixed inset-0 z-30" @click="notifOpen = false"></div>
                            <div class="absolute right-0 top-12 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 z-40 animate-scale-in overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <h3 class="font-semibold text-gray-900">Notifications</h3>
                                </div>
                                <div class="max-h-80 overflow-y-auto scrollbar-thin">
                                    @forelse($notifications as $n)
                                        <div class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition">
                                            <p class="text-sm font-medium text-gray-900">{{ $n->title }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $n->body }}</p>
                                        </div>
                                    @empty
                                        <p class="px-4 py-8 text-center text-sm text-gray-400">Aucune notification</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <a href="{{ route('tenant-portal.profile') }}"
                   class="w-9 h-9 rounded-full bg-gradient-to-br from-pl-500 to-pl-700 flex items-center justify-center text-xs font-bold text-white hover:ring-2 hover:ring-pl-300 transition">
                    {{ $initials ?: 'L' }}
                </a>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-x-hidden">
            {{ $slot }}
        </main>
    </div>

</body>
</html>
