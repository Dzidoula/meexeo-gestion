<x-layouts.public title="Mon compte — SONOR LOCATION">
    <div class="mx-auto max-w-2xl px-6 py-12">
        <h1 style="font-size:24px;font-weight:800">Mon compte</h1>

        <dl class="mt-6 grid grid-cols-1 gap-4 border-t pt-5 text-sm sm:grid-cols-2" style="border-color:var(--color-sonor-border)">
            <div><dt style="color:var(--color-sonor-ink-soft)">Nom</dt><dd class="mt-0.5 font-semibold">{{ $customer->name }}</dd></div>
            <div><dt style="color:var(--color-sonor-ink-soft)">Téléphone</dt><dd class="mt-0.5 font-semibold">{{ $customer->phone }}</dd></div>
            <div><dt style="color:var(--color-sonor-ink-soft)">Email</dt><dd class="mt-0.5 font-semibold">{{ $customer->email }}</dd></div>
        </dl>

        <form method="POST" action="{{ route('customer.logout') }}" class="mt-8">
            @csrf
            <button type="submit" class="min-h-[44px] text-sm font-semibold" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border);background:transparent;padding:0 18px">
                Se déconnecter
            </button>
        </form>
    </div>
</x-layouts.public>
