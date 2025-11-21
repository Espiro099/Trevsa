@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4" style="color:var(--brand-black)">Crear Tarifa</h2>

        <form action="{{ route('tarifas.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Origen</label>
                    <input name="origen" value="{{ old('origen') }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Destino</label>
                    <input name="destino" value="{{ old('destino') }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Precio</label>
                    <input type="number" step="0.01" name="precio" value="{{ old('precio') }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Moneda</label>
                    <input name="moneda" value="{{ old('moneda', 'MXN') }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 responsive-actions">
                <a href="{{ route('tarifas.index') }}" class="px-4 py-2 mr-2 border rounded">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-brand-red text-white rounded">Guardar</button>
            </div>
        </form>
    </div>
@endsection
