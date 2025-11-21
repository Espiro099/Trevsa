@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold mb-2" style="color:var(--brand-black)">Gestión de Precio del Diesel</h2>
                <p class="text-gray-600">Actualice el precio del diesel para cálculos automáticos</p>
            </div>
            <a href="{{ route('tarifas.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                ← Volver
            </a>
        </div>

        <!-- Precio Actual -->
        @if($precioActual)
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Precio Actual Vigente</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Precio por Litro</p>
                    <p class="text-3xl font-bold text-green-600">${{ number_format($precioActual->precio_litro, 2) }} MXN</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Precio por Galón</p>
                    <p class="text-3xl font-bold text-green-600">${{ number_format($precioActual->precio_galon, 2) }} MXN</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Vigente desde</p>
                    <p class="text-lg font-semibold text-gray-700">{{ $precioActual->fecha_vigencia->format('d/m/Y') }}</p>
                    @if($precioActual->notas)
                        <p class="text-xs text-gray-500 mt-1">{{ $precioActual->notas }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Formulario para actualizar precio -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Actualizar Precio del Diesel</h3>
            
            <form method="POST" action="{{ route('tarifas.actualizar-precio-diesel') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio por Litro (MXN) *</label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="precio_litro" 
                            value="{{ old('precio_litro', $precioActual->precio_litro ?? 24.50) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-red"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Vigencia *</label>
                        <input 
                            type="date" 
                            name="fecha_vigencia" 
                            value="{{ old('fecha_vigencia', now()->format('Y-m-d')) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-red"
                            required
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notas (opcional)</label>
                        <textarea 
                            name="notas" 
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-red"
                            placeholder="Ej: Precio actualizado según PEMEX..."
                        >{{ old('notas') }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end responsive-actions">
                    <button type="submit" class="px-6 py-2 bg-brand-red text-white rounded-lg hover:bg-red-700">
                        Guardar Nuevo Precio
                    </button>
                </div>
            </form>
        </div>

        <!-- Historial -->
        @if($historial->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Historial de Precios</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Fecha Vigencia</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Precio/Litro</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Precio/Galón</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Notas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($historial as $precio)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $precio->fecha_vigencia->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-semibold">${{ number_format($precio->precio_litro, 2) }}</td>
                            <td class="px-6 py-4">${{ number_format($precio->precio_galon, 2) }}</td>
                            <td class="px-6 py-4">
                                @if($precio->activo)
                                    <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Activo</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-800">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $precio->notas ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $historial->links() }}
            </div>
        </div>
        @endif
    </div>
@endsection
