@php
    $routeCreate = route('registro.create');
    $routeIndex = route('registro.index');
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Servicio Clientes') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Gestión de solicitudes de transporte</p>
            </div>
            <x-button variant="primary" size="lg" onclick="window.location.href='{{ $routeCreate }}';">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nueva Solicitud
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-full animate-fade-in">
        <!-- Barra de Búsqueda y Filtros -->
        <div class="filter-bar">
            <form method="GET" action="{{ route('registro.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Búsqueda General -->
                    <div>
                        <label class="form-label">Buscar</label>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Cliente, origen, destino..." 
                            class="form-input"
                            data-search-table="servicios-table"
                        >
                    </div>

                    <!-- Filtro por Estado -->
                    <div>
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-input">
                            <option value="">Todos</option>
                            @foreach($estados ?? [] as $estado)
                                <option value="{{ $estado }}" {{ request('estado') == $estado ? 'selected' : '' }}>
                                    {{ ucfirst($estado) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtro por Tipo de Transporte -->
                    <div>
                        <label class="form-label">Tipo Transporte</label>
                        <select name="tipo_transporte" class="form-input">
                            <option value="">Todos</option>
                            @foreach($tiposTransporte ?? [] as $tipo)
                                <option value="{{ $tipo }}" {{ request('tipo_transporte') == $tipo ? 'selected' : '' }}>
                                    {{ $tipo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex items-end gap-2">
                        <x-button type="submit" variant="primary">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Buscar
                        </x-button>
                        @if(request()->hasAny(['search', 'estado', 'tipo_transporte', 'fecha_desde', 'fecha_hasta']))
                            <x-button type="button" variant="ghost" onclick="window.location.href='{{ $routeIndex }}';">
                                Limpiar
                            </x-button>
                        @endif
                    </div>
                </div>

                <!-- Filtros de Fecha -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Fecha Desde</label>
                        <input 
                            type="date" 
                            name="fecha_desde" 
                            value="{{ request('fecha_desde') }}"
                            class="form-input"
                        >
                    </div>
                    <div>
                        <label class="form-label">Fecha Hasta</label>
                        <input 
                            type="date" 
                            name="fecha_hasta" 
                            value="{{ request('fecha_hasta') }}"
                            class="form-input"
                        >
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de Servicios -->
        <div class="modern-card animate-slide-up" style="animation-delay: 0.2s">
            <div class="modern-card-header">
                <h4 class="text-lg font-bold text-text-primary">Listado de Servicios</h4>
                <p class="text-sm text-text-muted mt-1">Total: {{ $solicitudes->total() }} servicios</p>
            </div>
            <div class="modern-card-body p-0">
                <x-table id="servicios-table">
                    <x-slot name="headers">
                        <th>
                            <a href="{{ route('registro.index', array_merge(request()->all(), ['sort' => 'cliente_nombre', 'direction' => request('sort') == 'cliente_nombre' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-primary transition-colors duration-200">
                                <span>Cliente</span>
                                @if(request('sort') == 'cliente_nombre')
                                    <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>Tipo Transporte</th>
                        <th>
                            <a href="{{ route('registro.index', array_merge(request()->all(), ['sort' => 'origen', 'direction' => request('sort') == 'origen' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-primary transition-colors duration-200">
                                <span>Origen</span>
                                @if(request('sort') == 'origen')
                                    <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('registro.index', array_merge(request()->all(), ['sort' => 'destino', 'direction' => request('sort') == 'destino' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-primary transition-colors duration-200">
                                <span>Destino</span>
                                @if(request('sort') == 'destino')
                                    <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('registro.index', array_merge(request()->all(), ['sort' => 'fecha_servicio', 'direction' => request('sort') == 'fecha_servicio' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-primary transition-colors duration-200">
                                <span>Fecha Servicio</span>
                                @if(request('sort') == 'fecha_servicio')
                                    <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>Hora</th>
                        <th>
                            <a href="{{ route('registro.index', array_merge(request()->all(), ['sort' => 'estado', 'direction' => request('sort') == 'estado' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-primary transition-colors duration-200">
                                <span>Estado</span>
                                @if(request('sort') == 'estado')
                                    <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>Acciones</th>
                    </x-slot>
                    @forelse($solicitudes as $s)
                        <tr class="transition-colors duration-150">
                            <td class="font-semibold text-text-primary">{{ $s->cliente_nombre ?? '-' }}</td>
                            <td class="text-text-secondary">{{ $s->tipo_transporte ?? '-' }}</td>
                            <td class="text-text-secondary">{{ $s->origen ?? '-' }}</td>
                            <td class="text-text-secondary">{{ $s->destino ?? '-' }}</td>
                            <td class="text-text-secondary">{{ optional($s->fecha_servicio)->toDateString() ?? '-' }}</td>
                            <td class="text-text-secondary">{{ $s->hora_servicio ?? '-' }}</td>
                            <td>
                                @php
                                    $estado = strtolower(str_replace(' ', '_', $s->estado ?? 'pendiente'));
                                @endphp
                                <x-badge variant="{{ $estado }}">
                                    {{ \App\Services\EstadoService::obtenerEtiqueta($s->estado ?? 'pendiente') }}
                                </x-badge>
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('registro.edit', $s->_id) }}" 
                                       class="text-red-600 hover:text-red-800 font-medium transition-colors duration-200">
                                        Editar
                                    </a>
                                    <span class="text-text-muted">|</span>
                                    <a href="{{ route('estado.show', $s->_id) }}" 
                                       class="text-primary hover:text-primary-dark font-semibold transition-colors duration-200">
                                        Cambiar Estado
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-12 text-center" colspan="8">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-text-muted font-medium">No hay servicios registrados</p>
                                    @if(request()->hasAny(['search', 'estado', 'tipo_transporte', 'fecha_desde', 'fecha_hasta']))
                                        <p class="text-sm text-text-muted mt-2">Intenta ajustar los filtros de búsqueda</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </x-table>
            </div>
        </div>

        <!-- Paginación -->
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 animate-fade-in" style="animation-delay: 0.3s">
            <div class="flex items-center space-x-3">
                <span class="text-sm font-medium text-text-secondary">Mostrar:</span>
                <form method="GET" action="{{ route('registro.index') }}" class="inline">
                    @foreach(request()->except('per_page', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select 
                        name="per_page" 
                        onchange="this.form.submit()"
                        class="form-input py-2 text-sm"
                    >
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <span class="text-sm text-text-secondary">
                    Mostrando <span class="font-semibold text-text-primary">{{ $solicitudes->firstItem() ?? 0 }}</span> a 
                    <span class="font-semibold text-text-primary">{{ $solicitudes->lastItem() ?? 0 }}</span> de 
                    <span class="font-semibold text-text-primary">{{ $solicitudes->total() }}</span> resultados
                </span>
            </div>
            <div class="pagination">
                {{ $solicitudes->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
