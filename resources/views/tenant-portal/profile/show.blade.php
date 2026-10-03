<x-layouts.tenant-portal title="Mon profil">

    <h1 class="text-2xl font-semibold text-lagune mb-6">Mon profil</h1>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 text-green-800 text-sm">✅ {{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
        <p class="text-xs text-ardoise mb-3">Informations personnelles</p>
        <div class="space-y-2">
            <div><p class="text-xs text-ardoise">Nom complet</p><p class="font-medium text-lagune">{{ $tenant->fullName }}</p></div>
            <div><p class="text-xs text-ardoise">Téléphone principal</p><p class="font-medium text-lagune">{{ $tenant->phone1 }}</p></div>
            @if($tenant->id_number)
                <div><p class="text-xs text-ardoise">N° CNI</p><p class="font-medium text-lagune">{{ $tenant->id_number }}</p></div>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('tenant-portal.profile.update') }}">
        @csrf
        @method('PATCH')

        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <p class="text-xs text-ardoise mb-3">Informations modifiables</p>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-lagune mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $tenant->email) }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 @error('email') border-red-400 @enderror">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-lagune mb-1">Téléphone secondaire</label>
                        <input type="tel" name="phone2" value="{{ old('phone2', $tenant->phone2) }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-lagune mb-1">Profession</label>
                        <input type="text" name="occupation" value="{{ old('occupation', $tenant->occupation) }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-lagune text-white py-3 rounded-2xl font-medium">
                Enregistrer les modifications
            </button>
        </div>
    </form>

</x-layouts.tenant-portal>
