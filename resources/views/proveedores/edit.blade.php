@php
    $urlIndex = route('prospectos_proveedores.index');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Editar Prospecto de Proveedor') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Modifique la información del prospecto</p>
            </div>
            <x-button variant="ghost" onclick="window.location.href='{{ $urlIndex }}'">
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
                @php
                    $tiposDisponibles = ['Caja Seca 53','Caja Seca 48','Plataforma','Rabon','Torton','Full Plataforma','Otro'];
                    $tiposIniciales = old('tipos_unidades', is_array($proveedor->tipos_unidades) ? $proveedor->tipos_unidades : []);
                    $cantidadesIniciales = old('cantidades_unidades', is_array($proveedor->cantidades_unidades) ? $proveedor->cantidades_unidades : []);
                    $cantidadesDefault = array_fill_keys($tiposDisponibles, 1);
                    $cantidadesCompletas = array_merge($cantidadesDefault, $cantidadesIniciales);
                @endphp
                <form action="{{ route('proveedores.update', $proveedor->_id) }}" method="POST" x-data="{ 
                    tiposUnidades: @js($tiposIniciales),
                    cantidades: @js($cantidadesCompletas),
                    toggleTipo(tipo) {
                        if (this.tiposUnidades.includes(tipo)) {
                            this.tiposUnidades = this.tiposUnidades.filter(t => t !== tipo);
                            delete this.cantidades[tipo];
                        } else {
                            this.tiposUnidades.push(tipo);
                            if (!this.cantidades[tipo] || this.cantidades[tipo] < 1) {
                                this.cantidades[tipo] = 1;
                            }
                        }
                    },
                    isSelected(tipo) {
                        return this.tiposUnidades.includes(tipo);
                    }
                }">
                    @csrf
                    @method('PUT')

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
                            label="Nombre o Empresa"
                            value="{{ old('nombre_empresa', $proveedor->nombre_empresa) }}"
                            placeholder="Nombre de la empresa o proveedor"
                            required
                        />

                        <x-form-input
                            name="telefono"
                            label="Teléfono"
                            type="tel"
                            value="{{ old('telefono', $proveedor->telefono) }}"
                            placeholder="Ej: +52 55 1234 5678"
                        />

                        <x-form-input
                            name="email"
                            label="Email"
                            type="email"
                            value="{{ old('email', $proveedor->email) }}"
                            placeholder="correo@ejemplo.com"
                        />

                        <x-form-input
                            name="cantidad_unidades"
                            label="Cantidad de Unidades"
                            type="number"
                            value="{{ old('cantidad_unidades', $proveedor->cantidad_unidades) }}"
                            placeholder="0"
                            min="0"
                        />

                        <x-form-input
                            name="base_linea_transporte"
                            label="Base Línea o Transporte"
                            value="{{ old('base_linea_transporte', $proveedor->base_linea_transporte) }}"
                            placeholder="Base de operaciones"
                        />

                        <x-form-input
                            name="corredor_linea_transporte"
                            label="Corredor de la Línea o Transporte"
                            value="{{ old('corredor_linea_transporte', $proveedor->corredor_linea_transporte) }}"
                            placeholder="Nombre del corredor"
                        />

                        <x-form-input
                            name="nombre_quien_registro"
                            label="Nombre quien registró"
                            value="{{ old('nombre_quien_registro', $proveedor->nombre_quien_registro) }}"
                            placeholder="Nombre de la persona que registra"
                        />

                        <div class="md:col-span-2">
                            <label class="form-label">
                                Tipos de Unidades y Cantidad <span class="text-brand-red">*</span>
                            </label>
                            <div class="space-y-3 mt-2">
                                @php
                                    $tipos = ['Caja Seca 53','Caja Seca 48','Plataforma','Rabon','Torton','Full Plataforma','Otro'];
                                    $oldTipos = old('tipos_unidades', is_array($proveedor->tipos_unidades) ? $proveedor->tipos_unidades : []);
                                    $oldCantidades = old('cantidades_unidades', is_array($proveedor->cantidades_unidades) ? $proveedor->cantidades_unidades : []);
                                @endphp
                                @foreach($tipos as $tipo)
                                    <div class="flex items-center gap-3 p-3 border-2 border-border-color rounded-lg hover:border-primary transition-all duration-200" 
                                         :class="isSelected('{{ $tipo }}') ? 'border-primary bg-bg-card-hover' : ''">
                                        <label class="flex items-center cursor-pointer flex-1">
                                            <input 
                                                type="checkbox" 
                                                name="tipos_unidades[]" 
                                                value="{{ $tipo }}"
                                                @change="toggleTipo('{{ $tipo }}')"
                                                class="w-4 h-4 text-primary border-border-color rounded focus:ring-primary focus:ring-2 transition-colors duration-200"
                                                {{ (is_array($oldTipos) && in_array($tipo, $oldTipos)) ? 'checked' : '' }}
                                            >
                                            <span class="ml-3 text-sm font-medium text-text-primary flex-1">
                                                {{ $tipo }}
                                            </span>
                                        </label>
                                        <div x-show="isSelected('{{ $tipo }}')" 
                                             x-transition
                                             class="flex items-center gap-2">
                                            <label class="text-xs text-text-muted">Cantidad:</label>
                                            <input 
                                                type="number" 
                                                name="cantidades_unidades[{{ $tipo }}]" 
                                                x-model="cantidades['{{ $tipo }}']"
                                                value="{{ $oldCantidades[$tipo] ?? '1' }}"
                                                min="1"
                                                class="w-20 px-2 py-1 text-sm form-input"
                                                placeholder="0"
                                            >
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('tipos_unidades')
                                <p class="form-error mt-1">{{ $message }}</p>
                            @enderror
                            @error('cantidades_unidades')
                                <p class="form-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <x-form-textarea
                                name="notas"
                                label="Notas / Comentarios"
                                value="{{ old('notas', $proveedor->notas) }}"
                                placeholder="Información adicional sobre el prospecto..."
                                rows="4"
                            />
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4 pt-6 border-t border-brand-gray-200 responsive-actions">
                        <x-button type="button" variant="ghost" onclick="window.location.href='{{ $urlIndex }}'">
                            Cancelar
                        </x-button>
                        <x-button type="submit" variant="primary">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar Cambios
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
