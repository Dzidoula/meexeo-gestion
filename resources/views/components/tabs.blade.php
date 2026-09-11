{{-- resources/views/components/tabs.blade.php --}}
@props(['tabs', 'initial' => null])
@php($first = array_key_first($tabs))
{{-- Le composant reste générique : c'est l'appelant qui sait quel onglet contient
     l'erreur de validation à révéler ; on retombe sur le premier onglet sinon. --}}
@php($active = ($initial && array_key_exists($initial, $tabs)) ? $initial : $first)
<div x-data="{ onglet: '{{ $active }}' }" class="mt-6">
    <div class="flex flex-wrap gap-1 border-b border-lin-clair" role="tablist">
        @foreach ($tabs as $key => $label)
            <button type="button" role="tab"
                    @click="onglet = '{{ $key }}'"
                    :aria-selected="onglet === '{{ $key }}'"
                    :class="onglet === '{{ $key }}'
                        ? 'border-cuivre text-lagune'
                        : 'border-transparent text-brume hover:text-ardoise'"
                    class="min-h-[44px] border-b-2 px-4 text-sm font-semibold">
                {{ $label }}
            </button>
        @endforeach
    </div>

    @foreach ($tabs as $key => $label)
        <div x-show="onglet === '{{ $key }}'" x-cloak role="tabpanel" class="pt-6">
            {{ ${'panel_'.$key} ?? '' }}
        </div>
    @endforeach
</div>
