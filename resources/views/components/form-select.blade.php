@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'required' => false,
    'options' => [],
    'placeholder' => 'Seleccione una opción',
])

@php
    $hasError = $errors->has($name);
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
        <select 
            name="{{ $name }}"
            id="{{ $name }}"
            @if($required) required @endif
            @change="
                touched = true;
                value = $event.target.value;
            "
            @blur="touched = true"
            :class="
                (touched && error) || {{ $hasError ? 'true' : 'false' }} 
                    ? 'form-input error border-brand-red focus:ring-brand-red' 
                    : 'form-input'
            "
            {{ $attributes->except(['class', 'name', 'id', 'required']) }}
            class="form-input {{ $hasError ? 'error border-brand-red focus:ring-brand-red' : '' }}"
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach($options as $optionValue => $optionLabel)
                <option 
                    value="{{ $optionValue }}" 
                    {{ old($name, $value) == $optionValue ? 'selected' : '' }}
                >
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>
        
        <!-- Ícono de error -->
        <div 
            x-show="(touched && error) || {{ $hasError ? 'true' : 'false' }}"
            x-transition:enter="transition ease-out duration-200"
            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
        >
            <svg class="w-5 h-5 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
        ></p>
    @endif
</div>

