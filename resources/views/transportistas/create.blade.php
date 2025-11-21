@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4" style="color:var(--brand-black)">Registrar Transportista</h2>

        <form action="{{ route('transportistas.store') }}" method="POST" x-data="{ tipo:'', showExtra:false }" @change="showExtra = (tipo === 'Especial')">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Transportista</label>
                    <input name="transportista" value="{{ old('transportista') }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                    @error('transportista') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input name="nombre" value="{{ old('nombre') }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                    @error('nombre') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">ESTATUS TPTTES</label>
                    <select name="estatus" class="mt-1 block w-full border-gray-300 rounded p-2" required>
                        <option value="">Seleccione</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                        <option value="Suspendido">Suspendido</option>
                    </select>
                    @error('estatus_tpttes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input name="telefono" value="{{ old('telefono') }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                    @error('telefono') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Qty Unidades 53 ft</label>
                    <input type="number" min="0" name="cantidad_unidades_53ft" value="{{ old('cantidad_unidades_53ft') }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                    @error('cantidad_unidades_53ft') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div x-data @change="tipo = $event.target.value">
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

                <!-- Campo condicional mostrado con Alpine -->
                <div x-show="tipo === 'Especial'" x-cloak class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Detalle Viaje Especial</label>
                    <input name="detalle_especial" class="mt-1 block w-full border-gray-300 rounded p-2" placeholder="Detalles..." />
                </div>
            </div>

            <div class="mt-6 flex justify-end responsive-actions">
                <a href="{{ route('transportistas.index') }}" class="px-4 py-2 mr-2 border rounded">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-brand-red text-white rounded">Guardar</button>
            </div>
        </form>
    </div>
@endsection
