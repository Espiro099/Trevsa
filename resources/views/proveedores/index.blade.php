<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Prospectos Proveedores') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Gestión de prospectos de proveedores</p>
            </div>
            <div class="flex gap-3">
                <x-button variant="primary" data-href="{{ route('prospectos_proveedores.create') }}" class="redirect-btn">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nuevo Prospecto
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-full animate-fade-in">
        <!-- Barra de Búsqueda -->
        <div class="modern-card mb-4">
            <form method="GET" action="{{ route('prospectos_proveedores.index') }}" class="p-4">
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
                            <x-button variant="outline" onclick="window.location.href='{{ route('prospectos_proveedores.index') }}'">
                                Limpiar
                            </x-button>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <div class="modern-card">
            <div class="modern-card-header">
                <h4 class="text-lg font-bold font-display text-text-primary">Listado de Prospectos</h4>
                <p class="text-sm text-text-muted mt-1">Total: {{ $proveedores->total() }} prospectos</p>
            </div>
            <div class="modern-card-body p-0">
                <x-table id="prospectos-table">
                    <x-slot name="headers">
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Cant. Unidades</th>
                        <th>Tipos</th>
                        <th>Registrado por</th>
                    </x-slot>
                    @forelse($proveedores as $p)
                        <tr class="transition-colors duration-150">
                            <td class="font-semibold text-primary">{{ $p->formatted_id }}</td>
                            <td class="font-semibold text-text-primary">{{ $p->nombre_empresa }}</td>
                            <td class="text-text-secondary">{{ $p->telefono ?? 'N/A' }}</td>
                            <td class="text-text-secondary">{{ $p->email ?? 'N/A' }}</td>
                            <td class="text-text-secondary">{{ $p->cantidad_unidades ?? 0 }}</td>
                            <td>
                                @if(is_array($p->tipos_unidades) && count($p->tipos_unidades) > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($p->tipos_unidades as $tipo)
                                            <x-badge variant="default" size="sm">{{ $tipo }}</x-badge>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-text-secondary">{{ $p->nombre_quien_registro ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-12 text-center" colspan="7">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p class="text-text-muted font-medium">No hay prospectos registrados</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </x-table>
            </div>
        </div>

        <div class="mt-6 flex justify-center animate-fade-in" style="animation-delay: 0.2s">
            {{ $proveedores->appends(request()->query())->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.redirect-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const href = this.getAttribute('data-href');
                    if (href) {
                        window.location.href = href;
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
