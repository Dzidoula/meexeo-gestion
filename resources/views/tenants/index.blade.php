{{-- resources/views/tenants/index.blade.php --}}
<x-layouts.app title="Locataires — MEEXEO Immobilier">
    <x-page-header title="Locataires" subtitle="{{ $tenants->total() }} locataire(s) enregistré(s)">
        <x-slot:actions>
            {{-- TASK11-NEUTRALIZED: route('tenants.create') n'existe pas avant la Task 12. --}}
            <a href="#"
               class="inline-flex min-h-[44px] items-center rounded-meexeo bg-cuivre px-4 text-sm font-semibold text-papier">
                Ajouter un locataire
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="mt-6 flex flex-wrap items-end gap-3">
        <div class="min-w-[220px] flex-1">
            <label for="q" class="text-xs font-semibold text-ardoise">Recherche</label>
            <input id="q" name="q" value="{{ request('q') }}" placeholder="Nom, téléphone, CNI…"
                   class="mt-1.5 min-h-[44px] w-full rounded-meexeo border border-lin bg-papier px-3 text-sm">
        </div>
        <div>
            <label for="status" class="text-xs font-semibold text-ardoise">Statut</label>
            <select id="status" name="status" class="mt-1.5 min-h-[44px] rounded-meexeo border border-lin bg-papier px-3 text-sm">
                <option value="">Tous</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="min-h-[44px] rounded-meexeo bg-lagune px-4 text-sm font-semibold text-sable">Filtrer</button>
    </form>

    @if ($tenants->isEmpty())
        <p class="mt-8 rounded-meexeo border border-lin-clair bg-papier p-8 text-center text-sm text-brume">
            Aucun locataire ne correspond à cette recherche.
        </p>
    @else
        <div class="mt-6 divide-y divide-lin-pale rounded-meexeo border border-lin-clair bg-papier">
            @foreach ($tenants as $tenant)
                <div class="flex flex-wrap items-center justify-between gap-4 p-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-sable font-titre text-sm text-lagune">
                            {{ $tenant->initials }}
                        </span>
                        <div>
                            {{-- TASK11-NEUTRALIZED: route('tenants.show', $tenant) n'existe pas avant la Task 12. --}}
                            <a href="#" class="font-titre text-base hover:text-cuivre">
                                {{ $tenant->full_name }}
                            </a>
                            <p class="chiffre text-[11px] text-brume">{{ $tenant->reference }} · {{ $tenant->phone1 }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="hidden text-xs text-ardoise sm:block">{{ $tenant->occupation ?? '—' }}</span>
                        <x-status-badge :status="$tenant->status->value" />
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $tenants->links() }}</div>
    @endif
</x-layouts.app>
