<x-layouts.app title="Utilisateurs & Permissions — MASTERCLAYS">
    <div style="font-family:-apple-system,'Helvetica Neue',Helvetica,Arial,sans-serif;color:var(--color-mc-ink)">
        <div style="font-size:20px;font-weight:800;margin-bottom:4px">Utilisateurs & Permissions</div>
        <div style="font-size:13px;color:var(--color-mc-ink-soft);margin-bottom:20px">Comptes, rôles et droits d'accès par module.</div>
        <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);overflow:hidden">
            <div class="grid" style="grid-template-columns:1.2fr 1fr 1.6fr 0.8fr;padding:14px 20px;background:var(--color-mc-table-head);border-bottom:1px solid var(--color-mc-border)">
                <div style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">NOM</div>
                <div style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">RÔLE</div>
                <div style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">MODULES ACCESSIBLES</div>
                <div style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">STATUT</div>
            </div>
            @foreach ($users as $user)
                <div class="grid items-center" style="grid-template-columns:1.2fr 1fr 1.6fr 0.8fr;padding:14px 20px;border-bottom:1px solid var(--color-mc-border-soft)">
                    <div style="font-size:13px;font-weight:700">{{ $user['name'] }}</div>
                    <div style="font-size:13px;color:var(--color-mc-ink-soft)">{{ $user['role'] }}</div>
                    <div style="font-size:13px;color:var(--color-mc-ink-soft)">{{ $user['access'] }}</div>
                    <div>
                        <span style="display:inline-block;padding:5px 10px;border-radius:9999px;font-size:11.5px;font-weight:700;background:{{ $user['pill_color'] }}1F;color:{{ $user['pill_color'] }}">{{ $user['status'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
