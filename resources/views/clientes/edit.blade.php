@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4" style="color:var(--brand-black)">Editar Prospecto Cliente</h2>

        <form action="{{ route('clientes.update', $cliente->_id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre Empresa <span class="text-red-500">*</span></label>
                    <input name="nombre_empresa" value="{{ old('nombre_empresa', $cliente->nombre_empresa) }}" class="mt-1 block w-full border-gray-300 rounded p-2" required />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre Contacto</label>
                    <input name="nombre_contacto" value="{{ old('nombre_contacto', $cliente->nombre_contacto) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input name="telefono" value="{{ old('telefono', $cliente->telefono) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $cliente->email) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ciudad</label>
                    <input name="ciudad" value="{{ old('ciudad', $cliente->ciudad) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado</label>
                    <input name="estado" value="{{ old('estado', $cliente->estado) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Industria o tipo de empresa</label>
                    <input name="industria" value="{{ old('industria', $cliente->industria) }}" class="mt-1 block w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Estado Prospecto</label>
                    <select name="estado_prospecto" class="mt-1 block w-full border-gray-300 rounded p-2">
                        <option value="prospecto" {{ old('estado_prospecto', $cliente->estado_prospecto ?? '') == 'prospecto' ? 'selected' : '' }}>Prospecto</option>
                        <option value="activo" {{ old('estado_prospecto', $cliente->estado_prospecto ?? '') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado_prospecto', $cliente->estado_prospecto ?? '') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Comentarios Adicionales</label>
                    <textarea name="comentarios" class="mt-1 block w-full border-gray-300 rounded p-2">{{ old('comentarios', $cliente->comentarios) }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3 responsive-actions">
                <a href="{{ route('clientes.index') }}" class="px-4 py-2 mr-2 border rounded">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-brand-red text-white rounded">Guardar Cambios</button>
            </div>
        </form>
    </div>
@endsection