@php
    $urlRegistro = route('registro.index');
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Registrar Solicitud') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Complete la información del nuevo servicio</p>
            </div>
            <x-button variant="ghost" onclick="window.location.href='{{ $urlRegistro }}'">
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
                <h4 class="text-lg font-bold font-display text-text-primary">Información del Servicio</h4>
            </div>
            <div class="modern-card-body">
                <form action="{{ route('registro.store') }}" method="POST">
            @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="form-label">Cliente *</label>
                        <select name="cliente_id" class="form-input" required>
                        <option value="">Seleccione un cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->_id }}" {{ old('cliente_id') == $cliente->_id ? 'selected' : '' }}>
                                {{ $cliente->nombre_empresa }} @if($cliente->nombre_contacto)({{ $cliente->nombre_contacto }})@endif
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="cliente_nombre" id="cliente_nombre_hidden">
                </div>

                    <div>
                        <label class="form-label">Proveedor</label>
                        <select name="proveedor_id" id="proveedor_id_select" class="form-input">
                        <option value="">Seleccione un proveedor (opcional)</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->_id }}" data-nombre="{{ $proveedor->nombre_empresa }}" {{ old('proveedor_id') == $proveedor->_id ? 'selected' : '' }}>
                                {{ $proveedor->nombre_empresa }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="proveedor_nombre" id="proveedor_nombre_hidden">
                </div>

                    <div>
                        <label class="form-label">Tipo Transporte para el Servicio</label>
                        <select name="tipo_transporte" class="form-input">
                        <option value="">Seleccione</option>
                        <option value="Caja seca 53">Caja seca 53</option>
                        <option value="Caja seca 48">Caja seca 48</option>
                        <option value="Plataforma">Plataforma</option>
                        <option value="Rabon">Rabon</option>
                        <option value="Camioneta 3.5">Camioneta 3.5</option>
                        <option value="Thermo King">Thermo King</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                    <div>
                        <label class="form-label">Tipo de carga</label>
                        <input name="tipo_carga" value="{{ old('tipo_carga') }}" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Peso de la Carga</label>
                        <input name="peso_carga" value="{{ old('peso_carga') }}" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Origen</label>
                        <input name="origen" value="{{ old('origen') }}" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Destino</label>
                        <input name="destino" value="{{ old('destino') }}" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Fecha Servicio</label>
                        <input type="date" name="fecha_servicio" value="{{ old('fecha_servicio') }}" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Hora Servicio</label>
                        <input type="time" name="hora_servicio" value="{{ old('hora_servicio') }}" class="form-input" />
                    </div>

                    <!-- Cálculo de Distancia y Costo -->
                    <div class="md:col-span-2 glass-card">
                        <h3 class="text-lg font-bold font-display text-text-primary mb-4">Cálculo Automático de Tarifas</h3>
                    
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-button type="button" variant="secondary" id="btn-calcular-distancia" class="w-full">
                                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Calcular Distancia
                                </x-button>
                            </div>
                            <div>
                                <label class="form-label">Distancia (km)</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    id="distancia_km" 
                                    name="distancia_km" 
                                    value="{{ old('distancia_km') }}" 
                                    class="form-input bg-bg-secondary" 
                                    readonly
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="form-label">Costo Diesel</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    id="costo_diesel" 
                                    name="costo_diesel" 
                                    value="{{ old('costo_diesel') }}" 
                                    class="form-input bg-bg-secondary" 
                                    readonly
                                />
                                <p class="text-xs text-text-muted mt-1">Distancia × Precio Diesel</p>
                            </div>
                            <div>
                                <label class="form-label">Tarifa Cliente *</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    id="tarifa_cliente" 
                                    name="tarifa_cliente" 
                                    value="{{ old('tarifa_cliente') }}" 
                                    class="form-input" 
                                    required
                                />
                                <p class="text-xs text-text-muted mt-1">Ingrese manualmente</p>
                            </div>
                            <div>
                                <label class="form-label">Margen Calculado</label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    id="margen_calculado" 
                                    name="margen_calculado" 
                                    value="{{ old('margen_calculado') }}" 
                                    class="form-input bg-success/10 font-semibold" 
                                    readonly
                                />
                                <p class="text-xs text-text-muted mt-1" id="margen_porcentual">Tarifa - Costos</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Tarifa Proveedor (opcional)</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            id="tarifa_proveedor" 
                            name="tarifa_proveedor" 
                            value="{{ old('tarifa_proveedor') }}" 
                            class="form-input" 
                        />
                        <p class="text-xs text-text-muted mt-1">Si se ingresa, se incluirá en el cálculo del margen</p>
                    </div>

                    <div>
                        <label class="form-label">Estado Inicial</label>
                        <select name="estado" class="form-input">
                            <option value="pendiente" {{ old('estado', 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        </select>
                        <p class="text-xs text-text-muted mt-1">
                            Los servicios siempre inician como "Pendiente". Puede cambiar el estado después de crear el servicio.
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="form-label">Comentarios</label>
                        <textarea name="comentarios" class="form-input" rows="4">{{ old('comentarios') }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-4 pt-6 border-t border-border-color responsive-actions">
                    <x-button type="button" variant="ghost" onclick="window.location.href='{{ $urlRegistro }}'">
                        Cancelar
                    </x-button>
                    <x-button type="submit" variant="primary">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar
                    </x-button>
                </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const clienteSelect = document.querySelector('select[name="cliente_id"]');
            const proveedorSelect = document.getElementById('proveedor_id_select');
            const btnCalcularDistancia = document.getElementById('btn-calcular-distancia');
            const inputOrigen = document.querySelector('input[name="origen"]');
            const inputDestino = document.querySelector('input[name="destino"]');
            const inputDistancia = document.getElementById('distancia_km');
            const inputCostoDiesel = document.getElementById('costo_diesel');
            const inputTarifaCliente = document.getElementById('tarifa_cliente');
            const inputTarifaProveedor = document.getElementById('tarifa_proveedor');
            const inputMargen = document.getElementById('margen_calculado');
            const labelMargenPorcentual = document.getElementById('margen_porcentual');

            // Actualizar nombre del cliente
            if (clienteSelect) {
                clienteSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const clienteNombre = selectedOption.textContent.trim();
                    document.getElementById('cliente_nombre_hidden').value = clienteNombre;
                });
                
                if (clienteSelect.value) {
                    clienteSelect.dispatchEvent(new Event('change'));
                }
            }

            // Actualizar nombre del proveedor
            if (proveedorSelect) {
                proveedorSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const proveedorNombre = selectedOption.getAttribute('data-nombre') || '';
                    document.getElementById('proveedor_nombre_hidden').value = proveedorNombre;
                });
                
                if (proveedorSelect.value) {
                    proveedorSelect.dispatchEvent(new Event('change'));
                }
            }

            // Calcular distancia
            if (btnCalcularDistancia) {
                btnCalcularDistancia.addEventListener('click', function() {
                    const origen = inputOrigen?.value.trim();
                    const destino = inputDestino?.value.trim();

                    if (!origen || !destino) {
                        alert('Por favor ingrese origen y destino antes de calcular la distancia.');
                        return;
                    }

                    btnCalcularDistancia.disabled = true;
                    btnCalcularDistancia.innerHTML = '<span class="animate-spin">⏳</span> Calculando...';

                    fetch('{{ route("api.calcular.distancia") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ origen, destino })
                    })
                    .then(response => response.json())
                    .then(data => {
                        btnCalcularDistancia.disabled = false;
                        btnCalcularDistancia.innerHTML = '<svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg> Calcular Distancia';

                        if (data.success) {
                            inputDistancia.value = data.distancia_km;
                            calcularTarifaYMargen();
                        } else {
                            alert('Error: ' + (data.message || 'No se pudo calcular la distancia.'));
                        }
                    })
                    .catch(error => {
                        btnCalcularDistancia.disabled = false;
                        btnCalcularDistancia.innerHTML = '<svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg> Calcular Distancia';
                        alert('Error al calcular la distancia. Por favor intente nuevamente.');
                        console.error('Error:', error);
                    });
                });
            }

            // Función para calcular tarifa y margen
            function calcularTarifaYMargen() {
                const distancia = parseFloat(inputDistancia.value) || 0;
                const tarifaCliente = parseFloat(inputTarifaCliente.value) || 0;
                const tarifaProveedor = parseFloat(inputTarifaProveedor.value) || 0;

                if (!distancia) {
                    inputCostoDiesel.value = '';
                    inputMargen.value = '';
                    labelMargenPorcentual.textContent = 'Tarifa - Costos';
                    return;
                }

                fetch('{{ route("api.calcular.tarifa") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        distancia_km: distancia,
                        tarifa_cliente: tarifaCliente,
                        tarifa_proveedor: tarifaProveedor
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        inputCostoDiesel.value = data.costo_diesel || '';
                        inputMargen.value = data.margen_calculado || '';
                        
                        if (data.margen_porcentual) {
                            labelMargenPorcentual.textContent = `Margen: ${data.margen_porcentual}%`;
                        } else {
                            labelMargenPorcentual.textContent = 'Tarifa - Costos';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error calculando tarifa:', error);
                });
            }

            // Calcular margen cuando cambian tarifa cliente o proveedor
            if (inputTarifaCliente) {
                inputTarifaCliente.addEventListener('input', calcularTarifaYMargen);
            }
            if (inputTarifaProveedor) {
                inputTarifaProveedor.addEventListener('input', calcularTarifaYMargen);
            }
        });
    </script>
    @endpush
</x-app-layout>
