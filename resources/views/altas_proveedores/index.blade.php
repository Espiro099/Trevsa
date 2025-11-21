<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Altas Proveedores') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Gestiona las altas de proveedores desde prospectos</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-full animate-fade-in">
        <!-- Barra de Búsqueda -->
        <div class="modern-card mb-4">
            <form method="GET" action="{{ route('altas_proveedores.index') }}" class="p-4">
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="form-label">Buscar por nombre</label>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Buscar por nombre de empresa..." 
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
                <div class="flex items-center justify-between">
                    <div>
                <h4 class="text-lg font-bold font-display text-text-primary">Listado de Prospectos para Alta</h4>
                <p class="text-sm text-text-muted mt-1">Total: {{ $prospectos->total() }} prospectos</p>
                    </div>
                    <div>
                        <x-button variant="outline" onclick="openExportModal()">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Exportar Excel
                        </x-button>
                    </div>
                </div>
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
                        <th>Nombre/Empresa</th>
                        <th>Teléfono</th>
                        <th>Fecha de Registro</th>
                        <th>Unidades</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </x-slot>
                    @forelse($prospectos as $prospecto)
                        @php
                            $alta = $prospecto->altaProveedor;
                            $estadoAlta = $alta ? $alta->status : 'pendiente';
                            $tieneAlta = $alta !== null;
                        @endphp
                        <tr class="transition-colors duration-150">
                            <td>
                                <div class="font-semibold text-text-primary">{{ $prospecto->nombre_empresa ?? 'N/A' }}</div>
                                <div class="text-xs text-text-muted mt-1">ID: {{ $prospecto->_id ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="text-text-secondary">{{ $prospecto->telefono ?? 'N/A' }}</div>
                                @if($prospecto->email)
                                    <div class="text-xs text-text-muted mt-1">{{ $prospecto->email }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="text-text-secondary">
                                    {{ $prospecto->created_at ? $prospecto->created_at->format('d/m/Y') : 'N/A' }}
                                </div>
                                @if($prospecto->nombre_quien_registro)
                                    <div class="text-xs text-text-muted mt-1">{{ $prospecto->nombre_quien_registro }}</div>
                                @elseif($prospecto->created_by && isset($users[$prospecto->created_by]))
                                    <div class="text-xs text-text-muted mt-1">{{ $users[$prospecto->created_by]->name }}</div>
                                @endif
                            </td>
                            <td>
                                @if($prospecto->tipos_unidades && count($prospecto->tipos_unidades) > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($prospecto->tipos_unidades as $tipo)
                                            <x-badge variant="default" size="sm">{{ $tipo }}</x-badge>
                                        @endforeach
                                    </div>
                                    <div class="text-xs text-text-muted mt-1">Cantidad: {{ $prospecto->cantidad_unidades ?? 0 }}</div>
                                @else
                                    <span class="text-text-muted">Sin unidades</span>
                                @endif
                            </td>
                            <td>
                                @if($tieneAlta)
                                    <x-badge variant="{{ $estadoAlta === 'alta' ? 'alta' : ($estadoAlta === 'pendiente' ? 'pendiente' : 'default') }}">
                                        {{ $estadoAlta === 'alta' ? 'Dado de Alta' : ucfirst(str_replace('_', ' ', $estadoAlta)) }}
                                    </x-badge>
                                @else
                                    <x-badge variant="default">
                                        Sin Alta
                                    </x-badge>
                                @endif
                            </td>
                            <td>
                                @php
                                    $urlShow = route('altas_proveedores.show', $prospecto->_id);
                                    $urlEdit = route('altas_proveedores.edit', $prospecto->_id);
                                    $urlCreate = route('altas_proveedores.create', $prospecto->_id);
                                @endphp
                                <div class="flex flex-wrap gap-2">
                                    <x-button variant="outline" size="sm" onclick="window.location.href='{{ $urlShow }}'">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </x-button>
                                    @if($tieneAlta)
                                        <x-button variant="outline" size="sm" onclick="window.location.href='{{ $urlEdit }}'">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar
                                        </x-button>
                                    @else
                                        <x-button variant="primary" size="sm" onclick="window.location.href='{{ $urlCreate }}'">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Completar
                                        </x-button>
                                    @endif
                                    @if($tieneAlta && $estadoAlta !== 'alta')
                                        <form action="{{ route('altas_proveedores.dar_alta', $prospecto->_id) }}" method="POST" class="inline">
                                            @csrf
                                            <x-button 
                                                type="submit" 
                                                variant="primary" 
                                                size="sm"
                                                onclick="return confirm('¿Está seguro de dar de alta a este proveedor?')"
                                            >
                                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Dar de Alta
                                            </x-button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-12 text-center" colspan="6">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    <p class="text-text-muted font-medium">No hay prospectos de proveedores registrados</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </x-table>
            </div>
        </div>

        <div class="mt-6 flex justify-center animate-fade-in" style="animation-delay: 0.2s">
            {{ $prospectos->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Modal de Exportación -->
    <div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden">
        <div class="modern-card max-w-md w-full mx-4">
            <div class="modern-card-header">
                <h4 class="text-lg font-bold font-display text-text-primary">Exportar a Excel</h4>
            </div>
            <div class="modern-card-body">
                <p class="text-text-secondary mb-4">Seleccione una opción de exportación:</p>
                <div class="flex flex-col gap-3">
                    <x-button variant="primary" onclick="exportAll()" class="w-full">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Exportar Todos los Registros
                    </x-button>
                    <div class="relative">
                        <label class="form-label mb-2 block">Seleccionar Prospecto:</label>
                        <select id="prospectoSelect" class="form-input w-full">
                            <option value="">Seleccione un prospecto...</option>
                            @foreach($allProspectos ?? [] as $prospecto)
                                <option value="{{ $prospecto->_id }}">{{ $prospecto->nombre_empresa ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-button variant="primary" onclick="exportSpecific()" class="w-full">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Exportar Prospecto Seleccionado
                    </x-button>
                    <x-button variant="outline" onclick="closeExportModal()" class="w-full">
                        Cancelar
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openExportModal() {
            const modal = document.getElementById('exportModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeExportModal() {
            const modal = document.getElementById('exportModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function exportAll() {
            window.location.href = "{{ route('altas_proveedores.export.all') }}";
        }

        function exportSpecific() {
            const prospectoId = document.getElementById('prospectoSelect').value;
            if (!prospectoId) {
                alert('Por favor, seleccione un prospecto');
                return;
            }
            const baseUrl = "{{ url('/altas-proveedores/export') }}";
            window.location.href = baseUrl + '/' + prospectoId;
        }

        function clearSearch() {
            window.location.href = "{{ route('altas_proveedores.index') }}";
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('exportModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeExportModal();
            }
        });
    </script>
    @endpush
</x-app-layout>
