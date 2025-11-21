<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Transportistas') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Gestión de transportistas</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-full animate-fade-in">
        <div class="modern-card">
            <div class="modern-card-header">
                <h4 class="text-lg font-bold font-display text-text-primary">Listado de Transportistas</h4>
                <p class="text-sm text-text-muted mt-1">Total: {{ $transportistas->total() }} transportistas</p>
            </div>
            <div class="modern-card-body p-0">
                <x-table>
                    <x-slot name="headers">
                        <th>Transportista</th>
                        <th>Nombre</th>
                        <th>Estatus</th>
                        <th>Teléfono</th>
                        <th>Qty 53ft</th>
                        <th>Tipo Viaje</th>
                    </x-slot>
                    @forelse($transportistas as $t)
                        <tr>
                            <td class="font-semibold text-text-primary">{{ $t->transportista }}</td>
                            <td class="text-text-secondary">{{ $t->nombre }}</td>
                            <td>
                                <x-badge variant="{{ $t->estatus == 'activo' ? 'alta' : 'pendiente' }}">
                                    {{ ucfirst($t->estatus ?? 'N/A') }}
                                </x-badge>
                            </td>
                            <td class="text-text-secondary">{{ $t->telefono ?? '-' }}</td>
                            <td class="text-text-secondary">{{ $t->cantidad_unidades_53ft ?? 0 }}</td>
                            <td class="text-text-secondary">{{ $t->tipo_viaje }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-12 text-center" colspan="6">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="text-text-muted font-medium">No hay transportistas registrados</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </x-table>
            </div>
        </div>

        <div class="mt-6 flex justify-center animate-fade-in" style="animation-delay: 0.2s">
            {{ $transportistas->links() }}
        </div>
    </div>
</x-app-layout>
