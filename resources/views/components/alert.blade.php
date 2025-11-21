@props([
    'type' => 'info', // success, warning, error, info
    'title' => '',
    'dismissible' => false,
])

@php
$typeClasses = [
    'success' => 'alert alert-success',
    'warning' => 'alert alert-warning', 
    'error' => 'alert alert-error',
    'info' => 'alert alert-info',
];

$iconClasses = [
    'success' => 'M5 13l4 4L19 7',
    'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    'error' => 'M6 18L18 6M6 6l12 12',
    'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
];

$classes = $typeClasses[$type] ?? $typeClasses['info'];
$iconPath = $iconClasses[$type] ?? $iconClasses['info'];
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
            </svg>
        </div>
        <div class="ml-3 flex-1">
            @if($title)
                <h3 class="text-sm font-medium">
                    {{ $title }}
                </h3>
            @endif
            <div class="{{ $title ? 'mt-2' : '' }}">
                <p class="text-sm">
                    {{ $slot }}
                </p>
            </div>
        </div>
        @if($dismissible)
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2">
                        <span class="sr-only">Dismiss</span>
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>