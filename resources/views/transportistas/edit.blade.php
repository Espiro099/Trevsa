@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4" style="color:var(--brand-black)">Editar Transportista</h2>

        <form action="{{ route('transportistas.update', $transportista->_id) }}" method="POST" x-data="{ tipo: '{{ $transportista->tipo_viaje }}' }">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Transportista</label>
                    <input name="transportista" value="{{ old('transportista', $transportista->transportista) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                    @error('transportista') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input name="nombre" value="{{ old('nombre', $transportista->nombre) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                    @error('nombre') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">ESTATUS TPTTES</label>
                    <select name="estatus" class="mt-1 block w-full border-gray-300 rounded p-2" required>
                        <option value="">Seleccione</option>
                        <option value="activo" {{ (old('estatus', $transportista->estatus ?? '') == 'activo') ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ (old('estatus', $transportista->estatus ?? '') == 'inactivo') ? 'selected' : '' }}>Inactivo</option>
                        <option value="suspendido" {{ (old('estatus', $transportista->estatus ?? '') == 'suspendido') ? 'selected' : '' }}>Suspendido</option>
                    </select>
                    @error('estatus') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input name="telefono" value="{{ old('telefono', $transportista->telefono) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                    @error('telefono') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Qty Unidades 53 ft</label>
                    <input type="number" min="0" name="cantidad_unidades_53ft" value="{{ old('cantidad_unidades_53ft', $transportista->cantidad_unidades_53ft ?? 0) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                    @error('cantidad_unidades_53ft') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo Viaje</label>
                    <select name="tipo_viaje" x-model="tipo" class="mt-1 block w-full border-gray-300 rounded p-2">
                        <option value="">Seleccione</option>
                        <option value="Local">Local</option>
                        <option value="Nacional">Nacional</option>
                        <option value="Internacional">Internacional</option>
                        <option value="Especial">Especial</option>
                    </select>
                    @error('tipo_viaje') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div x-show="tipo === 'Especial'" x-cloak class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Detalle Viaje Especial</label>
                    <input name="detalle_especial" value="{{ old('detalle_especial', $transportista->detalle_especial ?? '') }}" class="mt-1 block w-full border-gray-300 rounded p-2" placeholder="Detalles..." />
                </div>
            </div>

            <div class="mt-6 flex justify-between gap-4 responsive-actions">
                <div class="flex items-center gap-3 responsive-actions">
                    <form action="{{ route('transportistas.destroy', $transportista->_id) }}" method="POST" onsubmit="return confirm('Eliminar este transportista?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Eliminar</button>
                    </form>
                </div>

                <div class="flex items-center gap-3 responsive-actions">
                    <a href="{{ route('transportistas.index') }}" class="px-4 py-2 mr-2 border rounded">Cancelar</a>
                    <button type="submit" class="px-4 py-2 bg-brand-red text-white rounded">Guardar</button>
                </div>
            </div>
        </form>
    </div>
@endsection
