<x-app-layout>
    @php
        $createUrl = route('unidades.create');
    @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Unidades Disponibles') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Gestión de unidades disponibles</p>
            </div>
            <x-button variant="primary" onclick="window.location.href='{{ $createUrl }}';">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nueva Unidad
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-full animate-fade-in">
        <!-- Barra de Búsqueda -->
        <div class="modern-card mb-4">
            <form method="GET" action="{{ route('unidades.index') }}" class="p-4">
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="form-label">Buscar por transportista</label>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Buscar por nombre de transportista..." 
                            class="form-input"
                        >
                    </div>
                    <div class="flex gap-2">
                        <x-button type="submit" variant="primary">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Buscar
                        </x-button>
                        @if(request('search'))
                            <x-button variant="outline" onclick="clearSearch()">
                                Limpiar
                            </x-button>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="modern-card">
            <div class="modern-card-header">
                <h4 class="text-lg font-bold font-display text-text-primary">Listado de Unidades</h4>
                <p class="text-sm text-text-muted mt-1">Total: {{ $unidades->total() }} unidades</p>
            </div>
            <div class="modern-card-body p-0">
                @if (session('success'))
                    <div class="alert alert-success m-6 animate-slide-in-right">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-error m-6 animate-slide-in-right">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <x-table>
                    <x-slot name="headers">
                        <th>Transportista</th>
                        <th>Unidades Disponibles</th>
                        <th>Lugar Disponible</th>
                        <th>Fecha y Hora</th>
                        <th>Destino Sugerido</th>
                        <th>Estatus</th>
                        <th>Acciones</th>
                    </x-slot>
                    @forelse($unidades as $u)
                        <tr>
                            <td>
                                <div class="font-semibold text-text-primary">
                                    {{ $u->nombre_transportista ?? ($u->transporteProveedor->proveedor->nombre_empresa ?? '-') }}
                                </div>
                                @if($u->transporte_proveedor_id)
                                    <div class="text-xs text-text-muted mt-1">
                                        ID: {{ $u->transporte_proveedor_id }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($u->unidades_disponibles && count($u->unidades_disponibles) > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($u->unidades_disponibles as $unidad)
                                            @php
                                                $cantidad = $u->cantidades_unidades[$unidad] ?? 1;
                                            @endphp
                                            <x-badge variant="default" size="sm">
                                                {{ $unidad }} ({{ $cantidad }})
                                            </x-badge>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-text-secondary">{{ $u->lugar_disponible ?? '-' }}</td>
                            <td class="text-text-secondary">
                                <div>{{ optional($u->fecha_disponible)->format('d/m/Y') ?? '-' }}</div>
                                @if($u->hora_disponible)
                                    <div class="text-xs text-text-muted">{{ $u->hora_disponible }}</div>
                                @endif
                            </td>
                            <td class="text-text-secondary">{{ $u->destino_sugerido ?? '-' }}</td>
                            <td>
                                <x-badge variant="{{ $u->estatus ?? 'disponible' }}">
                                    {{ ucfirst($u->estatus ?? 'Disponible') }}
                                </x-badge>
                            </td>
                            <td>
                                @php
                                    $urlEdit = route('unidades.edit', $u->_id);
                                @endphp
                                <x-button variant="outline" size="sm" onclick="window.location.href='{{ $urlEdit }}'">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar
                                </x-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-12 text-center" colspan="7">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-text-muted font-medium">No hay unidades disponibles registradas</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </x-table>
            </div>
        </div>

        <div class="mt-6 flex justify-center animate-fade-in" style="animation-delay: 0.2s">
            {{ $unidades->appends(request()->query())->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        function clearSearch() {
            window.location.href = "{{ route('unidades.index') }}";
        }
    </script>
    @endpush
</x-app-layout>
