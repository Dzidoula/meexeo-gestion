<x-layouts.app title="Paramètres — MASTERCLAYS">
    <div style="font-family:-apple-system,'Helvetica Neue',Helvetica,Arial,sans-serif;color:var(--color-mc-ink)"
         x-data="{ toggles: { emailNotif: true, smsNotif: false } }">
        <div style="font-size:20px;font-weight:800;margin-bottom:20px">Paramètres</div>
        <div class="grid" style="grid-template-columns:1fr 1fr;gap:20px">
            <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:20px">
                <div style="font-size:14.5px;font-weight:800;margin-bottom:14px">Général</div>
                <div class="flex justify-between" style="padding:10px 0;border-bottom:1px solid var(--color-mc-border-soft);font-size:13px">
                    <span style="color:var(--color-mc-ink-soft)">Nom de l'entreprise</span><span style="font-weight:700">MASTERCLAYS</span>
                </div>
                <div class="flex justify-between" style="padding:10px 0;border-bottom:1px solid var(--color-mc-border-soft);font-size:13px">
                    <span style="color:var(--color-mc-ink-soft)">Devise</span><span style="font-weight:700">FCFA</span>
                </div>
                <div class="flex justify-between" style="padding:10px 0;font-size:13px">
                    <span style="color:var(--color-mc-ink-soft)">Fuseau horaire</span><span style="font-weight:700">GMT (Abidjan)</span>
                </div>
            </div>
            <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:20px">
                <div style="font-size:14.5px;font-weight:800;margin-bottom:14px">Notifications</div>
                @foreach ([['emailNotif', 'Notifications par e-mail'], ['smsNotif', 'Notifications par SMS']] as [$key, $label])
                    <div class="flex items-center justify-between" style="padding:10px 0;border-bottom:1px solid var(--color-mc-border-soft)">
                        <div style="font-size:13px;font-weight:600">{{ $label }}</div>
                        <div style="min-height:44px;display:flex;align-items:center">
                            <button type="button" role="switch" :aria-checked="toggles.{{ $key }}" @click="toggles.{{ $key }} = ! toggles.{{ $key }}"
                                    class="min-h-[24px]" style="width:40px;height:22px;border-radius:11px;padding:2px;cursor:pointer;display:flex"
                                    :style="`background:${toggles.{{ $key }} ? 'var(--color-mc-accent)' : '#D9DBE6'};justify-content:${toggles.{{ $key }} ? 'flex-end' : 'flex-start'}`">
                                <span style="width:18px;height:18px;border-radius:50%;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.25)"></span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
