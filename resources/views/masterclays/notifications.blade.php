<x-layouts.app title="Notifications — MASTERCLAYS">
    <div style="font-family:-apple-system,'Helvetica Neue',Helvetica,Arial,sans-serif;color:var(--color-mc-ink)">
        <div style="font-size:20px;font-weight:800;margin-bottom:20px">Notifications & Alertes</div>
        <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc)">
            @foreach ($items as $item)
                <div class="flex items-start" style="gap:14px;padding:16px 20px;border-bottom:1px solid var(--color-mc-border-soft)">
                    <div style="width:34px;height:34px;border-radius:var(--radius-mc-sm);background:{{ $item['color'] }};flex:none;margin-top:2px"></div>
                    <div style="flex:1">
                        <div style="font-size:13.5px;font-weight:700">{{ $item['title'] }}</div>
                        <div style="font-size:12.5px;color:var(--color-mc-ink-soft);margin-top:2px">{{ $item['module'] }} · {{ $item['detail'] }}</div>
                    </div>
                    <div style="font-size:11.5px;color:var(--color-mc-ink-quiet);flex:none">{{ $item['time'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
