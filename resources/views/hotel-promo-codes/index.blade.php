<x-layouts.app title="Codes promo — MASTERCLAYS">
    <x-page-header title="Codes promo">
        <x-slot:actions>
            <a href="{{ route('hotel-promo-codes.create') }}" class="min-h-[44px] inline-flex items-center px-4 text-sm font-semibold" style="border-radius:var(--radius-mc-sm);background:var(--color-mc-accent);color:var(--color-mc-on-accent)">Ajouter un code</a>
        </x-slot:actions>
    </x-page-header>
    @include('hotel._subnav')

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
                            <a href="{{ route('hotel-promo-codes.edit', $promoCode) }}" title="Modifier" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[color:var(--color-mc-ink-faint)] hover:bg-[var(--color-mc-canvas)] hover:text-[color:var(--color-mc-accent)]">
                                <x-mc-icon name="pencil" class="h-4 w-4" />
                            </a>
                            <form method="POST" action="{{ route('hotel-promo-codes.toggle-status', $promoCode) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $promoCode->is_active ? 'Désactiver' : 'Activer' }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[color:var(--color-mc-ink-faint)] hover:bg-[var(--color-mc-canvas)] hover:text-[color:var(--color-mc-accent)]">
                                    <x-mc-icon name="power" class="h-4 w-4" />
                                </button>
                            </form>
                            <form method="POST" action="{{ route('hotel-promo-codes.destroy', $promoCode) }}" class="inline" onsubmit="return confirm('Supprimer définitivement ce code promo ?')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Supprimer" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[color:var(--color-mc-danger)] hover:bg-red-50">
                                    <x-mc-icon name="trash-2" class="h-4 w-4" />
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $promoCodes->links() }}</div>
</x-layouts.app>
