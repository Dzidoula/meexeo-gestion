@php
    $initials = mb_strtoupper(mb_substr($tenant->first_names, 0, 1).mb_substr($tenant->last_name, 0, 1));
@endphp

<x-layouts.tenant-portal title="Mon profil">
<div class="space-y-6 animate-fade-in max-w-3xl mx-auto">

    {{-- ===== Carte d'identité ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-pl-500 to-pl-700 flex items-center justify-center text-2xl font-bold text-white shrink-0">
                {{ $initials ?: 'L' }}
            </div>
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-gray-900 truncate">{{ $tenant->fullName }}</h2>
                <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                    <x-tenant-portal.icon name="phone" class="w-4 h-4" />
                    {{ $tenant->phone1 }}
                </p>
                <p class="text-xs text-gray-400 mt-1 flex items-center gap-1.5">
                    <x-tenant-portal.icon name="calendar" class="w-3.5 h-3.5" />
                    Membre depuis {{ $tenant->created_at->isoFormat('MMMM YYYY') }}
                </p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2">
            <x-tenant-portal.icon name="check" class="w-4 h-4" />
            {{ session('success') }}
        </div>
    @endif

    {{-- ===== Informations modifiables ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8">
        <div class="flex items-center gap-2 mb-6">
            <x-tenant-portal.icon name="user" class="w-5 h-5 text-pl-600" />
            <h3 class="font-semibold text-gray-900">Informations personnelles</h3>
        </div>

        <form method="POST" action="{{ route('tenant-portal.profile.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            {{-- Nom et téléphone principal sont figés : seul le gestionnaire les change,
                 et le téléphone est l'identifiant de connexion. --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Prénom</label>
                    <input type="text" value="{{ $tenant->first_names }}" disabled
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-100 text-sm text-gray-500 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nom</label>
                    <input type="text" value="{{ $tenant->last_name }}" disabled
                           class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-100 text-sm text-gray-500 cursor-not-allowed">
                </div>
            </div>
            <p class="text-xs text-gray-400 -mt-3">
                Votre nom est géré par votre gestionnaire. Contactez-le pour toute correction.
            </p>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone principal</label>
                <div class="relative">
                    <x-tenant-portal.icon name="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input type="tel" value="{{ $tenant->phone1 }}" disabled
                           class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-gray-200 bg-gray-100 text-sm text-gray-500 cursor-not-allowed">
                </div>
                <p class="text-xs text-gray-400 mt-1">C'est votre identifiant de connexion : il ne peut pas être modifié ici.</p>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <div class="relative">
                    <x-tenant-portal.icon name="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input id="email" type="email" name="email" value="{{ old('email', $tenant->email) }}"
                           placeholder="vous@exemple.ci"
                           class="w-full pl-10 pr-3 py-2.5 rounded-lg border bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-pl-500 focus:border-transparent transition @error('email') border-red-300 @else border-gray-200 @enderror">
                </div>
                @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="phone2" class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone secondaire</label>
                <div class="relative">
                    <x-tenant-portal.icon name="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input id="phone2" type="tel" name="phone2" value="{{ old('phone2', $tenant->phone2) }}"
                           placeholder="07 00 00 00 00"
                           class="w-full pl-10 pr-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-pl-500 focus:border-transparent transition">
                </div>
            </div>

            <div>
                <label for="occupation" class="block text-sm font-medium text-gray-700 mb-1.5">Profession</label>
                <input id="occupation" type="text" name="occupation" value="{{ old('occupation', $tenant->occupation) }}"
                       class="w-full px-3 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-pl-500 focus:border-transparent transition">
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                        class="flex items-center gap-2 bg-pl-600 hover:bg-pl-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition hover:shadow-lg hover:shadow-pl-500/25">
                    <x-tenant-portal.icon name="save" class="w-4 h-4" />
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

    {{-- ===== Informations du compte ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8">
        <div class="flex items-center gap-2 mb-5">
            <x-tenant-portal.icon name="building" class="w-5 h-5 text-pl-600" />
            <h3 class="font-semibold text-gray-900">Informations du compte</h3>
        </div>
        <div class="space-y-3">
            @if($tenant->id_number)
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">N° CNI</span>
                    <span class="text-sm font-medium text-gray-900">{{ $tenant->id_number }}</span>
                </div>
            @endif
            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                <span class="text-sm text-gray-500">Statut du compte</span>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700">
                    {{ $tenant->status->label() }}
                </span>
            </div>
            <div class="flex items-center justify-between py-2">
                <span class="text-sm text-gray-500">Date d'inscription</span>
                <span class="text-sm font-medium text-gray-900">{{ $tenant->created_at->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>

</div>
</x-layouts.tenant-portal>
