<x-layouts.app title="Codes promo — MASTERCLAYS">
    <x-page-header title="Codes promo">
        <x-slot:actions>
            <a href="{{ route('hotel-promo-codes.create') }}" class="min-h-[44px] inline-flex items-center px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Ajouter un code</a>
        </x-slot:actions>
    </x-page-header>

    @if (session('status'))
        <p class="mt-4 text-sm" style="color:var(--color-mc-success)">{{ session('status') }}</p>
    @endif

    <div class="mt-6 overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr style="color:var(--color-mc-ink-soft)"><th class="text-left">Code</th><th class="text-left">Remise</th><th class="text-left">Utilisations</th><th class="text-left">Statut</th><th></th></tr></thead>
            <tbody>
                @foreach ($promoCodes as $promoCode)
                    <tr style="border-top:1px solid var(--color-mc-border)">
                        <td class="py-2">{{ $promoCode->code }}</td>
                        <td>{{ $promoCode->discount_type === 'percent' ? $promoCode->discount_value.'%' : \App\Support\Money::fcfa($promoCode->discount_value) }}</td>
                        <td>{{ $promoCode->uses_count }}@if($promoCode->max_uses) / {{ $promoCode->max_uses }} @endif</td>
                        <td>{{ $promoCode->is_active ? 'Actif' : 'Inactif' }}</td>
                        <td>
                            <a href="{{ route('hotel-promo-codes.edit', $promoCode) }}" class="text-xs font-semibold" style="color:var(--color-mc-accent)">Modifier</a>
                            <form method="POST" action="{{ route('hotel-promo-codes.toggle-status', $promoCode) }}" class="inline">
                                @csrf @method('PATCH')
                                <button class="ml-2 text-xs font-semibold" style="color:var(--color-mc-ink-soft)">{{ $promoCode->is_active ? 'Désactiver' : 'Activer' }}</button>
                            </form>
                            <form method="POST" action="{{ route('hotel-promo-codes.destroy', $promoCode) }}" class="inline">
                                @csrf @method('DELETE')
                                <button class="ml-2 text-xs" style="color:var(--color-mc-danger)">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $promoCodes->links() }}</div>
</x-layouts.app>
