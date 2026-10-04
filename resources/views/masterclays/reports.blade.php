<x-layouts.app title="Rapports & Statistiques — MASTERCLAYS">
    <div style="color:var(--color-mc-ink)">
        <h1 class="font-titre text-3xl text-lagune" style="margin-bottom:4px">Rapports & Statistiques</h1>
        <div style="font-size:13px;color:var(--color-mc-ink-soft);margin-bottom:20px">Préparez et exportez vos rapports par module, au format PDF ou Excel.</div>
        <div class="grid" style="grid-template-columns:repeat(3,1fr);gap:14px">
            @foreach ($cards as $card)
                <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:18px">
                    <div style="width:36px;height:36px;border-radius:var(--radius-mc-sm);background:{{ $card['color'] }};margin-bottom:12px"></div>
                    <div style="font-size:14.5px;font-weight:800;margin-bottom:12px">Rapport {{ $card['label'] }}</div>
                    <div class="flex" style="gap:8px">
                        <button type="button" disabled title="Bientôt disponible" class="min-h-[44px]" style="flex:1;padding:9px;border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);font-size:12px;font-weight:700;opacity:.6">Exporter PDF</button>
                        <button type="button" disabled title="Bientôt disponible" class="min-h-[44px]" style="flex:1;padding:9px;border-radius:var(--radius-mc-sm);border:1px solid var(--color-mc-border);background:var(--color-mc-surface);font-size:12px;font-weight:700;opacity:.6">Exporter Excel</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
