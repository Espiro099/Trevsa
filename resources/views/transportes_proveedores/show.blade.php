<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalles del Proveedor') }}
            </h2>
            <a href="{{ route('transportes_proveedores.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información General -->
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-4">Información General</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="font-medium">Nombre Solicita:</label>
                                    <p>{{ $proveedor->nombre_solicita ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="font-medium">Estado:</label>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($proveedor->status === 'aprobado') bg-green-100 text-green-800
                                        @elseif($proveedor->status === 'pendiente') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $proveedor->status ?? 'pendiente' }}
                                    </span>
                                </div>
                                <div>
                                    <label class="font-medium">Fecha de Registro:</label>
                                    <p>{{ $proveedor->created_at ? $proveedor->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Información de Unidades -->
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-4">Unidades y Cantidad</h3>
                            <div class="space-y-3">
                                @if($proveedor->unidades && count($proveedor->unidades) > 0)
                                    <div>
                                        <label class="font-medium">Unidades:</label>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach($proveedor->unidades as $unidad)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                    {{ $unidad }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <p class="text-gray-500">No se han especificado unidades</p>
                                @endif
                                @if($proveedor->unidades_otros)
                                <div>
                                    <label class="font-medium">Otras Unidades:</label>
                                    <p>{{ $proveedor->unidades_otros }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Documentos -->
                        <div class="bg-white p-6 rounded-lg shadow md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Documentos</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- Contrato -->
                                <div class="border p-4 rounded">
                                    <h4 class="font-medium mb-2">Contrato</h4>
                                    @if(!empty($proveedor->contrato_files))
                                        @foreach($proveedor->contrato_files as $file)
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block">
                                                Ver documento {{ $loop->iteration }}
                                            </a>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500">No hay documentos</p>
                                    @endif
                                </div>

                                <!-- Formato de Alta -->
                                <div class="border p-4 rounded">
                                    <h4 class="font-medium mb-2">Formato de Alta</h4>
                                    @if($proveedor->formato_alta_file)
                                        <a href="{{ Storage::url($proveedor->formato_alta_file) }}" target="_blank" class="text-red-600 hover:text-red-800">
                                            Ver documento
                                        </a>
                                    @else
                                        <p class="text-gray-500">No hay documento</p>
                                    @endif
                                </div>

                                <!-- INE del Dueño -->
                                <div class="border p-4 rounded">
                                    <h4 class="font-medium mb-2">INE del Dueño</h4>
                                    @if(!empty($proveedor->ine_dueno_files))
                                        @foreach($proveedor->ine_dueno_files as $file)
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block">
                                                Ver documento {{ $loop->iteration }}
                                            </a>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500">No hay documentos</p>
                                    @endif
                                </div>

                                <!-- RFC -->
                                <div class="border p-4 rounded">
                                    <h4 class="font-medium mb-2">RFC</h4>
                                    @if(!empty($proveedor->rfc_consta_files))
                                        @foreach($proveedor->rfc_consta_files as $file)
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block">
                                                Ver documento {{ $loop->iteration }}
                                            </a>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500">No hay documentos</p>
                                    @endif
                                </div>

                                <!-- Comprobante de Domicilio -->
                                <div class="border p-4 rounded">
                                    <h4 class="font-medium mb-2">Comprobante de Domicilio</h4>
                                    @if($proveedor->comprobante_domicilio_file)
                                        <a href="{{ Storage::url($proveedor->comprobante_domicilio_file) }}" target="_blank" class="text-red-600 hover:text-red-800">
                                            Ver documento
                                        </a>
                                    @else
                                        <p class="text-gray-500">No hay documento</p>
                                    @endif
                                </div>

                                <!-- Cuenta Bancaria -->
                                <div class="border p-4 rounded">
                                    <h4 class="font-medium mb-2">Cuenta Bancaria</h4>
                                    @if($proveedor->cuenta_bancaria_file)
                                        <a href="{{ Storage::url($proveedor->cuenta_bancaria_file) }}" target="_blank" class="text-red-600 hover:text-red-800">
                                            Ver documento
                                        </a>
                                    @else
                                        <p class="text-gray-500">No hay documento</p>
                                    @endif
                                </div>

                                <!-- Seguros de Unidades -->
                                <div class="border p-4 rounded">
                                    <h4 class="font-medium mb-2">Seguros de Unidades</h4>
                                    @if(!empty($proveedor->seguro_unidades_files))
                                        @foreach($proveedor->seguro_unidades_files as $file)
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block">
                                                Ver documento {{ $loop->iteration }}
                                            </a>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500">No hay documentos</p>
                                    @endif
                                </div>

                                <!-- Tarjetas de Circulación -->
                                <div class="border p-4 rounded">
                                    <h4 class="font-medium mb-2">Tarjetas de Circulación</h4>
                                    @if(!empty($proveedor->tarjetas_circulacion_files))
                                        @foreach($proveedor->tarjetas_circulacion_files as $file)
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block">
                                                Ver documento {{ $loop->iteration }}
                                            </a>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500">No hay documentos</p>
                                    @endif
                                </div>

                                <!-- INE del Conductor -->
                                <div class="border p-4 rounded">
                                    <h4 class="font-medium mb-2">INE del Conductor</h4>
                                    @if(!empty($proveedor->ine_conductor_files))
                                        @foreach($proveedor->ine_conductor_files as $file)
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block">
                                                Ver documento {{ $loop->iteration }}
                                            </a>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500">No hay documentos</p>
                                    @endif
                                </div>

                                <!-- Licencia Federal -->
                                <div class="border p-4 rounded">
                                    <h4 class="font-medium mb-2">Licencia Federal</h4>
                                    @if(!empty($proveedor->licencia_federal_files))
                                        @foreach($proveedor->licencia_federal_files as $file)
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block">
                                                Ver documento {{ $loop->iteration }}
                                            </a>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500">No hay documentos</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Fotos de Unidades -->
                            <div class="mt-6">
                                <h4 class="text-lg font-semibold mb-4">Fotos de Unidades</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="border p-4 rounded">
                                        <h5 class="font-medium mb-2">Fotos del Tractor</h5>
                                        <div class="grid grid-cols-2 gap-2">
                                            @if(!empty($proveedor->foto_tractor_files))
                                                @foreach($proveedor->foto_tractor_files as $file)
                                                    <a href="{{ Storage::url($file) }}" target="_blank" class="block">
                                                        <img src="{{ Storage::url($file) }}" alt="Foto del tractor {{ $loop->iteration }}" class="w-full h-32 object-cover rounded">
                                                    </a>
                                                @endforeach
                                            @else
                                                <p class="text-gray-500 col-span-2">No hay fotos</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="border p-4 rounded">
                                        <h5 class="font-medium mb-2">Fotos de la Caja</h5>
                                        <div class="grid grid-cols-2 gap-2">
                                            @if(!empty($proveedor->foto_caja_files))
                                                @foreach($proveedor->foto_caja_files as $file)
                                                    <a href="{{ Storage::url($file) }}" target="_blank" class="block">
                                                        <img src="{{ Storage::url($file) }}" alt="Foto de la caja {{ $loop->iteration }}" class="w-full h-32 object-cover rounded">
                                                    </a>
                                                @endforeach
                                            @else
                                                <p class="text-gray-500 col-span-2">No hay fotos</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>