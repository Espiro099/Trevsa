@props([
    'type' => 'text',
    'label' => '',
    'error' => '',
])

<div>
    @if($label)
        <label class="form-label">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        <input 
            type="{{ $type }}"
            {{ $attributes->merge([
                'class' => 'form-input ' . ($error ? 'border-red-500 focus:ring-red-500' : '')
            ]) }}
        >
        
        @if($attributes->has('icon'))
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <i class="{{ $attributes->get('icon') }} text-gray-400"></i>
            </div>
        @endif
    </div>

    @if($error)
        <p class="form-error">{{ $error }}</p>
    @endif
</div>