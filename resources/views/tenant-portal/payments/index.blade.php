<x-layouts.tenant-portal title="Mes paiements">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-lagune">Mes paiements</h1>
        <a href="{{ route('tenant-portal.payments.create') }}"
           class="bg-lagune text-white px-4 py-2 rounded-xl text-sm font-medium">
            + Payer
        </a>
    </div>

    <div class="space-y-3">
        @forelse($payments as $payment)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <p class="font-medium text-lagune">
                            {{ ucfirst($payment->month->isoFormat('MMMM YYYY')) }}
                        </p>
                        <p class="text-xs text-ardoise mt-0.5">
                            Ref : {{ $payment->reference ?? '—' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-lagune">{{ number_format($payment->amount, 0, ',', ' ') }} F</p>
                        <p class="text-xs text-ardoise">{{ \Carbon\Carbon::parse($payment->paid_on)->format('d/m/Y') }}</p>
                    </div>
                </div>

                @if($payment->proof_path)
                    <div class="pt-2 border-t border-gray-50">
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($payment->proof_path) }}"
                           target="_blank"
                           class="text-xs text-acier hover:underline flex items-center gap-1">
                            📎 Voir la preuve
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12 text-ardoise">
                <p class="text-4xl mb-3">💳</p>
                <p>Aucun paiement enregistré.</p>
                <a href="{{ route('tenant-portal.payments.create') }}" class="mt-3 inline-block text-acier text-sm">
                    Effectuer un paiement →
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $payments->links() }}</div>

</x-layouts.tenant-portal>
