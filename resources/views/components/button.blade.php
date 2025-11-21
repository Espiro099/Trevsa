@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, outline, ghost
    'size' => 'md', // sm, md, lg
])

@php
$href = $attributes->get('href');
$attributes = $attributes->except('href');

$baseClasses = 'inline-flex items-center justify-center rounded-xl font-semibold transition-all duration-300';
$baseClasses .= ' focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-bg-primary';
$baseClasses .= ' active:scale-[0.98] transform';

$variantClasses = [
    'primary' => 'btn-primary',
    'secondary' => 'btn-secondary',
    'outline' => 'btn-outline',
    'ghost' => 'btn-ghost',
];

$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-xs',
    'md' => 'px-6 py-3 text-sm',
    'lg' => 'px-8 py-4 text-base',
];

$classes = ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . 
          ($sizeClasses[$size] ?? $sizeClasses['md']);
@endphp

@if($href)
    <a 
        href="{{ $href }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        {{ $slot }}
    </a>
@else
    <button 
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        {{ $slot }}
    </button>
@endif
