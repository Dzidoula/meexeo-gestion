<x-layouts.public :title="$title.' — SONOR LOCATION'">
    <div class="mx-auto max-w-2xl px-6 py-24 text-center">
        <div class="mx-auto flex h-20 w-20 items-center justify-center" style="border-radius:var(--radius-sonor);background:linear-gradient(155deg, var(--color-sonor-navy), var(--color-sonor-navy-soft));color:var(--color-sonor-yellow);box-shadow:var(--shadow-sonor-card)">
            <x-sonor-icon :name="$icon" class="h-10 w-10" />
        </div>
        <p class="mt-6" style="font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--color-sonor-yellow)">Bientôt disponible</p>
        <h1 class="mt-3" style="font-size:32px;font-weight:800">{{ $title }}</h1>
        <p class="mt-4" style="font-size:15px;color:var(--color-sonor-ink-soft)">{{ $description }}</p>
        <a href="{{ route('public.home') }}" class="mt-8 inline-flex min-h-[44px] items-center px-6" style="border-radius:var(--radius-sonor-sm);background:var(--color-sonor-yellow);color:var(--color-sonor-navy);font-weight:700;font-size:14px">
            Retour à l'accueil
        </a>
    </div>
</x-layouts.public>
