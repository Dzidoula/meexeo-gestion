<x-layouts.public title="Créer un compte — SONOR LOCATION">
    <div class="mx-auto max-w-md px-6 py-12">
        <h1 style="font-size:24px;font-weight:800">Créer un compte</h1>
        <p class="mt-1 text-sm" style="color:var(--color-sonor-ink-soft)">Créez votre compte pour gérer votre panier et vos commandes.</p>

        <form method="POST" action="{{ route('customer.register.store') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="name" style="font-size:12px;font-weight:700;color:var(--color-sonor-ink-soft)">Nom complet</label>
                <input id="name" name="name" value="{{ old('name') }}" required autofocus
                       class="mt-1.5 min-h-[44px] w-full" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border);padding:0 12px;font-size:13px">
            </div>
            <div>
                <label for="phone" style="font-size:12px;font-weight:700;color:var(--color-sonor-ink-soft)">Téléphone</label>
                <input id="phone" name="phone" value="{{ old('phone') }}" required
                       class="mt-1.5 min-h-[44px] w-full" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border);padding:0 12px;font-size:13px">
            </div>
            <div>
                <label for="email" style="font-size:12px;font-weight:700;color:var(--color-sonor-ink-soft)">Adresse email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                       class="mt-1.5 min-h-[44px] w-full" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border);padding:0 12px;font-size:13px">
            </div>
            <div>
                <label for="password" style="font-size:12px;font-weight:700;color:var(--color-sonor-ink-soft)">Mot de passe</label>
                <input id="password" name="password" type="password" required
                       class="mt-1.5 min-h-[44px] w-full" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border);padding:0 12px;font-size:13px">
            </div>
            <div>
                <label for="password_confirmation" style="font-size:12px;font-weight:700;color:var(--color-sonor-ink-soft)">Confirmer le mot de passe</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="mt-1.5 min-h-[44px] w-full" style="border-radius:var(--radius-sonor-sm);border:1px solid var(--color-sonor-border);padding:0 12px;font-size:13px">
            </div>

            @if ($errors->any())
                <p class="text-sm" style="color:#B4622F">{{ $errors->first() }}</p>
            @endif

            <button type="submit" class="min-h-[44px] w-full text-sm font-semibold" style="border-radius:var(--radius-sonor-sm);border:none;background:var(--color-sonor-yellow);color:var(--color-sonor-navy)">
                Créer mon compte
            </button>
        </form>

        <p class="mt-4 text-sm" style="color:var(--color-sonor-ink-soft)">
            Déjà un compte ? <a href="{{ route('customer.login') }}" style="color:var(--color-sonor-ink);font-weight:700">Se connecter</a>
        </p>
    </div>
</x-layouts.public>
