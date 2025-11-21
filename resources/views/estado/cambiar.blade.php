<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-brand-black">
            Cambiar Estado de Servicio
        </h2>
        <p class="text-sm text-gray-500 mt-1">Gestionar estado y transiciones del servicio</p>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Información del Servicio -->
        <div class="bg-white rounded-lg shadow mb-6 p-6">
            <h3 class="text-lg font-semibold text-brand-black mb-4">Información del Servicio</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Cliente</p>
                    <p class="font-medium">{{ $servicio->cliente_nombre ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Origen → Destino</p>
                    <p class="font-medium">{{ $servicio->origen ?? '-' }} → {{ $servicio->destino ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Fecha de Servicio</p>
                    <p class="font-medium">{{ optional($servicio->fecha_servicio)->format('d/m/Y') ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Estado Actual</p>
                    <span class="px-3 py-1 text-sm rounded {{ \App\Services\EstadoService::obtenerColor($estadoActual) }}">
                        {{ \App\Services\EstadoService::obtenerEtiqueta($estadoActual) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Formulario de Cambio de Estado -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-brand-black mb-4">Cambiar Estado</h3>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (count($estadosPermitidos) === 0)
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                        <p class="text-sm text-yellow-800">
                            Este servicio está en un estado final y no se puede cambiar.
                        </p>
                    </div>
                @else
                    <form action="{{ route('estado.update', $servicio->_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nuevo Estado
                            </label>
                            <select 
                                name="nuevo_estado" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-red"
                                required
                            >
                                <option value="">Seleccione un estado</option>
                                @foreach($estadosPermitidos as $estado)
                                    <option value="{{ $estado }}">
                                        {{ \App\Services\EstadoService::obtenerEtiqueta($estado) }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Estados disponibles desde "{{ \App\Services\EstadoService::obtenerEtiqueta($estadoActual) }}"
                            </p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Comentario (opcional)
                            </label>
                            <textarea 
                                name="comentario" 
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-red"
                                placeholder="Agregue un comentario sobre el cambio de estado..."
                                maxlength="1000"
                            ></textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                Este comentario se guardará en el historial del servicio
                            </p>
                        </div>

                        <div class="flex justify-end gap-3 responsive-actions">
                            <a 
                                href="{{ route('registro.edit', $servicio->_id) }}" 
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Cancelar
                            </a>
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-brand-red text-white rounded-lg hover:bg-red-700"
                            >
                                Cambiar Estado
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            <!-- Historial de Estados -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-brand-black mb-4">Historial de Cambios</h3>
                
                @if($historial->count() > 0)
                    <div class="space-y-4">
                        @foreach($historial as $cambio)
                            <div class="border-l-4 border-brand-red pl-4 pb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        @if($cambio->estado_anterior)
                                            <span class="px-2 py-1 text-xs rounded {{ \App\Services\EstadoService::obtenerColor($cambio->estado_anterior) }}">
                                                {{ \App\Services\EstadoService::obtenerEtiqueta($cambio->estado_anterior) }}
                                            </span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-600">
                                                Estado inicial
                                            </span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        @endif
                                        <span class="px-2 py-1 text-xs rounded {{ \App\Services\EstadoService::obtenerColor($cambio->estado_nuevo) }}">
                                            {{ \App\Services\EstadoService::obtenerEtiqueta($cambio->estado_nuevo) }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        {{ optional($cambio->changed_at)->format('d/m/Y H:i') ?? '-' }}
                                    </span>
                                </div>
                                @if($cambio->comentario)
                                    <p class="text-sm text-gray-600 mt-2">{{ $cambio->comentario }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm">No hay historial de cambios</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Diagrama de Estados -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-semibold text-brand-black mb-4">Flujo de Estados</h3>
            <div class="flex flex-wrap items-center gap-3 justify-center p-4 bg-gray-50 rounded-lg">
                @php
                    $flujo = ['pendiente', 'confirmado', 'en_carga', 'en_transito', 'entregado', 'facturado'];
                @endphp
                @foreach($flujo as $index => $estado)
                    <div class="flex items-center">
                        <div class="text-center">
                            <div class="px-4 py-2 rounded-lg {{ $estado === $estadoActual ? 'bg-brand-red text-white font-semibold' : 'bg-gray-200 text-gray-700' }}">
                                {{ \App\Services\EstadoService::obtenerEtiqueta($estado) }}
                            </div>
                        </div>
                        @if($index < count($flujo) - 1)
                            <svg class="w-6 h-6 text-gray-400 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-500">
                    El servicio también puede cancelarse en cualquier momento antes de ser entregado.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
