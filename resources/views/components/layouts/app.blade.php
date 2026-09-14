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
<body class="min-h-screen bg-sable font-ui text-lagune antialiased" x-data="{
    menu: false,
    collapsed: (() => { try { return localStorage.getItem('mc_sidebar_collapsed') === '1'; } catch (e) { return false; } })(),
    toggle() {
        this.collapsed = ! this.collapsed;
        try { localStorage.setItem('mc_sidebar_collapsed', this.collapsed ? '1' : '0'); } catch (e) {}
    },
}">
<div class="flex min-h-screen">
    {{-- Sidebar : tiroir en recouvrement sous 1024px (ergonomie terrain
         MEEXEO déjà en place), rétractable 250↔80px au-dessus, état persisté
         en localStorage (pas de préférence serveur pour ce chantier). L'état
         menu/collapsed/toggle() vit sur <body> (Step 3) — cet <aside> n'a pas
         son propre x-data, il hérite de la portée du body. --}}
    <aside
        :class="[menu ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', collapsed ? 'lg:w-[80px]' : 'lg:w-[250px]']"
        class="fixed inset-y-0 left-0 z-40 w-[250px] shrink-0 transition-[transform,width] lg:static"
        style="background:var(--color-mc-sidebar);display:flex;flex-direction:column"
    >
        <div class="flex items-center gap-2.5" style="padding:20px 18px;border-bottom:1px solid rgba(255,255,255,.08)">
            <div class="flex items-center justify-center" style="width:38px;height:38px;flex:none;border-radius:10px;background:linear-gradient(135deg,var(--color-mc-accent),#7C3AED);transform:rotate(45deg)">
                <div style="width:14px;height:14px;background:#fff;border-radius:3px;transform:rotate(45deg)"></div>
            </div>
            <div x-show="! collapsed" x-cloak>
                <div style="font-size:15px;font-weight:800;color:var(--color-mc-on-accent);letter-spacing:.5px;line-height:1.1">MASTERCLAYS</div>
                <div style="font-size:9.5px;font-weight:600;color:var(--color-mc-sidebar-ink);letter-spacing:1px">DASHBOARD CENTRALISÉ</div>
            </div>
        </div>

        <nav class="mc-scroll" style="flex:1;overflow-y:auto;padding:14px 12px 20px">
            {{-- Rangée d'accueil : restyle l'entrée Tableau de bord MEEXEO existante,
                 plutôt que d'en ajouter une seconde qui pointerait vers la même route. --}}
            <a href="{{ route('dashboard') }}"
               class="flex min-h-[44px] items-center gap-3 rounded-lg mb-0.5"
               style="padding:10px;{{ request()->routeIs('dashboard') ? 'background:rgba(67,56,202,.165);border-left:3px solid var(--color-mc-accent)' : 'border-left:3px solid transparent' }}">
                <div class="flex items-center justify-center text-white font-extrabold text-xs" style="width:34px;height:34px;flex:none;border-radius:8px;background:var(--color-mc-accent)">TB</div>
                <div x-show="! collapsed" x-cloak style="min-width:0">
                    <div style="font-size:13.5px;font-weight:700;color:var(--color-mc-on-accent)">Tableau de bord</div>
                    <div style="font-size:11px;color:var(--color-mc-sidebar-ink)">Vue d'ensemble</div>
                </div>
            </a>

            @foreach (config('masterclays_modules.nav_groups') as $groupTitle => $moduleIds)
                <div style="margin-top:18px">
                    <div x-show="! collapsed" x-cloak style="font-size:10.5px;font-weight:700;color:#5D6285;letter-spacing:1px;padding:0 10px 8px">{{ $groupTitle }}</div>
                    @foreach ($moduleIds as $moduleId)
                        @php($module = config("masterclays_modules.modules.{$moduleId}"))
                        @php($moduleRoute = $module['route'] ?? null)
                        @continue($moduleRoute && ! \Illuminate\Support\Facades\Route::has($moduleRoute))
                        @php($href = $moduleRoute ? route($moduleRoute) : route('modules.show', $moduleId))
                        @php($isActive = $moduleRoute ? request()->routeIs($moduleRoute) : request()->is("modules/{$moduleId}"))
                        <a href="{{ $href }}" class="flex min-h-[44px] items-center gap-3 rounded-lg mb-0.5"
                           style="padding:10px;{{ $isActive ? 'background:'.$module['color'].'2A;border-left:3px solid '.$module['color'] : 'border-left:3px solid transparent' }}">
                            <div class="flex items-center justify-center text-white font-extrabold text-xs" style="width:34px;height:34px;flex:none;border-radius:8px;background:{{ $module['color'] }}">{{ $module['icon'] }}</div>
                            <div x-show="! collapsed" x-cloak style="min-width:0">
                                <div style="font-size:13.5px;font-weight:700;color:var(--color-mc-on-accent);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $module['label'] }}</div>
                                <div style="font-size:11px;color:var(--color-mc-sidebar-ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $module['sub'] }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endforeach

            {{-- Entrées MEEXEO historiques : conservées telles quelles, dupliquées
                 avec « Gestion locative » ci-dessus jusqu'au chantier 2. --}}
            <div style="margin-top:18px">
                <div x-show="! collapsed" x-cloak class="surtitre" style="padding:0 10px 8px">MEEXEO (historique)</div>
                @php($iconBiens = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5 shrink-0 text-white" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>')
                @php($iconLocataires = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5 shrink-0 text-white" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>')
                @php($iconPaiements = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-5 w-5 shrink-0 text-white" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-9-10.5h18a1.5 1.5 0 0 1 1.5 1.5v10.5a1.5 1.5 0 0 1-1.5 1.5h-18a1.5 1.5 0 0 1-1.5-1.5V6a1.5 1.5 0 0 1 1.5-1.5Z" /></svg>')
                @php($legacyNav = [
                    ['properties.index', 'Biens', $iconBiens],
                    ['tenants.index', 'Locataires', $iconLocataires],
                    ['payments.create', 'Paiements', $iconPaiements],
                ])
                @foreach ($legacyNav as [$route, $label, $icon])
                    @continue(! \Illuminate\Support\Facades\Route::has($route))
                    <a href="{{ route($route) }}" class="flex min-h-[44px] items-center gap-3 rounded-lg mb-0.5"
                       style="padding:10px;{{ request()->routeIs($route) ? 'background:rgba(67,56,202,.16);border-left:3px solid var(--color-mc-accent)' : 'border-left:3px solid transparent' }}">
                        <span class="text-white">{!! $icon !!}</span>
                        <span x-show="! collapsed" x-cloak style="font-size:13.5px;font-weight:700;color:var(--color-mc-on-accent)">{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        </nav>

        @auth
            <div x-show="! collapsed" x-cloak class="border-t border-white/10" style="padding:16px">
                <p class="text-sm text-white">{{ auth()->user()->name }}</p>
                <p class="text-xs" style="color:var(--color-mc-sidebar-ink)">{{ auth()->user()->role->label() }}</p>
            </div>
        @endauth
    </aside>

    <div class="min-w-0 flex-1">
        <header class="flex items-center gap-4" x-data="{ openDropdown: null }" @click.outside="openDropdown = null" style="height:64px;flex:none;background:#fff;border-bottom:1px solid var(--color-mc-border);padding:0 24px;position:relative;z-index:20">
            <button type="button" aria-label="Réduire ou déplier le menu" @click="toggle()"
                    class="min-h-[44px] hidden lg:flex items-center justify-center" style="width:36px;height:36px;border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:#fff">
                <span style="width:16px;height:2px;background:#4A4E6B;display:block;position:relative">
                    <span style="position:absolute;top:-5px;left:0;width:16px;height:2px;background:#4A4E6B"></span>
                    <span style="position:absolute;top:5px;left:0;width:16px;height:2px;background:#4A4E6B"></span>
                </span>
            </button>
            <button type="button" aria-label="Ouvrir le menu" @click="menu = ! menu" class="min-h-[44px] px-2 lg:hidden">☰</button>

            <div class="hidden md:flex items-center" style="flex:1;max-width:420px;gap:10px;background:var(--color-mc-canvas);border-radius:var(--radius-mc-sm);padding:9px 14px">
                <span style="font-size:13.5px;color:var(--color-mc-ink-faint)">Rechercher...</span>
            </div>

            <div style="flex:1"></div>

            <div class="relative">
                <button type="button" aria-label="Notifications" @click="openDropdown = openDropdown === 'notif' ? null : 'notif'"
                        class="min-h-[44px]" style="width:38px;height:38px;border-radius:var(--radius-mc-sm);border:none;background:var(--color-mc-canvas)">🔔</button>
                <div x-show="openDropdown === 'notif'" x-cloak style="position:absolute;top:48px;right:0;width:300px;background:#fff;border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);box-shadow:0 16px 40px rgba(20,20,40,.18);padding:14px">
                    <div style="font-size:12.5px;font-weight:800;color:#5D6285;letter-spacing:.5px;margin-bottom:8px">NOTIFICATIONS</div>
                    <div style="font-size:12.5px;color:var(--color-mc-ink-soft)">Aucune notification pour le moment.</div>
                </div>
            </div>

            <div class="relative">
                <button type="button" aria-label="Messages" @click="openDropdown = openDropdown === 'mail' ? null : 'mail'"
                        class="min-h-[44px]" style="width:38px;height:38px;border-radius:var(--radius-mc-sm);border:none;background:var(--color-mc-canvas)">✉️</button>
                <div x-show="openDropdown === 'mail'" x-cloak style="position:absolute;top:48px;right:0;width:300px;background:#fff;border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);box-shadow:0 16px 40px rgba(20,20,40,.18);padding:14px">
                    <div style="font-size:12.5px;font-weight:800;color:#5D6285;letter-spacing:.5px;margin-bottom:8px">MESSAGES</div>
                    <div style="font-size:12.5px;color:var(--color-mc-ink-soft)">Aucun message pour le moment.</div>
                </div>
            </div>

            <div style="width:1px;height:26px;background:var(--color-mc-border)"></div>

            @auth
                <div class="relative">
                    <button type="button" @click="openDropdown = openDropdown === 'profile' ? null : 'profile'" class="flex items-center min-h-[44px]" style="gap:10px;padding:4px 6px;border-radius:var(--radius-mc-sm)">
                        <span class="flex items-center justify-center text-white font-extrabold text-sm" style="width:38px;height:38px;border-radius:50%;background:var(--color-mc-accent)">
                            {{ collect(explode(' ', auth()->user()->name))->map(fn ($n) => mb_substr($n, 0, 1))->take(2)->implode('') }}
                        </span>
                        <span class="hidden md:block text-left">
                            <span style="display:block;font-size:13px;font-weight:700">{{ auth()->user()->name }}</span>
                            <span style="display:block;font-size:11px;color:var(--color-mc-ink-faint)">{{ auth()->user()->role->label() }}</span>
                        </span>
                    </button>
                    <div x-show="openDropdown === 'profile'" x-cloak style="position:absolute;top:52px;right:0;width:200px;background:#fff;border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);box-shadow:0 16px 40px rgba(20,20,40,.18);padding:8px">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="min-h-[44px] w-full text-left" style="padding:9px 10px;border-radius:var(--radius-mc-sm);font-size:13px;font-weight:600;color:var(--color-mc-danger)">Déconnexion</button>
                        </form>
                    </div>
                </div>
            @endauth
        </header>
        <main class="p-6 lg:p-8">{{ $slot }}</main>
    </div>
</div>
</body>
</html>
