@php use App\Support\Money; @endphp

<x-layouts.app title="Preuves de paiement — MEEXEO">
    <x-page-header title="Preuves de paiement"
                   subtitle="{{ $pending->count() }} preuve(s) envoyée(s) par les locataires, en attente de vérification." />
    @include('locative._subnav')

    @if(session('status'))
        <div class="mt-6" style="border-radius:var(--radius-mc);background:var(--color-ok-bg);border:1px solid var(--color-ok-bord);padding:12px 16px">
            <p style="font-size:13px;color:var(--color-ok-texte)">{{ session('status') }}</p>
        </div>
    @endif

    @error('reason')
        <div class="mt-6" style="border-radius:var(--radius-mc);background:var(--color-impaye-bg);border:1px solid var(--color-impaye-bord);padding:12px 16px">
            <p style="font-size:13px;color:var(--color-impaye-texte)">{{ $message }}</p>
        </div>
    @enderror

    {{-- ===== En attente ===== --}}
    <div class="mt-6 space-y-3">
        @forelse($pending as $payment)
            @php $tenant = $payment->lease->tenant @endphp

            <div x-data="{ refus: false }"
                 style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:18px">

                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ route('tenants.show', $tenant) }}"
                               style="font-size:14px;font-weight:700;color:var(--color-mc-ink)">{{ $tenant->fullName }}</a>
                            <span style="font-size:12px;color:var(--color-mc-ink-faint)">·</span>
                            <span style="font-size:12.5px;color:var(--color-mc-ink-soft)">{{ $payment->lease->property->title }}</span>
                        </div>
                        <p class="mt-1" style="font-size:12.5px;color:var(--color-mc-ink-soft)">
                            Mois : <strong>{{ ucfirst($payment->month->isoFormat('MMMM YYYY')) }}</strong>
                            · Déclaré le {{ $payment->created_at->format('d/m/Y à H:i') }}
                            · {{ $tenant->phone1 }}
                        </p>
                        <p class="mt-2 chiffre" style="font-size:20px;font-weight:800;color:var(--color-mc-ink)">
                            {{ Money::fcfa($payment->amount) }}
                        </p>
                        @if($payment->amount < $payment->lease->monthly_rent)
                            <p class="mt-0.5" style="font-size:12px;color:var(--color-part-texte)">
                                Inférieur au loyer de {{ Money::fcfa((int) $payment->lease->monthly_rent) }}
                                — validera un paiement partiel.
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if($payment->proof_path)
                            <a href="{{ route('portal-proofs.file', $payment) }}" target="_blank" rel="noopener"
                               class="inline-flex min-h-[40px] items-center"
                               style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);padding:0 14px;font-size:13px;font-weight:600;color:var(--color-mc-ink)">
                                Voir la preuve
                            </a>
                        @endif

                        <form method="POST" action="{{ route('portal-proofs.approve', $payment) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex min-h-[40px] items-center"
                                    style="border-radius:var(--radius-mc-sm);background:var(--color-mc-success);padding:0 16px;font-size:13px;font-weight:700;color:#fff">
                                Valider
                            </button>
                        </form>

                        <button type="button" @click="refus = ! refus" class="inline-flex min-h-[40px] items-center"
                                style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);padding:0 14px;font-size:13px;font-weight:600;color:var(--color-mc-danger)">
                            Refuser
                        </button>
                    </div>
                </div>

                {{-- Un refus sans motif laisserait le locataire sans rien comprendre. --}}
                <form x-show="refus" x-cloak method="POST" action="{{ route('portal-proofs.reject', $payment) }}"
                      class="mt-4 flex flex-wrap items-end gap-2"
                      style="border-top:1px solid var(--color-mc-border-soft);padding-top:14px">
                    @csrf @method('PATCH')
                    <div class="min-w-[260px] flex-1">
                        <label style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">
                            Motif du refus — il sera affiché au locataire
                        </label>
                        <input type="text" name="reason" required minlength="5"
                               placeholder="Reçu illisible, montant ne correspond pas, mois erroné…"
                               class="mt-1.5 min-h-[40px] w-full"
                               style="border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);padding:0 12px;font-size:13px;color:var(--color-mc-ink)">
                    </div>
                    <button type="submit" class="inline-flex min-h-[40px] items-center"
                            style="border-radius:var(--radius-mc-sm);background:var(--color-mc-danger);padding:0 16px;font-size:13px;font-weight:700;color:#fff">
                        Confirmer le refus
                    </button>
                </form>
            </div>
        @empty
            <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:48px;text-align:center">
                <p style="font-size:14px;font-weight:600;color:var(--color-mc-ink)">Aucune preuve en attente</p>
                <p class="mt-1" style="font-size:13px;color:var(--color-mc-ink-soft)">
                    Les preuves envoyées depuis le portail locataire apparaîtront ici.
                </p>
            </div>
        @endforelse
    </div>

    {{-- ===== Refusées récemment ===== --}}
    @if($recent->isNotEmpty())
        <div class="mt-8">
            <h2 style="font-size:13px;font-weight:700;color:var(--color-mc-ink-soft)">Refusées récemment</h2>
            <div class="mt-3 space-y-2">
                @foreach($recent as $payment)
                    <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:14px 16px">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p style="font-size:13px;font-weight:600;color:var(--color-mc-ink)">
                                    {{ $payment->lease->tenant->fullName }} ·
                                    {{ ucfirst($payment->month->isoFormat('MMMM YYYY')) }} ·
                                    <span class="chiffre">{{ Money::fcfa($payment->amount) }}</span>
                                </p>
                                <p class="mt-0.5" style="font-size:12px;color:var(--color-mc-ink-soft)">{{ $payment->notes }}</p>
                            </div>
                            <span style="border-radius:999px;background:var(--color-impaye-bg);color:var(--color-impaye-texte);padding:3px 10px;font-size:11.5px;font-weight:700">
                                Refusée
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.app>
