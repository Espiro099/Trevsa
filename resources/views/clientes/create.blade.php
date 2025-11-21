@php
    $clientesIndexUrl = route('clientes.index');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Registrar Prospecto Cliente') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Complete la información del nuevo prospecto</p>
            </div>
            <x-button variant="ghost" onclick="window.location.href='{{ $clientesIndexUrl }}'">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto animate-fade-in">
        <div class="modern-card">
            <div class="modern-card-header">
                <h4 class="text-lg font-bold font-display text-text-primary">Información del Prospecto</h4>
            </div>
            <div class="modern-card-body">
                <form action="{{ route('clientes.store') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-error mb-6 animate-slide-in-right">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-error mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <h3 class="font-semibold">Por favor corrige los siguientes errores:</h3>
                            </div>
                            <ul class="list-disc pl-6 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-form-input
                            name="nombre_empresa"
                            label="Nombre Empresa"
                            value="{{ old('nombre_empresa') }}"
                            placeholder="Nombre de la empresa"
                            required
                        />

                        <x-form-input
                            name="nombre_contacto"
                            label="Nombre Contacto"
                            value="{{ old('nombre_contacto') }}"
                            placeholder="Nombre del contacto principal"
                        />

                        <x-form-input
                            name="telefono"
                            label="Teléfono"
                            type="tel"
                            value="{{ old('telefono') }}"
                            placeholder="Ej: +52 55 1234 5678"
                        />

                        <x-form-input
                            name="email"
                            label="Email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="correo@ejemplo.com"
                        />

                        <x-form-input
                            name="ciudad"
                            label="Ciudad"
                            value="{{ old('ciudad') }}"
                            placeholder="Ciudad de operación"
                        />

                        <x-form-input
                            name="estado"
                            label="Estado"
                            value="{{ old('estado') }}"
                            placeholder="Estado"
                        />

                        <x-form-input
                            name="industria"
                            label="Industria o tipo de empresa"
                            value="{{ old('industria') }}"
                            placeholder="Tipo de industria"
                        />

                        <x-form-select
                            name="estado_prospecto"
                            label="Estado Prospecto"
                            value="{{ old('estado_prospecto', 'prospecto') }}"
                            :options="[
                                'prospecto' => 'Prospecto',
                                'activo' => 'Activo',
                                'inactivo' => 'Inactivo'
                            ]"
                        />

                        <div class="md:col-span-2">
                            <x-form-textarea
                                name="comentarios"
                                label="Comentarios Adicionales"
                                value="{{ old('comentarios') }}"
                                placeholder="Información adicional sobre el prospecto..."
                                rows="4"
                            />
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4 pt-6 border-t border-border-color responsive-actions">
                        <x-button type="button" variant="ghost" onclick="window.location.href='{{ $clientesIndexUrl }}'">
                            Cancelar
                        </x-button>
                        <x-button type="submit" variant="primary">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar Prospecto
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
