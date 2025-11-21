@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold mb-2" style="color:var(--brand-black)">Sistema de Cálculo Automático de Tarifas</h2>
            <p class="text-gray-600">Calcule automáticamente distancias, costos de diesel y márgenes de ganancia</p>
        </div>

        <!-- Precio Actual del Diesel -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-1">Precio Actual del Diesel</h3>
                    <p class="text-2xl font-bold text-red-600">
                        ${{ number_format($precioDiesel->precio_litro ?? 24.50, 2) }} MXN/Litro
                    </p>
                    @if($precioDiesel && $precioDiesel->fecha_vigencia)
                        <p class="text-xs text-gray-500 mt-1">
                            Vigente desde: {{ $precioDiesel->fecha_vigencia->format('d/m/Y') }}
                        </p>
                    @else
                        <p class="text-xs text-yellow-600 mt-1">
                            ⚠️ Configure el precio del diesel
                        </p>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('tarifas.precio-diesel') }}" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                        Gestionar Precio
                    </a>
                    <a href="{{ route('tarifas.historial') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm">
                        Ver Historial
                    </a>
                </div>
            </div>
        </div>

        <!-- Formulario de Cálculo -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Calculadora de Tarifas</h3>

            <form id="form-calculo" method="POST" action="{{ route('tarifas.calcular') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Origen y Destino -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Origen</label>
                        <input 
                            type="text" 
                            name="origen" 
                            id="origen"
                            value="{{ old('origen') }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-red"
                            placeholder="Ej: Monterrey, NL"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Destino</label>
                        <input 
                            type="text" 
                            name="destino" 
                            id="destino"
                            value="{{ old('destino') }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-red"
                            placeholder="Ej: Guadalajara, Jal"
                            required
                        />
                    </div>

                    <!-- Botón Calcular Distancia -->
                    <div class="md:col-span-2">
                        <button 
                            type="button" 
                            id="btn-calcular-distancia"
                            class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                        >
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Calcular Distancia
                        </button>
                    </div>

                    <!-- Distancia Calculada -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Distancia (km)</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            id="distancia_km" 
                            name="distancia_km"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" 
                            readonly
                        />
                    </div>

                    <!-- Costo Diesel (calculado automáticamente) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Costo Diesel (calculado)</label>
                        <input 
                            type="text" 
                            id="costo_diesel_display"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 font-semibold" 
                            readonly
                        />
                    </div>

                    <!-- Tarifa Cliente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tarifa Cliente *</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="tarifa_cliente" 
                            id="tarifa_cliente"
                            value="{{ old('tarifa_cliente') }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-red"
                            placeholder="0.00"
                            required
                        />
                        <p class="text-xs text-gray-500 mt-1">Ingrese la tarifa que cobrará al cliente</p>
                    </div>

                    <!-- Tarifa Proveedor (opcional) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tarifa Proveedor (opcional)</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="tarifa_proveedor" 
                            id="tarifa_proveedor"
                            value="{{ old('tarifa_proveedor') }}" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-red"
                            placeholder="0.00"
                        />
                        <p class="text-xs text-gray-500 mt-1">Si aplica, ingrese la tarifa del proveedor</p>
                    </div>

                    <!-- Margen Calculado -->
                    <div class="md:col-span-2">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Margen Calculado</label>
                            <div class="flex items-center justify-between">
                                <input 
                                    type="text" 
                                    id="margen_calculado_display"
                                    class="flex-1 px-3 py-2 border border-green-300 rounded-lg bg-white font-bold text-lg text-green-700" 
                                    readonly
                                />
                                <span id="margen_porcentual_display" class="ml-3 text-sm font-semibold text-green-600"></span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Tarifa Cliente - (Costo Diesel + Tarifa Proveedor)</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3 responsive-actions">
                    <button 
                        type="button" 
                        id="btn-limpiar"
                        class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                    >
                        Limpiar
                    </button>
                    <button 
                        type="submit" 
                        id="btn-calcular-tarifa"
                        class="px-6 py-2 bg-brand-red text-white rounded-lg hover:bg-red-700 font-medium"
                        disabled
                    >
                        Calcular Tarifa Completa
                    </button>
                </div>
            </form>
        </div>

        <!-- Información Adicional -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="font-semibold text-gray-700 mb-2">📐 Cálculo de Distancia</h4>
                <p class="text-sm text-gray-600">Utiliza APIs de geocodificación para calcular la distancia real entre origen y destino</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="font-semibold text-gray-700 mb-2">⛽ Costo de Diesel</h4>
                <p class="text-sm text-gray-600">Calcula automáticamente: Distancia × Consumo (0.35 L/km) × Precio/Litro</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <h4 class="font-semibold text-gray-700 mb-2">💰 Margen de Ganancia</h4>
                <p class="text-sm text-gray-600">Calcula el margen restando todos los costos (diesel + proveedor) de la tarifa cliente</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnCalcularDistancia = document.getElementById('btn-calcular-distancia');
            const btnCalcularTarifa = document.getElementById('btn-calcular-tarifa');
            const btnLimpiar = document.getElementById('btn-limpiar');
            const inputOrigen = document.getElementById('origen');
            const inputDestino = document.getElementById('destino');
            const inputDistancia = document.getElementById('distancia_km');
            const inputCostoDiesel = document.getElementById('costo_diesel_display');
            const inputTarifaCliente = document.getElementById('tarifa_cliente');
            const inputTarifaProveedor = document.getElementById('tarifa_proveedor');
            const inputMargen = document.getElementById('margen_calculado_display');
            const labelMargenPorcentual = document.getElementById('margen_porcentual_display');

            let distanciaActual = 0;
            let costoDieselActual = 0;

            // Calcular distancia
            if (btnCalcularDistancia) {
                btnCalcularDistancia.addEventListener('click', function() {
                    const origen = inputOrigen.value.trim();
                    const destino = inputDestino.value.trim();

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
                        btnCalcularDistancia.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg> Calcular Distancia';

                        if (data.success) {
                            distanciaActual = data.distancia_km;
                            inputDistancia.value = data.distancia_km;
                            calcularCostoYMargen();
                            btnCalcularTarifa.disabled = false;
                        } else {
                            alert('Error: ' + (data.message || 'No se pudo calcular la distancia.'));
                        }
                    })
                    .catch(error => {
                        btnCalcularDistancia.disabled = false;
                        btnCalcularDistancia.innerHTML = '<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg> Calcular Distancia';
                        alert('Error al calcular la distancia. Por favor intente nuevamente.');
                        console.error('Error:', error);
                    });
                });
            }

            // Calcular costo y margen
            function calcularCostoYMargen() {
                if (!distanciaActual) {
                    inputCostoDiesel.value = '';
                    inputMargen.value = '';
                    labelMargenPorcentual.textContent = '';
                    return;
                }

                const tarifaCliente = parseFloat(inputTarifaCliente.value) || 0;
                const tarifaProveedor = parseFloat(inputTarifaProveedor.value) || 0;

                fetch('{{ route("api.calcular.tarifa") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        distancia_km: distanciaActual,
                        tarifa_cliente: tarifaCliente,
                        tarifa_proveedor: tarifaProveedor
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        costoDieselActual = data.costo_diesel || 0;
                        inputCostoDiesel.value = '$' + new Intl.NumberFormat('es-MX', {minimumFractionDigits: 2}).format(costoDieselActual);
                        inputMargen.value = '$' + new Intl.NumberFormat('es-MX', {minimumFractionDigits: 2}).format(data.margen_calculado || 0);
                        
                        if (data.margen_porcentual) {
                            labelMargenPorcentual.textContent = '(' + data.margen_porcentual + '% margen)';
                        } else {
                            labelMargenPorcentual.textContent = '';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error calculando tarifa:', error);
                });
            }

            // Calcular cuando cambian las tarifas
            if (inputTarifaCliente) {
                inputTarifaCliente.addEventListener('input', calcularCostoYMargen);
            }
            if (inputTarifaProveedor) {
                inputTarifaProveedor.addEventListener('input', calcularCostoYMargen);
            }

            // Limpiar formulario
            if (btnLimpiar) {
                btnLimpiar.addEventListener('click', function() {
                    inputOrigen.value = '';
                    inputDestino.value = '';
                    inputDistancia.value = '';
                    inputCostoDiesel.value = '';
                    inputTarifaCliente.value = '';
                    inputTarifaProveedor.value = '';
                    inputMargen.value = '';
                    labelMargenPorcentual.textContent = '';
                    distanciaActual = 0;
                    costoDieselActual = 0;
                    btnCalcularTarifa.disabled = true;
                });
            }
        });
    </script>
    @endpush
@endsection
