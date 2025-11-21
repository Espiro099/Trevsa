@props([
    'title' => '',
    'footer' => '',
    'noPadding' => false,
])

<div {{ $attributes->merge(['class' => 'glass-card overflow-hidden']) }}>
    @if($title)
        <div class="modern-card-header">
            <h3 class="text-lg font-semibold text-text-primary">{{ $title }}</h3>
        </div>
    @endif

    <div class="{{ $noPadding ? '' : 'p-6' }}">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="modern-card-header">
            {{ $footer }}
        </div>
    @endif
</div>