<x-layouts.app title="Sécurité — MASTERCLAYS">
    <div style="font-family:-apple-system,'Helvetica Neue',Helvetica,Arial,sans-serif;color:var(--color-mc-ink)"
         x-data="{ toggles: { tfa: true, lockout: true, logging: true } }">
        <div style="font-size:20px;font-weight:800;margin-bottom:20px">Sécurité</div>
        <div class="grid" style="grid-template-columns:1fr 1fr;gap:20px">
            <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:20px">
                <div style="font-size:14.5px;font-weight:800;margin-bottom:14px">Options de sécurité</div>
                @foreach ([['tfa', 'Authentification à deux facteurs'], ['lockout', 'Verrouillage après échecs de connexion'], ['logging', 'Journalisation des actions']] as [$key, $label])
                    <div class="flex items-center justify-between" style="padding:12px 0;border-bottom:1px solid var(--color-mc-border-soft)">
                        <div style="font-size:13px;font-weight:600">{{ $label }}</div>
                        <button type="button" role="switch" :aria-checked="toggles.{{ $key }}" @click="toggles.{{ $key }} = ! toggles.{{ $key }}"
                                class="min-h-[24px]" style="width:40px;height:22px;border-radius:11px;padding:2px;cursor:pointer;display:flex"
                                :style="`background:${toggles.{{ $key }} ? 'var(--color-mc-accent)' : '#D9DBE6'};justify-content:${toggles.{{ $key }} ? 'flex-end' : 'flex-start'}`">
                            <span style="width:18px;height:18px;border-radius:50%;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,.25)"></span>
                        </button>
                    </div>
                @endforeach
            </div>
            <div style="background:var(--color-mc-surface);border:1px solid var(--color-mc-border);border-radius:var(--radius-mc);padding:20px">
                <div style="font-size:14.5px;font-weight:800;margin-bottom:14px">Journal des actions</div>
                @foreach ($log as $entry)
                    <div style="padding:10px 0;border-bottom:1px solid var(--color-mc-border-soft)">
                        <div style="font-size:13px;font-weight:600">{{ $entry['action'] }}</div>
                        <div style="font-size:11.5px;color:var(--color-mc-ink-quiet);margin-top:2px">{{ $entry['user'] }} · {{ $entry['date'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
