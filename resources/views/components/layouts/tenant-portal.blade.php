<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Espace Locataire' }} — MEEXEO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-sable text-lagune antialiased pb-20 lg:pb-0 lg:flex"
      x-data="{ mobileNav: false }">

    {{-- Sidebar desktop --}}
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:min-h-screen bg-white border-r border-gray-100 fixed top-0 left-0 bottom-0">
        <div class="px-6 py-5 border-b border-gray-100">
            <span class="text-lg font-semibold text-lagune">Espace Locataire</span>
            <p class="text-xs text-ardoise mt-0.5">{{ auth()->guard('tenant')->user()->fullName }}</p>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1">
            @php $nav = [
                ['route' => 'tenant-portal.dashboard',  'label' => 'Accueil',      'icon' => '🏠'],
                ['route' => 'tenant-portal.rents',       'label' => 'Loyers',       'icon' => '📋'],
                ['route' => 'tenant-portal.payments',    'label' => 'Paiements',    'icon' => '💳'],
                ['route' => 'tenant-portal.repairs',     'label' => 'Réparations',  'icon' => '🔧'],
                ['route' => 'tenant-portal.profile',     'label' => 'Profil',       'icon' => '👤'],
            ]; @endphp

            @foreach($nav as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs($item['route']) ? 'bg-lagune text-white' : 'text-ardoise hover:bg-sable hover:text-lagune' }}">
                    <span>{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="px-3 py-4 border-t border-gray-100">
            <form method="POST" action="{{ route('tenant-portal.logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2.5 rounded-xl text-sm text-ardoise hover:bg-red-50 hover:text-red-600 transition-colors">
                    🚪 Se déconnecter
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <main class="flex-1 lg:ml-64">
        <div class="max-w-2xl mx-auto px-4 py-6 lg:px-6 lg:py-8">
            {{ $slot }}
        </div>
    </main>

    {{-- Bottom navigation mobile --}}
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 z-30">
        <div class="grid grid-cols-5 h-16">
            @foreach($nav as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex flex-col items-center justify-center gap-0.5 text-xs font-medium transition-colors
                          {{ request()->routeIs($item['route']) ? 'text-lagune' : 'text-ardoise' }}">
                    <span class="text-xl leading-none">{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

</body>
</html>
