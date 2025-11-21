@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-black mb-2">Registrar Transportes / Proveedores</h1>
                <p class="text-gray-600">Complete el formulario con la información del proveedor y la documentación requerida</p>
            </div>
            <a href="{{ route('transportes_proveedores.index') }}" class="flex items-center px-4 py-2 text-sm font-medium text-black border border-black rounded-lg hover:bg-black hover:text-white transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <h3 class="text-red-800 font-semibold">Por favor corrige los siguientes errores:</h3>
            </div>
            <ul class="list-disc pl-6 space-y-1 text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transportes_proveedores.store') }}" method="POST" enctype="multipart/form-data" id="proveedorForm">
        @csrf

        <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
            
            <!-- Información Básica -->
            <div class="section-accordion" data-section="basica">
                <div class="section-header bg-brand-black text-white px-6 py-4 cursor-pointer flex items-center justify-between hover:bg-brand-black transition-colors duration-200" role="button" tabindex="0" aria-controls="section-basica" aria-expanded="true">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <h2 class="text-xl font-bold">Información Básica</h2>
                    </div>
                    <svg class="w-5 h-5 transform transition-transform duration-200 section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="section-basica" class="section-content px-6 py-6 border-t border-gray-200" aria-hidden="false">
                    <div class="max-w-md">
                        <div>
                            <label class="block text-sm font-semibold text-black mb-2">
                                Nombre Solicita <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nombre_solicita" value="{{ old('nombre_solicita') }}" 
                                   class="form-input" 
                                   required 
                                   placeholder="Nombre completo" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentos Legales y Contratos -->
            <div class="section-accordion" data-section="documentos">
                <div class="section-header bg-brand-black text-white px-6 py-4 cursor-pointer flex items-center justify-between hover:bg-brand-black transition-colors duration-200" role="button" tabindex="0" aria-controls="section-documentos" aria-expanded="false">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h2 class="text-xl font-bold">Documentos Legales y Contratos</h2>
                    </div>
                    <svg class="w-5 h-5 transform transition-transform duration-200 section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="section-documentos" class="section-content px-6 py-6 border-t border-gray-200 hidden" aria-hidden="true">
                    <div class="space-y-6">
                        <div class="file-upload-group">
                            <label class="block text-sm font-semibold text-black mb-2">
                                Contrato Firmado y Llenado
                                <span class="text-xs font-normal text-gray-500">(hasta 5 archivos: PDF, document, image o spreadsheet, max 10MB c/u)</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="contrato_files[]" multiple 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*,text/plain" 
                                       class="file-input-custom" />
                                <div class="file-input-label">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>Seleccionar archivos</span>
                                </div>
                            </div>
                        </div>

                        <div class="file-upload-group">
                            <label class="block text-sm font-semibold text-black mb-2">
                                Formato Alta Proveedor
                                <span class="text-xs font-normal text-gray-500">(1 archivo: PDF, document o spreadsheet, max 10MB)</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="formato_alta_file" 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv" 
                                       class="file-input-custom" />
                                <div class="file-input-label">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>Seleccionar archivo</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Identificación del Propietario -->
            <div class="section-accordion" data-section="identificacion">
                <div class="section-header bg-brand-black text-white px-6 py-4 cursor-pointer flex items-center justify-between hover:bg-brand-black transition-colors duration-200" role="button" tabindex="0" aria-controls="section-identificacion" aria-expanded="false">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                        <h2 class="text-xl font-bold">Identificación del Propietario</h2>
                    </div>
                    <svg class="w-5 h-5 transform transition-transform duration-200 section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="section-identificacion" class="section-content px-6 py-6 border-t border-gray-200 hidden" aria-hidden="true">
                    <div class="space-y-6">
                        <div class="file-upload-group">
                            <label class="block text-sm font-semibold text-black mb-2">
                                INE del Dueño
                                <span class="text-xs font-normal text-gray-500">(hasta 5 archivos: PDF, document, image o spreadsheet, max 100MB c/u)</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="ine_dueno_files[]" multiple 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*" 
                                       class="file-input-custom" />
                                <div class="file-input-label">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>Seleccionar archivos</span>
                                </div>
                            </div>
                        </div>

                        <div class="file-upload-group">
                            <label class="block text-sm font-semibold text-black mb-2">
                                RFC o Constancia Situación Fiscal
                                <span class="text-xs font-normal text-gray-500">(hasta 5 archivos: PDF, document, image o spreadsheet, max 100MB c/u)</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="rfc_consta_files[]" multiple 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*" 
                                       class="file-input-custom" />
                                <div class="file-input-label">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>Seleccionar archivos</span>
                                </div>
                            </div>
                        </div>

                        <div class="file-upload-group">
                            <label class="block text-sm font-semibold text-black mb-2">
                                Comprobante de Domicilio del Dueño
                                <span class="text-xs font-normal text-gray-500">(1 archivo: PDF, document, image o spreadsheet, max 10MB)</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="comprobante_domicilio_file" 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*" 
                                       class="file-input-custom" />
                                <div class="file-input-label">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>Seleccionar archivo</span>
                                </div>
                            </div>
                        </div>

                        <div class="file-upload-group">
                            <label class="block text-sm font-semibold text-black mb-2">
                                Cuenta Bancaria
                                <span class="text-xs font-normal text-gray-500">(1 archivo: PDF, document, drawing, image o spreadsheet, max 10MB)</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="cuenta_bancaria_file" 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*,.dwg,.dxf" 
                                       class="file-input-custom" />
                                <div class="file-input-label">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>Seleccionar archivo</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unidades y Servicios -->
            <div class="section-accordion" data-section="unidades">
                <div class="section-header bg-brand-black text-white px-6 py-4 cursor-pointer flex items-center justify-between hover:bg-brand-black transition-colors duration-200" role="button" tabindex="0" aria-controls="section-unidades" aria-expanded="false">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <h2 class="text-xl font-bold">Unidades y Servicios</h2>
                    </div>
                    <svg class="w-5 h-5 transform transition-transform duration-200 section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="section-unidades" class="section-content px-6 py-6 border-t border-gray-200 hidden" aria-hidden="true">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-black mb-3">
                                Unidades y Cantidad (seleccione las que apliquen)
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @php
                                    $opciones = ['Caja seca 53 ft','Caja Seca 48 ft','Plataformas','Torton','Rabon','Camioneta 3.5','Plataforma Baja','otros'];
                                @endphp
                                @foreach($opciones as $opt)
                                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 cursor-pointer transition-colors duration-200">
                                        <input type="checkbox" name="unidades[]" value="{{ $opt }}" class="w-4 h-4 text-red-500 border-gray-300 rounded focus:ring-red-500 mr-3">
                                        <span class="text-sm font-medium text-black">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div id="otros_wrap" class="mt-4 hidden">
                                <label class="block text-sm font-semibold text-black mb-2">Especificar Otros</label>
                                <input type="text" name="unidades_otros" 
                                       class="form-input" 
                                       placeholder="Especificar tipo de unidad" />
                            </div>
                        </div>

                        <div class="file-upload-group">
                            <label class="block text-sm font-semibold text-black mb-2">
                                Seguro de Unidades
                                <span class="text-xs font-normal text-gray-500">(hasta 5 archivos: PDF, document, drawing, image o spreadsheet, max 10MB c/u)</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="seguro_unidades_files[]" multiple 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*,.dwg,.dxf" 
                                       class="file-input-custom" />
                                <div class="file-input-label">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>Seleccionar archivos</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentos Vehiculares -->
            <div class="section-accordion" data-section="vehiculos">
                <div class="section-header bg-brand-black text-white px-6 py-4 cursor-pointer flex items-center justify-between hover:bg-brand-black transition-colors duration-200" role="button" tabindex="0" aria-controls="section-vehiculos" aria-expanded="false">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <h2 class="text-xl font-bold">Documentos Vehiculares</h2>
                    </div>
                    <svg class="w-5 h-5 transform transition-transform duration-200 section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="section-vehiculos" class="section-content px-6 py-6 border-t border-gray-200 hidden" aria-hidden="true">
                    <div class="space-y-6">
                        <div class="file-upload-group">
                            <label class="block text-sm font-semibold text-black mb-2">
                                Tarjetas de Circulación (Tractor y Caja o Planas)
                                <span class="text-xs font-normal text-gray-500">(hasta 10 archivos: PDF, document, drawing, image o spreadsheet, max 10MB c/u)</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="tarjetas_circulacion_files[]" multiple 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*,.dwg,.dxf" 
                                       class="file-input-custom" />
                                <div class="file-input-label">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>Seleccionar archivos</span>
                                </div>
                            </div>
                        </div>

                        <div class="file-upload-group">
                            <label class="block text-sm font-semibold text-black mb-2">
                                INE del Conductor
                                <span class="text-xs font-normal text-gray-500">(hasta 5 archivos: PDF, document, drawing, image o spreadsheet, max 10MB c/u)</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="ine_conductor_files[]" multiple 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*,.dwg,.dxf" 
                                       class="file-input-custom" />
                                <div class="file-input-label">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>Seleccionar archivos</span>
                                </div>
                            </div>
                        </div>

                        <div class="file-upload-group">
                            <label class="block text-sm font-semibold text-black mb-2">
                                Licencia Federal Vigente del Conductor
                                <span class="text-xs font-normal text-gray-500">(hasta 5 archivos: PDF, document, drawing, image o spreadsheet, max 10MB c/u)</span>
                            </label>
                            <div class="relative">
                                <input type="file" name="licencia_federal_files[]" multiple 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*,.dwg,.dxf" 
                                       class="file-input-custom" />
                                <div class="file-input-label">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <span>Seleccionar archivos</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="file-upload-group">
                                <label class="block text-sm font-semibold text-black mb-2">
                                    Foto del Tractor con Placa
                                    <span class="text-xs font-normal text-gray-500">(hasta 5 imágenes, max 10MB c/u)</span>
                                </label>
                                <div class="relative">
                                    <input type="file" name="foto_tractor_files[]" multiple 
                                           accept="image/*" 
                                           class="file-input-custom" />
                                    <div class="file-input-label">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Seleccionar imágenes</span>
                                    </div>
                                </div>
                            </div>

                            <div class="file-upload-group">
                                <label class="block text-sm font-semibold text-black mb-2">
                                    Foto de la Caja o Plataforma con Placa
                                    <span class="text-xs font-normal text-gray-500">(hasta 5 imágenes, max 10MB c/u)</span>
                                </label>
                                <div class="relative">
                                    <input type="file" name="foto_caja_files[]" multiple 
                                           accept="image/*" 
                                           class="file-input-custom" />
                                    <div class="file-input-label">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Seleccionar imágenes</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                <a href="{{ route('transportes_proveedores.index') }}" 
                   class="w-full sm:w-auto btn-secondary text-center">
                    Cancelar
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto btn-primary">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Guardar Registro
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>

