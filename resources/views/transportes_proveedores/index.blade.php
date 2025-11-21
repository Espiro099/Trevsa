<x-app-layout>
    @php
        $createUrl = route('transportes_proveedores.create');
    @endphp
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Transportes Proveedores') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Gestiona los transportistas y proveedores registrados</p>
            </div>
            <x-button variant="primary" onclick="window.location.href='{{ $createUrl }}';">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Registrar Nuevo Proveedor
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-full animate-fade-in">
        <div class="modern-card">
            <div class="modern-card-header">
                <h4 class="text-lg font-bold font-display text-text-primary">Listado de Proveedores</h4>
                <p class="text-sm text-text-muted mt-1">Total: {{ $proveedores->total() }} proveedores</p>
            </div>
            <div class="modern-card-body p-0">
                <x-table>
                    <x-slot name="headers">
                        <th>Nombre Solicita</th>
                        <th>Unidades</th>
                        <th>Estado</th>
                        <th>Fecha de Registro</th>
                        <th>Acciones</th>
                    </x-slot>
                    @foreach($proveedores as $proveedor)
                        @php
                            $showUrl = route('transportes_proveedores.show', $proveedor->id);
                        @endphp
                        <tr>
                            <td class="font-semibold text-text-primary">{{ $proveedor->nombre_solicita ?? 'N/A' }}</td>
                            <td>
                                @if($proveedor->unidades && count($proveedor->unidades) > 0)
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($proveedor->unidades as $unidad)
                                            <x-badge variant="default" size="sm">{{ $unidad }}</x-badge>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-text-muted">Sin unidades</span>
                                @endif
                            </td>
                            <td>
                                <x-badge variant="{{ $proveedor->status === 'aprobado' ? 'alta' : 'pendiente' }}">
                                    {{ ucfirst($proveedor->status ?? 'pendiente') }}
                                </x-badge>
                            </td>
                            <td class="text-text-secondary">{{ $proveedor->created_at ? $proveedor->created_at->format('d/m/Y') : 'N/A' }}</td>
                            <td>
                                <x-button variant="outline" size="sm" onclick="window.location.href='{{ $showUrl }}';">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver detalles
                                </x-button>
                            </td>
                        </tr>
                    @endforeach
                </x-table>
            </div>
        </div>

        <div class="mt-6 flex justify-center animate-fade-in" style="animation-delay: 0.2s">
            {{ $proveedores->links() }}
        </div>
        </div>
    </div>
</x-app-layout>