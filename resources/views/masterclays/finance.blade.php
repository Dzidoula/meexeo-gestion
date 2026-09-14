<x-layouts.app title="Comptabilité — MASTERCLAYS">
    <div style="font-family:-apple-system,'Helvetica Neue',Helvetica,Arial,sans-serif;color:var(--color-mc-ink)">
        <div style="font-size:20px;font-weight:800;margin-bottom:4px">Comptabilité</div>
        <div style="font-size:13px;color:var(--color-mc-ink-soft);margin-bottom:20px">Finances & rapports consolidés de toutes les activités.</div>
        <div class="grid" style="grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px">
            <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:18px">
                <div style="font-size:12px;color:var(--color-mc-ink-faint);font-weight:600">Revenus totaux</div>
                <div style="font-size:22px;font-weight:800;color:var(--color-mc-success);margin-top:4px">12 450 000 FCFA</div>
            </div>
            <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:18px">
                <div style="font-size:12px;color:var(--color-mc-ink-faint);font-weight:600">Dépenses totales</div>
                <div style="font-size:22px;font-weight:800;color:var(--color-mc-danger);margin-top:4px">3 200 000 FCFA</div>
            </div>
            <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:18px">
                <div style="font-size:12px;color:var(--color-mc-ink-faint);font-weight:600">Bénéfice net</div>
                <div style="font-size:22px;font-weight:800;margin-top:4px">9 250 000 FCFA</div>
            </div>
        </div>
        <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);overflow:hidden">
            <div class="grid" style="grid-template-columns:1fr 1.6fr 1.2fr 1fr 1fr;padding:14px 20px;background:var(--color-mc-table-head);border-bottom:1px solid var(--color-mc-border)">
                <div style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">DATE</div>
                <div style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">LIBELLÉ</div>
                <div style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">MODULE</div>
                <div style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">TYPE</div>
                <div style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft)">MONTANT</div>
            </div>
            @foreach ($rows as $row)
                <div class="grid items-center" style="grid-template-columns:1fr 1.6fr 1.2fr 1fr 1fr;padding:14px 20px;border-bottom:1px solid var(--color-mc-border-soft)">
                    <div style="font-size:13px;color:var(--color-mc-ink-soft)">{{ $row['date'] }}</div>
                    <div style="font-size:13px;font-weight:600">{{ $row['label'] }}</div>
                    <div style="font-size:13px;color:var(--color-mc-ink-soft)">{{ $row['module'] }}</div>
                    <div style="font-size:13px;color:var(--color-mc-ink-soft)">{{ $row['type'] }}</div>
                    <div style="font-size:13px;font-weight:700;color:{{ $row['up'] ? 'var(--color-mc-success)' : 'var(--color-mc-danger)' }}">{{ $row['amount'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
