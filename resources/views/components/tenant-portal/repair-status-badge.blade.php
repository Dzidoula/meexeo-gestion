@props(['status'])

@php
$styles = match($status) {
    'recu'     => 'bg-blue-100 text-blue-800',
    'en_cours' => 'bg-orange-100 text-orange-800',
    'repare'   => 'bg-green-100 text-green-800',
    'cloture'  => 'bg-gray-100 text-gray-700',
    default    => 'bg-gray-100 text-gray-700',
};
$labels = match($status) {
    'recu'     => 'Reçu',
    'en_cours' => 'En cours',
    'repare'   => 'Réparé',
    'cloture'  => 'Clôturé',
    default    => $status,
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $styles"]) }}>
    {{ $labels }}
</span>
