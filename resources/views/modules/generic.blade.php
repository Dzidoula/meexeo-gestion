<x-layouts.app :title="$module['label'].' — MASTERCLAYS'">
    <div style="font-family:-apple-system,'Helvetica Neue',Helvetica,Arial,sans-serif;color:var(--color-mc-ink)">
        <div class="flex items-center justify-between gap-4" style="margin-bottom:20px">
            <div class="flex items-center gap-3.5">
                <div class="flex items-center justify-center text-white font-extrabold text-sm"
                     style="width:46px;height:46px;border-radius:11px;background:{{ $module['color'] }}">{{ $module['icon'] }}</div>
                <div>
                    <div style="font-size:20px;font-weight:800">{{ $module['label'] }}</div>
                    <div style="font-size:13px;color:var(--color-mc-ink-soft)">{{ $module['sub'] }}</div>
                </div>
            </div>
            <button type="button" disabled title="Bientôt disponible"
                    class="inline-flex min-h-[44px] items-center justify-center text-white font-bold text-sm"
                    style="padding:0 18px;border-radius:var(--radius-mc-sm);border:none;background:{{ $module['color'] }};opacity:.6">
                + {{ $module['addLabel'] }}
            </button>
        </div>

        <div class="grid gap-3.5" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));margin-bottom:20px">
            @foreach ($module['kpis'] as $kpi)
                <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:16px">
                    <div style="font-size:12px;color:var(--color-mc-ink-faint);font-weight:600">{{ $kpi['label'] }}</div>
                    <div style="font-size:20px;font-weight:800;margin-top:4px">{{ $kpi['value'] }}</div>
                </div>
            @endforeach
        </div>

        <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);overflow:hidden">
            <div class="grid" style="grid-template-columns:repeat({{ count($module['columns']) }}, 1fr) 0.8fr;padding:14px 20px;background:var(--color-mc-table-head);border-bottom:1px solid var(--color-mc-border)">
                @foreach ($module['columns'] as $column)
                    <div style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">{{ strtoupper($column) }}</div>
                @endforeach
                <div style="font-size:11.5px;font-weight:700;color:var(--color-mc-ink-soft);letter-spacing:.4px">STATUT</div>
            </div>
            @foreach ($rows as $row)
                <div class="grid items-center" style="grid-template-columns:repeat({{ count($module['columns']) }}, 1fr) 0.8fr;padding:14px 20px;border-bottom:1px solid var(--color-mc-border-soft)">
                    @foreach ($row['cells'] as $cell)
                        <div style="font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;padding-right:8px">{{ $cell }}</div>
                    @endforeach
                    <div>
                        <span style="display:inline-block;padding:5px 10px;border-radius:9999px;font-size:11.5px;font-weight:700;background:{{ $row['pill_color'] }}1F;color:{{ $row['pill_color'] }}">
                            {{ $row['status'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
