@props(['title' => 'Connexion'])

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — MEEXEO IMMOBILIER</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: var(--font-pl); line-height: 1.5; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex antialiased">

    {{-- ===== Panneau de marque (bureau) ===== --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-pl-950 via-pl-800 to-pl-700">
        <div class="absolute inset-0 opacity-20 bg-cover bg-center"
             style="background-image:url('https://images.pexels.com/photos/28350363/pexels-photo-28350363.jpeg?auto=compress&cs=tinysrgb&h=650&w=940')"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-pl-950/90 via-pl-900/60 to-pl-800/40"></div>
        <div class="absolute top-20 right-20 w-64 h-64 rounded-full bg-gold-400/10 blur-3xl"></div>
        <div class="absolute bottom-20 left-10 w-80 h-80 rounded-full bg-pl-400/10 blur-3xl"></div>

        <div class="relative z-10 flex flex-col justify-between p-12 text-white">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center">
                    <x-tenant-portal.icon name="building" class="w-6 h-6 text-gold-300" />
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight">MEEXEO IMMOBILIER</h1>
                    <p class="text-xs text-white/60 tracking-widest uppercase">Portail Locataire</p>
                </div>
            </div>

            <div class="space-y-6">
                <h2 class="text-4xl font-bold leading-tight">
                    Votre espace locataire,<br>
                    <span class="text-gold-300">simple et transparent.</span>
                </h2>
                <p class="text-lg text-white/70 max-w-md">
                    Suivez vos loyers, envoyez vos preuves de paiement, signalez un problème
                    et retrouvez tous vos documents — en un seul endroit.
                </p>
                <div class="grid grid-cols-2 gap-4 pt-4 max-w-md">
                    @foreach([
                        ['Suivi des loyers',      'Mois par mois, sans surprise'],
                        ['Preuve de paiement',    'Wave, Orange Money, virement'],
                        ['Demandes d\'entretien', 'Suivies par numéro de ticket'],
                        ['Messagerie directe',    'Échange avec votre gestionnaire'],
                    ] as [$label, $desc])
                        <div class="rounded-xl bg-white/5 border border-white/10 p-4 backdrop-blur-sm">
                            <p class="text-sm font-semibold text-white">{{ $label }}</p>
                            <p class="text-xs text-white/50 mt-1">{{ $desc }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <p class="text-xs text-white/40">© {{ date('Y') }} MEEXEO IMMOBILIER — Tous droits réservés</p>
        </div>
    </div>

    {{-- ===== Panneau du formulaire ===== --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-12 bg-gray-50">
        <div class="w-full max-w-md animate-slide-up">
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <div class="w-11 h-11 rounded-xl bg-pl-600 flex items-center justify-center">
                    <x-tenant-portal.icon name="building" class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900 tracking-tight">MEEXEO IMMOBILIER</h1>
                    <p class="text-xs text-gray-400 tracking-widest uppercase">Portail Locataire</p>
                </div>
            </div>

            {{ $slot }}
        </div>
    </div>

</body>
</html>
