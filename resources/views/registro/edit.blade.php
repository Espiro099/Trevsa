@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4" style="color:var(--brand-black)">Editar Servicio Cliente</h2>

        <form action="{{ route('registro.update', $solicitud->_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cliente</label>
                    <select name="cliente_id" class="mt-1 block w-full border-gray-300 rounded p-2" required>
                        <option value="">Seleccione un cliente</option>
                        @foreach($clientes ?? [] as $cliente)
                            <option value="{{ $cliente->_id }}" {{ (old('cliente_id', $solicitud->cliente_id ?? '') == $cliente->_id) ? 'selected' : '' }}>
                                {{ $cliente->nombre_empresa }} @if($cliente->nombre_contacto)({{ $cliente->nombre_contacto }})@endif
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="cliente_nombre" id="cliente_nombre_hidden" value="{{ old('cliente_nombre', $solicitud->cliente_nombre ?? '') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Proveedor</label>
                    <select name="proveedor_id" id="proveedor_id_select" class="mt-1 block w-full border-gray-300 rounded p-2">
                        <option value="">Seleccione un proveedor (opcional)</option>
                        @foreach($proveedores ?? [] as $proveedor)
                            <option value="{{ $proveedor->_id }}" data-nombre="{{ $proveedor->nombre_empresa }}" {{ (old('proveedor_id', $solicitud->proveedor_id ?? '') == $proveedor->_id) ? 'selected' : '' }}>
                                {{ $proveedor->nombre_empresa }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="proveedor_nombre" id="proveedor_nombre_hidden" value="{{ old('proveedor_nombre', $solicitud->proveedor_nombre ?? '') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo Transporte para el Servicio</label>
                    <select name="tipo_transporte" class="mt-1 block w-full border-gray-300 rounded p-2">
                        @php
                            $tipos = ['Caja seca 53','Caja seca 48','Plataforma','Rabon','Camioneta 3.5','Thermo King','Otro'];
                        @endphp
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo }}" @if($solicitud->tipo_transporte == $tipo) selected @endif>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo de carga</label>
                    <input name="tipo_carga" value="{{ old('tipo_carga', $solicitud->tipo_carga) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Peso de la Carga</label>
                    <input name="peso_carga" value="{{ old('peso_carga', $solicitud->peso_carga) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Origen</label>
                    <input name="origen" value="{{ old('origen', $solicitud->origen) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Destino</label>
                    <input name="destino" value="{{ old('destino', $solicitud->destino) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fecha Servicio</label>
                    <input type="date" name="fecha_servicio" value="{{ old('fecha_servicio', optional($solicitud->fecha_servicio)->toDateString()) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Hora Servicio</label>
                    <input type="time" name="hora_servicio" value="{{ old('hora_servicio', $solicitud->hora_servicio) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>
                <!-- Cálculo de Distancia y Costo -->
                <div class="md:col-span-2 bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Cálculo Automático de Tarifas</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div>
                            <button 
                                type="button" 
                                id="btn-calcular-distancia"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                            >
                                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Calcular Distancia
                            </button>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Distancia (km)</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                id="distancia_km" 
                                name="distancia_km" 
                                value="{{ old('distancia_km', $solicitud->distancia_km) }}" 
                                class="mt-1 block w-full border-gray-300 rounded p-2 bg-gray-50" 
                                readonly
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Costo Diesel</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                id="costo_diesel" 
                                name="costo_diesel" 
                                value="{{ old('costo_diesel', $solicitud->costo_diesel) }}" 
                                class="mt-1 block w-full border-gray-300 rounded p-2 bg-gray-50" 
                                readonly
                            />
                            <p class="text-xs text-gray-500 mt-1">Distancia × Precio Diesel</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tarifa Cliente *</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                id="tarifa_cliente" 
                                name="tarifa_cliente" 
                                value="{{ old('tarifa_cliente', $solicitud->tarifa_cliente) }}" 
                                class="mt-1 block w-full border-gray-300 rounded p-2" 
                                required
                            />
                            <p class="text-xs text-gray-500 mt-1">Ingrese manualmente</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Margen Calculado</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                id="margen_calculado" 
                                name="margen_calculado" 
                                value="{{ old('margen_calculado', $solicitud->margen_calculado) }}" 
                                class="mt-1 block w-full border-gray-300 rounded p-2 bg-green-50 font-semibold" 
                                readonly
                            />
                            <p class="text-xs text-gray-500 mt-1" id="margen_porcentual">Tarifa - Costos</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tarifa Proveedor (opcional)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        id="tarifa_proveedor" 
                        name="tarifa_proveedor" 
                        value="{{ old('tarifa_proveedor', $solicitud->tarifa_proveedor) }}" 
                        class="mt-1 block w-full border-gray-300 rounded p-2" 
                    />
                    <p class="text-xs text-gray-500 mt-1">Si se ingresa, se incluirá en el cálculo del margen</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <div class="flex items-center gap-2">
                        <input 
                            type="text" 
                            value="{{ \App\Services\EstadoService::obtenerEtiqueta($solicitud->estado ?? 'pendiente') }}" 
                            class="mt-1 block flex-1 border-gray-300 rounded p-2 bg-gray-100" 
                            readonly
                        />
                        <a 
                            href="{{ route('estado.show', $solicitud->_id) }}" 
                            class="mt-1 px-4 py-2 bg-brand-red text-white rounded hover:bg-red-700 text-sm"
                        >
                            Cambiar Estado
                        </a>
                    </div>
                    <input type="hidden" name="estado" value="{{ $solicitud->estado ?? 'pendiente' }}">
                    <p class="text-xs text-gray-500 mt-1">
                        Use el botón "Cambiar Estado" para gestionar transiciones válidas
                    </p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Comentarios</label>
                    <textarea name="comentarios" class="mt-1 block w-full border-gray-300 rounded p-2">{{ old('comentarios', $solicitud->comentarios ?? '') }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end responsive-actions">
                <a href="{{ route('registro.index') }}" class="px-4 py-2 mr-2 border rounded">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-brand-red text-white rounded">Guardar Cambios</button>
            </div>
        </form>
    </div>
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
                    document.getElementById('cliente_nombre_hidden').value = selectedOption.textContent.trim();
                });
            }

            // Actualizar nombre del proveedor
            if (proveedorSelect) {
                proveedorSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    document.getElementById('proveedor_nombre_hidden').value = selectedOption.getAttribute('data-nombre') || '';
                });
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

            // Calcular automáticamente al cargar si hay datos
            if (inputDistancia.value && inputTarifaCliente.value) {
                calcularTarifaYMargen();
            }
        });
    </script>
@endsection