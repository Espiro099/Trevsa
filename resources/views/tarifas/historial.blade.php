@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold mb-2" style="color:var(--brand-black)">Historial de Cálculos de Tarifas</h2>
                <p class="text-gray-600">Registro de cambios en tarifas y cálculos realizados</p>
            </div>
            <a href="{{ route('tarifas.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                ← Volver
            </a>
        </div>

        @if($historial->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Servicio ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Cambios</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Distancia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Costo Diesel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Margen</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($historial as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">
                            {{ optional($item->changed_at)->format('d/m/Y H:i') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ substr($item->servicio_id ?? '-', 0, 8) }}...
                        </td>
                        <td class="px-6 py-4">
                            @if(isset($item->cambios['tarifa_cliente']))
                                <div class="text-xs">
                                    <span class="font-medium">Tarifa Cliente:</span> 
                                    ${{ number_format($item->cambios['tarifa_cliente']['anterior'] ?? 0, 2) }} 
                                    → 
                                    ${{ number_format($item->cambios['tarifa_cliente']['nuevo'] ?? 0, 2) }}
                                </div>
                            @endif
                            @if(isset($item->cambios['tarifa_proveedor']))
                                <div class="text-xs mt-1">
                                    <span class="font-medium">Tarifa Proveedor:</span> 
                                    ${{ number_format($item->cambios['tarifa_proveedor']['anterior'] ?? 0, 2) }} 
                                    → 
                                    ${{ number_format($item->cambios['tarifa_proveedor']['nuevo'] ?? 0, 2) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $item->distancia_km ? number_format($item->distancia_km, 2) . ' km' : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $item->costo_diesel ? '$' . number_format($item->costo_diesel, 2) : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold">
                            {{ $item->margen_calculado ? '$' . number_format($item->margen_calculado, 2) : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $historial->links() }}
        </div>
        @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No hay historial registrado</h3>
            <p class="text-gray-500">El historial aparecerá cuando se realicen cálculos de tarifas.</p>
        </div>
        @endif
    </div>
@endsection
