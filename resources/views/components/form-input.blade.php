@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'placeholder' => '',
    'validation' => null, // Array de reglas de validación
])

@php
    $hasError = $errors->has($name) || (isset($validation) && $validation);
    $errorMessage = $errors->first($name);
@endphp

<div class="form-group" x-data="{ 
    touched: false,
    error: @js($errorMessage),
    value: @js(old($name, $value))
}">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-brand-red">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <input 
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @input="
                touched = true;
                value = $event.target.value;
                error = '';
                // Validación básica
                if ({{ $required ? 'true' : 'false' }} && !value.trim()) {
                    error = 'Este campo es requerido';
                } else if ('{{ $type }}' === 'email' && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    error = 'El email no es válido';
                } else if ('{{ $type }}' === 'tel' && value && !/^[\d\s\-\+\(\)]+$/.test(value)) {
                    error = 'El teléfono no es válido';
                }
            "
            @blur="touched = true"
            :class="
                (touched && error) || {{ $hasError ? 'true' : 'false' }} 
                    ? 'form-input error border-brand-red focus:ring-brand-red' 
                    : 'form-input'
            "
            {{ $attributes->except(['class', 'type', 'name', 'id', 'value', 'placeholder', 'required']) }}
            class="form-input {{ $hasError ? 'error border-brand-red focus:ring-brand-red' : '' }}"
        >
        
        <!-- Ícono de error -->
        <div 
            x-show="(touched && error) || {{ $hasError ? 'true' : 'false' }}"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
        >
            <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <!-- Ícono de éxito (cuando es válido) -->
        <div 
            x-show="touched && !error && value && value.length > 0"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
        >
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
    </div>

    <!-- Mensaje de error -->
    @if($errorMessage)
        <p class="form-error" x-show="true">
            {{ $errorMessage }}
        </p>
    @else
        <p 
            x-show="touched && error" 
            x-text="error"
            class="form-error"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
        ></p>
    @endif
</div>

