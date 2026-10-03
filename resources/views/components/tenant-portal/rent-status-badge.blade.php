@props(['status'])

@php
$styles = match($status) {
    'paid'    => 'bg-green-100 text-green-800',
    'upcoming'=> 'bg-blue-100 text-blue-800',
    'late'    => 'bg-orange-100 text-orange-800',
    'unpaid'  => 'bg-red-100 text-red-800',
    'partial' => 'bg-yellow-100 text-yellow-800',
    'pending' => 'bg-amber-100 text-amber-800',
    default   => 'bg-gray-100 text-gray-700',
};
$labels = match($status) {
    'paid'    => 'Payé',
    'upcoming'=> 'À venir',
    'late'    => 'En retard',
    'unpaid'  => 'Impayé',
    'partial' => 'Partiel',
    'pending' => 'En attente de vérification',
    default   => $status,
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $styles"]) }}>
    {{ $labels }}
</span>
