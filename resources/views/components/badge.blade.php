@props([
    'variant' => 'default',
    'size' => 'md',
])

@php
$baseClasses = 'status-badge';

$variantMap = [
    'default' => 'pendiente',
    'pendiente' => 'pendiente',
    'confirmado' => 'confirmado',
    'en_carga' => 'en_carga',
    'en_transito' => 'en_transito',
    'entregado' => 'entregado',
    'facturado' => 'facturado',
    'cancelado' => 'cancelado',
    'alta' => 'alta',
    'aprobado' => 'alta',
    'disponible' => 'disponible',
    'reservada' => 'reservada',
    'en_uso' => 'en_uso',
    'no_disponible' => 'no_disponible',
];

$variantClass = $variantMap[$variant] ?? 'pendiente';
$classes = $baseClasses . ' ' . $variantClass;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
