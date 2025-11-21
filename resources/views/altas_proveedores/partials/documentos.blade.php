@php
    use Illuminate\Support\Facades\Storage;
@endphp

<!-- Contrato -->
<div class="border p-4 rounded">
    <h5 class="font-medium mb-2">Contrato</h5>
    @if(!empty($alta->contrato_files))
        @foreach($alta->contrato_files as $file)
            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block text-sm">
                Ver documento {{ $loop->iteration }}
            </a>
        @endforeach
    @else
        <p class="text-gray-500 text-sm">No hay documentos</p>
    @endif
</div>

<!-- Formato de Alta -->
<div class="border p-4 rounded">
    <h5 class="font-medium mb-2">Formato de Alta</h5>
    @if($alta->formato_alta_file)
        <a href="{{ Storage::url($alta->formato_alta_file) }}" target="_blank" class="text-red-600 hover:text-red-800 text-sm">
            Ver documento
        </a>
    @else
        <p class="text-gray-500 text-sm">No hay documento</p>
    @endif
</div>

<!-- INE del Dueño -->
<div class="border p-4 rounded">
    <h5 class="font-medium mb-2">INE del Dueño</h5>
    @if(!empty($alta->ine_dueno_files))
        @foreach($alta->ine_dueno_files as $file)
            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block text-sm">
                Ver documento {{ $loop->iteration }}
            </a>
        @endforeach
    @else
        <p class="text-gray-500 text-sm">No hay documentos</p>
    @endif
</div>

<!-- RFC -->
<div class="border p-4 rounded">
    <h5 class="font-medium mb-2">RFC</h5>
    @if(!empty($alta->rfc_consta_files))
        @foreach($alta->rfc_consta_files as $file)
            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block text-sm">
                Ver documento {{ $loop->iteration }}
            </a>
        @endforeach
    @else
        <p class="text-gray-500 text-sm">No hay documentos</p>
    @endif
</div>

<!-- Comprobante de Domicilio -->
<div class="border p-4 rounded">
    <h5 class="font-medium mb-2">Comprobante de Domicilio</h5>
    @if($alta->comprobante_domicilio_file)
        <a href="{{ Storage::url($alta->comprobante_domicilio_file) }}" target="_blank" class="text-red-600 hover:text-red-800 text-sm">
            Ver documento
        </a>
    @else
        <p class="text-gray-500 text-sm">No hay documento</p>
    @endif
</div>

<!-- Cuenta Bancaria -->
<div class="border p-4 rounded">
    <h5 class="font-medium mb-2">Cuenta Bancaria</h5>
    @if($alta->cuenta_bancaria_file)
        <a href="{{ Storage::url($alta->cuenta_bancaria_file) }}" target="_blank" class="text-red-600 hover:text-red-800 text-sm">
            Ver documento
        </a>
    @else
        <p class="text-gray-500 text-sm">No hay documento</p>
    @endif
</div>

<!-- Seguros de Unidades -->
<div class="border p-4 rounded">
    <h5 class="font-medium mb-2">Seguros de Unidades</h5>
    @if(!empty($alta->seguro_unidades_files))
        @foreach($alta->seguro_unidades_files as $file)
            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block text-sm">
                Ver documento {{ $loop->iteration }}
            </a>
        @endforeach
    @else
        <p class="text-gray-500 text-sm">No hay documentos</p>
    @endif
</div>

<!-- Tarjetas de Circulación -->
<div class="border p-4 rounded">
    <h5 class="font-medium mb-2">Tarjetas de Circulación</h5>
    @if(!empty($alta->tarjetas_circulacion_files))
        @foreach($alta->tarjetas_circulacion_files as $file)
            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block text-sm">
                Ver documento {{ $loop->iteration }}
            </a>
        @endforeach
    @else
        <p class="text-gray-500 text-sm">No hay documentos</p>
    @endif
</div>

<!-- INE del Conductor -->
<div class="border p-4 rounded">
    <h5 class="font-medium mb-2">INE del Conductor</h5>
    @if(!empty($alta->ine_conductor_files))
        @foreach($alta->ine_conductor_files as $file)
            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block text-sm">
                Ver documento {{ $loop->iteration }}
            </a>
        @endforeach
    @else
        <p class="text-gray-500 text-sm">No hay documentos</p>
    @endif
</div>

<!-- Licencia Federal -->
<div class="border p-4 rounded">
    <h5 class="font-medium mb-2">Licencia Federal</h5>
    @if(!empty($alta->licencia_federal_files))
        @foreach($alta->licencia_federal_files as $file)
            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block text-sm">
                Ver documento {{ $loop->iteration }}
            </a>
        @endforeach
    @else
        <p class="text-gray-500 text-sm">No hay documentos</p>
    @endif
</div>

<!-- Repuve -->
<div class="border p-4 rounded">
    <h5 class="font-medium mb-2">Repuve</h5>
    @if(!empty($alta->repuve_files))
        @foreach($alta->repuve_files as $file)
            <a href="{{ Storage::url($file) }}" target="_blank" class="text-red-600 hover:text-red-800 block text-sm">
                Ver documento {{ $loop->iteration }}
            </a>
        @endforeach
    @else
        <p class="text-gray-500 text-sm">No hay documentos</p>
    @endif
</div>

<!-- Fotos de Unidades -->
<div class="border p-4 rounded md:col-span-2 lg:col-span-3">
    <h5 class="font-medium mb-4">Fotos de Unidades</h5>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h6 class="font-medium mb-2">Fotos del Tractor</h6>
            <div class="grid grid-cols-2 gap-2">
                @if(!empty($alta->foto_tractor_files))
                    @foreach($alta->foto_tractor_files as $file)
                        <a href="{{ Storage::url($file) }}" target="_blank" class="block">
                            <img src="{{ Storage::url($file) }}" alt="Foto del tractor {{ $loop->iteration }}" class="w-full h-32 object-cover rounded">
                        </a>
                    @endforeach
                @else
                    <p class="text-gray-500 text-sm col-span-2">No hay fotos</p>
                @endif
            </div>
        </div>
        <div>
            <h6 class="font-medium mb-2">Fotos de la Caja</h6>
            <div class="grid grid-cols-2 gap-2">
                @if(!empty($alta->foto_caja_files))
                    @foreach($alta->foto_caja_files as $file)
                        <a href="{{ Storage::url($file) }}" target="_blank" class="block">
                            <img src="{{ Storage::url($file) }}" alt="Foto de la caja {{ $loop->iteration }}" class="w-full h-32 object-cover rounded">
                        </a>
                    @endforeach
                @else
                    <p class="text-gray-500 text-sm col-span-2">No hay fotos</p>
                @endif
            </div>
        </div>
    </div>
</div>

