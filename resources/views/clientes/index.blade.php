<x-app-layout>
    @php
        $createUrl = route('clientes.create');
        $indexUrl = route('clientes.index');
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Prospectos Clientes') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Gestión de prospectos de clientes</p>
            </div>
            <x-button variant="primary" size="lg" onclick="window.location.href='{{ $createUrl }}';">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nuevo Cliente
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-full animate-fade-in">
        @if (session('success'))
            <div class="alert alert-success mb-6 animate-slide-in-right">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error mb-6 animate-slide-in-right">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-error mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <h3 class="font-semibold">Errores:</h3>
                </div>
                <ul class="list-disc pl-6 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Barra de Búsqueda y Filtros -->
        <div class="filter-bar">
            <form method="GET" action="{{ route('clientes.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Búsqueda General -->
                    <div>
                        <label class="form-label">Buscar</label>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Nombre o ciudad..." 
                            class="form-input"
                            data-search-table="clientes-table"
                        >
                    </div>

                    <!-- Filtro por Ciudad -->
                    <div>
                        <label class="form-label">Ciudad</label>
                        <select name="ciudad" class="form-input">
                            <option value="">Todas</option>
                            @foreach($ciudades ?? [] as $ciudad)
                                <option value="{{ $ciudad }}" {{ request('ciudad') == $ciudad ? 'selected' : '' }}>
                                    {{ $ciudad }}
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
                        @if(request()->hasAny(['search', 'ciudad']))
                            <x-button type="button" variant="ghost" onclick="window.location.href='{{ $indexUrl }}';">
                                Limpiar
                            </x-button>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabla de Clientes -->
        <div class="modern-card animate-slide-up" style="animation-delay: 0.2s">
            <div class="modern-card-header">
                <h4 class="text-lg font-bold font-display text-text-primary">Listado de Prospectos</h4>
                <p class="text-sm text-text-muted mt-1">Total: {{ $clientes->total() }} prospectos</p>
            </div>
            <div class="modern-card-body p-0">
                <x-table id="clientes-table">
                    <x-slot name="headers">
                        <th>
                            <a href="{{ route('clientes.index', array_merge(request()->all(), ['sort' => 'nombre_empresa', 'direction' => request('sort') == 'nombre_empresa' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-primary transition-colors duration-200">
                                <span>Nombre Empresa</span>
                                @if(request('sort') == 'nombre_empresa')
                                    <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>Nombre Contacto</th>
                        <th>Teléfono</th>
                        <th>
                            <a href="{{ route('clientes.index', array_merge(request()->all(), ['sort' => 'ciudad', 'direction' => request('sort') == 'ciudad' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                               class="flex items-center space-x-1 hover:text-primary transition-colors duration-200">
                                <span>Ciudad</span>
                                @if(request('sort') == 'ciudad')
                                    <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th>Industria</th>
                    </x-slot>
                    @forelse($clientes as $c)
                        <tr class="transition-colors duration-150">
                            <td>
                                <div class="font-semibold text-text-primary">{{ $c->nombre_empresa ?? '-' }}</div>
                                <div class="text-xs text-text-muted mt-1">ID: {{ $c->_id ?? '-' }}</div>
                            </td>
                            <td class="text-text-secondary">{{ $c->nombre_contacto ?? '-' }}</td>
                            <td>
                                <div class="text-text-secondary">{{ $c->telefono ?? '-' }}</div>
                                @if($c->email)
                                    <div class="text-xs text-text-muted mt-1">{{ $c->email }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="text-text-secondary">{{ $c->ciudad ?? '-' }}</div>
                                @if($c->estado)
                                    <div class="text-xs text-text-muted mt-1">{{ $c->estado }}</div>
                                @endif
                            </td>
                            <td class="text-text-secondary">{{ $c->industria ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-12 text-center" colspan="5">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="text-text-muted font-medium">No hay prospectos de clientes registrados</p>
                                    @if(request()->hasAny(['search', 'ciudad']))
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
                <form method="GET" action="{{ route('clientes.index') }}" class="inline">
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
                    Mostrando <span class="font-semibold text-text-primary">{{ $clientes->firstItem() ?? 0 }}</span> a 
                    <span class="font-semibold text-text-primary">{{ $clientes->lastItem() ?? 0 }}</span> de 
                    <span class="font-semibold text-text-primary">{{ $clientes->total() }}</span> resultados
                </span>
            </div>
            <div class="pagination">
                {{ $clientes->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