<style>
/* Estilos para acordeones */
.section-accordion {
    border-bottom: 1px solid rgb(0, 0, 0);
}

.section-accordion:last-child {
    border-bottom: none;
}

.section-content {
    transition: all 0.3s ease;
}

.section-content.hidden {
    display: none;
}

.section-icon.rotate-180 {
    transform: rotate(180deg);
}

/* Accesibilidad: foco visible en headers */
.section-header:focus {
    outline: 2px solid var(--brand-red);
    outline-offset: 2px;
}

/* Estilos para inputs de archivo personalizados */
.file-input-custom {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    z-index: 2;
}

.file-input-label {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    border: 2px dashed #000000;
    border-radius: 0.5rem;
    background-color: #f9fafb;
    color:rgb(0, 0, 0);
    cursor: pointer;
    transition: all 0.2s ease;
}

.file-input-label:hover {
    border-color: #ef4444;
    background-color: #fef2f2;
    color: #dc2626;
}

/* Mejoras adicionales */
input:focus, select:focus, textarea:focus {
    outline: none;
}

input[type="checkbox"]:checked {
    background-color: #dc2626;
    border-color: #dc2626;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Funcionalidad de acordeones
    const sections = document.querySelectorAll('.section-accordion');
    
    sections.forEach(section => {
        const header = section.querySelector('.section-header');
        const content = section.querySelector('.section-content');
        const icon = section.querySelector('.section-icon');
        
        // Primera sección abierta por defecto
        if (section.dataset.section === 'basica') {
            content.classList.remove('hidden');
            icon.classList.add('rotate-180');
            header.setAttribute('aria-expanded', 'true');
            content.setAttribute('aria-hidden', 'false');
        }
        
        function toggleSection() {
            const isHidden = content.classList.contains('hidden');
            if (isHidden) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
                header.setAttribute('aria-expanded', 'true');
                content.setAttribute('aria-hidden', 'false');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
                header.setAttribute('aria-expanded', 'false');
                content.setAttribute('aria-hidden', 'true');
            }
        }

        header.addEventListener('click', toggleSection);
        header.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleSection();
            }
        });
    });

    // Funcionalidad para mostrar/ocultar campo "otros"
    const checkboxes = document.querySelectorAll('input[name="unidades[]"]');
    const otrosWrap = document.getElementById('otros_wrap');
    
    function toggleOtros() {
        let show = false;
        checkboxes.forEach(cb => {
            if (cb.value === 'otros' && cb.checked) {
                show = true;
            }
        });
        
        if (show) {
            otrosWrap.classList.remove('hidden');
        } else {
            otrosWrap.classList.add('hidden');
        }
    }
    
    checkboxes.forEach(cb => cb.addEventListener('change', toggleOtros));
    toggleOtros();

    // Actualizar label de archivos seleccionados
    const fileInputs = document.querySelectorAll('.file-input-custom');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const label = this.nextElementSibling;
            const files = this.files;
            
            if (files && files.length > 0) {
                if (files.length === 1) {
                    label.innerHTML = `<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg><span>${files[0].name}</span>`;
                } else {
                    label.innerHTML = `<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg><span>${files.length} archivos seleccionados</span>`;
                }
                label.classList.add('border-red-500', 'bg-red-50', 'text-red-600');
            }
        });
    });
});
</script>
@endsection