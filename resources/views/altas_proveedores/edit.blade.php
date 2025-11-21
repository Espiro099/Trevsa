<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Editar Alta - ') }} {{ $prospecto->nombre_empresa }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Edite la documentación del proveedor</p>
            </div>
            <x-button variant="ghost" href="{{ route('altas_proveedores.show', $prospecto->_id) }}">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Detalles
            </x-button>
        </div>
    </x-slot>

    <div class="max-w-full animate-fade-in">
        <div class="max-w-6xl mx-auto">
            <!-- Estado Actual -->
            @if($alta)
            <div class="modern-card animate-slide-up mb-6" style="animation-delay: 0.1s">
                <div class="modern-card-body">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-warning mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <label class="form-label mb-1">Estado actual:</label>
                            @php
                                $estado = strtolower(str_replace(' ', '_', $alta->status ?? 'pendiente'));
                            @endphp
                            <x-badge variant="{{ $estado }}">
                                {{ ucfirst($alta->status ?? 'pendiente') }}
                            </x-badge>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="modern-card mb-6 border-l-4 border-error">
                    <div class="modern-card-body">
                        <div class="flex items-center mb-3">
                            <svg class="w-5 h-5 text-error mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <h3 class="text-error font-semibold">Por favor corrige los siguientes errores:</h3>
                        </div>
                        <ul class="list-disc pl-6 space-y-1 text-text-secondary">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @php
                $opciones = ['Caja seca 53 ft','Caja Seca 48 ft','Plataformas','Torton','Rabon','Camioneta 3.5','Plataforma Baja','otros'];
                $unidadesSeleccionadas = $alta ? ($alta->unidades ?? []) : ($prospecto->tipos_unidades ?? []);
                $cantidadesIniciales = old('cantidades_unidades', $alta ? ($alta->cantidades_unidades ?? []) : ($prospecto->cantidades_unidades ?? []));
                $cantidadesDefault = array_fill_keys($opciones, 1);
                $cantidadesCompletas = array_merge($cantidadesDefault, $cantidadesIniciales);
            @endphp
            <form action="{{ route('altas_proveedores.update', $prospecto->_id) }}" method="POST" enctype="multipart/form-data" id="altaForm"
                  x-data="{ 
                      tiposUnidades: @js($unidadesSeleccionadas),
                      cantidades: @js($cantidadesCompletas),
                      toggleTipo(tipo) {
                          if (this.tiposUnidades.includes(tipo)) {
                              this.tiposUnidades = this.tiposUnidades.filter(t => t !== tipo);
                              delete this.cantidades[tipo];
                          } else {
                              this.tiposUnidades.push(tipo);
                              if (!this.cantidades[tipo] || this.cantidades[tipo] < 1) {
                                  this.cantidades[tipo] = 1;
                              }
                          }
                      },
                      isSelected(tipo) {
                          return this.tiposUnidades.includes(tipo);
                      }
                  }">
                @csrf
                @method('PUT')

                <!-- Información del Prospecto (dentro del formulario) -->
                <div class="modern-card animate-slide-up mb-6">
                    <div class="modern-card-header border-l-4 border-primary">
                        <h3 class="text-lg font-bold text-text-primary">Información del Prospecto</h3>
                    </div>
                    <div class="modern-card-body">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <x-form-input
                                name="prospecto_nombre_empresa"
                                label="Nombre/Empresa"
                                value="{{ old('prospecto_nombre_empresa', $prospecto->nombre_empresa) }}"
                                placeholder="Nombre de la empresa o proveedor"
                                required
                            />
                            <x-form-input
                                name="prospecto_telefono"
                                label="Teléfono"
                                type="tel"
                                value="{{ old('prospecto_telefono', $prospecto->telefono) }}"
                                placeholder="Ej: +52 55 1234 5678"
                            />
                            <x-form-input
                                name="prospecto_email"
                                label="Email"
                                type="email"
                                value="{{ old('prospecto_email', $prospecto->email) }}"
                                placeholder="correo@ejemplo.com"
                            />
                            <x-form-input
                                name="prospecto_cantidad_unidades"
                                label="Cantidad de Unidades"
                                type="number"
                                value="{{ old('prospecto_cantidad_unidades', $prospecto->cantidad_unidades ?? 0) }}"
                                placeholder="0"
                                min="0"
                            />
                            <x-form-input
                                name="prospecto_base_linea_transporte"
                                label="Base Línea o Transporte"
                                value="{{ old('prospecto_base_linea_transporte', $prospecto->base_linea_transporte) }}"
                                placeholder="Base de operaciones"
                            />
                            <x-form-input
                                name="prospecto_corredor_linea_transporte"
                                label="Corredor de la Línea o Transporte"
                                value="{{ old('prospecto_corredor_linea_transporte', $prospecto->corredor_linea_transporte) }}"
                                placeholder="Nombre del corredor"
                            />
                            @if($prospecto->tipos_unidades && count($prospecto->tipos_unidades) > 0)
                            <div class="md:col-span-3">
                                <label class="form-label">Unidades Actuales:</label>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($prospecto->tipos_unidades as $tipo)
                                        <x-badge variant="info">{{ $tipo }}</x-badge>
                                    @endforeach
                                </div>
                                <span class="text-text-secondary ml-2">Cantidad: {{ $prospecto->cantidad_unidades ?? 0 }}</span>
                                <p class="text-xs text-text-muted mt-2">Nota: Las unidades se actualizan en la sección "Unidades y Servicios" más abajo</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modern-card animate-slide-up" style="animation-delay: 0.2s">
                    
                    <!-- Unidades y Servicios -->
                    <div class="section-accordion" data-section="unidades">
                        <div class="section-header bg-gradient-to-r from-primary to-primary-dark px-6 py-4 cursor-pointer flex items-center justify-between hover:from-primary-dark hover:to-primary transition-colors duration-200">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                <h2 class="text-xl font-bold text-white">Unidades y Servicios</h2>
                            </div>
                            <svg class="w-5 h-5 transform transition-transform duration-200 section-icon text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="section-content px-6 py-6 border-t border-border-color">
                            <div class="space-y-6">
                                <div>
                                    <label class="form-label mb-3">
                                        Unidades y Cantidad (seleccione las que apliquen)
                                    </label>
                                    <div class="space-y-3">
                                        @foreach($opciones as $opt)
                                            <div class="flex items-center gap-3 p-3 border-2 border-border-color rounded-lg hover:border-primary transition-all duration-200" 
                                                 :class="isSelected('{{ $opt }}') ? 'border-primary bg-bg-card-hover' : ''">
                                                <label class="flex items-center cursor-pointer flex-1">
                                                    <input type="checkbox" 
                                                           name="unidades[]" 
                                                           value="{{ $opt }}"
                                                           @change="toggleTipo('{{ $opt }}')"
                                                           class="w-4 h-4 text-primary border-border-color rounded focus:ring-primary focus:ring-2 transition-colors duration-200"
                                                           {{ in_array($opt, $unidadesSeleccionadas) ? 'checked' : '' }}
                                                    >
                                                    <span class="ml-3 text-sm font-medium text-text-primary flex-1">{{ $opt }}</span>
                                                </label>
                                                <div x-show="isSelected('{{ $opt }}')" 
                                                     x-transition
                                                     class="flex items-center gap-2">
                                                    <label class="text-xs text-text-muted">Cantidad:</label>
                                                    <input 
                                                        type="number" 
                                                        name="cantidades_unidades[{{ $opt }}]" 
                                                        x-model="cantidades['{{ $opt }}']"
                                                        value="{{ $cantidadesIniciales[$opt] ?? '1' }}"
                                                        min="1"
                                                        class="w-20 px-2 py-1 text-sm form-input"
                                                        placeholder="0"
                                                    >
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div id="otros_wrap" class="mt-4" x-show="isSelected('otros')" x-transition>
                                        <label class="form-label mb-2">Especificar Otros</label>
                                        <input type="text" name="unidades_otros" 
                                               value="{{ old('unidades_otros', $alta->unidades_otros ?? '') }}"
                                               class="form-input" 
                                               placeholder="Especificar tipo de unidad" />
                                    </div>
                                </div>

                                <div class="file-upload-group">
                                    <label class="form-label mb-2">
                                        Seguro de Unidades
                                        <span class="text-xs font-normal text-text-muted">(hasta 5 archivos: PDF, document, drawing, image o spreadsheet, max 10MB c/u)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="seguro_unidades_files[]" multiple 
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*,.dwg,.dxf" 
                                               class="file-input-custom" />
                                        <div class="file-input-label">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span>Seleccionar archivos adicionales</span>
                                        </div>
                                    </div>
                                    @if($alta && !empty($alta->seguro_unidades_files))
                                        <div class="mt-2 p-3 glass-card border-l-4 border-success">
                                            <div class="text-sm font-medium text-success mb-1">Archivos actuales ({{ count($alta->seguro_unidades_files) }}):</div>
                                            <div class="text-xs text-text-secondary">Los nuevos archivos se agregarán a los existentes</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documentos Legales y Contratos -->
                    <div class="section-accordion" data-section="documentos">
                        <div class="section-header bg-gradient-to-r from-bg-secondary to-bg-tertiary px-6 py-4 cursor-pointer flex items-center justify-between hover:from-bg-tertiary hover:to-bg-secondary transition-colors duration-200">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h2 class="text-xl font-bold text-text-primary">Documentos Legales y Contratos</h2>
                            </div>
                            <svg class="w-5 h-5 transform transition-transform duration-200 section-icon text-text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="section-content px-6 py-6 border-t border-border-color hidden">
                            <div class="space-y-6">
                                <div class="file-upload-group">
                                    <label class="form-label mb-2">
                                        Contrato Firmado y Llenado
                                        <span class="text-xs font-normal text-text-muted">(hasta 5 archivos: PDF, document, image o spreadsheet, max 10MB c/u)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="contrato_files[]" multiple 
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*,text/plain" 
                                               class="file-input-custom" />
                                        <div class="file-input-label">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span>Seleccionar archivos adicionales</span>
                                        </div>
                                    </div>
                                    @if($alta && !empty($alta->contrato_files))
                                        <div class="mt-2 p-3 glass-card border-l-4 border-success">
                                            <div class="text-sm font-medium text-success mb-1">Archivos actuales ({{ count($alta->contrato_files) }}):</div>
                                            <div class="text-xs text-text-secondary">Los nuevos archivos se agregarán a los existentes</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="file-upload-group">
                                    <label class="form-label mb-2">
                                        Formato Alta Proveedor
                                        <span class="text-xs font-normal text-text-muted">(1 archivo: PDF, document o spreadsheet, max 10MB)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="formato_alta_file" 
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.csv" 
                                               class="file-input-custom" />
                                        <div class="file-input-label">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span>{{ $alta && $alta->formato_alta_file ? 'Reemplazar archivo' : 'Seleccionar archivo' }}</span>
                                        </div>
                                    </div>
                                    @if($alta && !empty($alta->formato_alta_file))
                                        <div class="mt-2 p-3 glass-card border-l-4 border-success">
                                            <div class="text-sm font-medium text-success">✓ Archivo actual cargado</div>
                                            <div class="text-xs text-text-secondary">Al seleccionar un nuevo archivo, se reemplazará el existente</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Identificación del Propietario -->
                    <div class="section-accordion" data-section="identificacion">
                        <div class="section-header bg-gradient-to-r from-bg-secondary to-bg-tertiary px-6 py-4 cursor-pointer flex items-center justify-between hover:from-bg-tertiary hover:to-bg-secondary transition-colors duration-200">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                </svg>
                                <h2 class="text-xl font-bold text-text-primary">Identificación del Propietario</h2>
                            </div>
                            <svg class="w-5 h-5 transform transition-transform duration-200 section-icon text-text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="section-content px-6 py-6 border-t border-border-color hidden">
                            <div class="space-y-6">
                                <div class="file-upload-group">
                                    <label class="form-label mb-2">
                                        INE del Dueño
                                        <span class="text-xs font-normal text-text-muted">(hasta 5 archivos: PDF, document, image o spreadsheet, max 100MB c/u)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="ine_dueno_files[]" multiple 
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*" 
                                               class="file-input-custom" />
                                        <div class="file-input-label">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span>Seleccionar archivos adicionales</span>
                                        </div>
                                    </div>
                                    @if($alta && !empty($alta->ine_dueno_files))
                                        <div class="mt-2 p-3 glass-card border-l-4 border-success">
                                            <div class="text-sm font-medium text-success mb-1">Archivos actuales ({{ count($alta->ine_dueno_files) }}):</div>
                                            <div class="text-xs text-text-secondary">Los nuevos archivos se agregarán a los existentes</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="file-upload-group">
                                    <label class="form-label mb-2">
                                        RFC o Constancia Situación Fiscal
                                        <span class="text-xs font-normal text-text-muted">(hasta 5 archivos: PDF, document, image o spreadsheet, max 100MB c/u)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="rfc_consta_files[]" multiple 
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*" 
                                               class="file-input-custom" />
                                        <div class="file-input-label">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span>Seleccionar archivos adicionales</span>
                                        </div>
                                    </div>
                                    @if($alta && !empty($alta->rfc_consta_files))
                                        <div class="mt-2 p-3 glass-card border-l-4 border-success">
                                            <div class="text-sm font-medium text-success mb-1">Archivos actuales ({{ count($alta->rfc_consta_files) }}):</div>
                                            <div class="text-xs text-text-secondary">Los nuevos archivos se agregarán a los existentes</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="file-upload-group">
                                    <label class="form-label mb-2">
                                        Comprobante de Domicilio del Dueño
                                        <span class="text-xs font-normal text-text-muted">(1 archivo: PDF, document, image o spreadsheet, max 10MB)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="comprobante_domicilio_file" 
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*" 
                                               class="file-input-custom" />
                                        <div class="file-input-label">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span>{{ $alta && $alta->comprobante_domicilio_file ? 'Reemplazar archivo' : 'Seleccionar archivo' }}</span>
                                        </div>
                                    </div>
                                    @if($alta && !empty($alta->comprobante_domicilio_file))
                                        <div class="mt-2 p-3 glass-card border-l-4 border-success">
                                            <div class="text-sm font-medium text-success">✓ Archivo actual cargado</div>
                                            <div class="text-xs text-text-secondary">Al seleccionar un nuevo archivo, se reemplazará el existente</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="file-upload-group">
                                    <label class="form-label mb-2">
                                        Cuenta Bancaria
                                        <span class="text-xs font-normal text-text-muted">(1 archivo: PDF, document, drawing, image o spreadsheet, max 10MB)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="cuenta_bancaria_file" 
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*,.dwg,.dxf" 
                                               class="file-input-custom" />
                                        <div class="file-input-label">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span>{{ $alta && $alta->cuenta_bancaria_file ? 'Reemplazar archivo' : 'Seleccionar archivo' }}</span>
                                        </div>
                                    </div>
                                    @if($alta && !empty($alta->cuenta_bancaria_file))
                                        <div class="mt-2 p-3 glass-card border-l-4 border-success">
                                            <div class="text-sm font-medium text-success">✓ Archivo actual cargado</div>
                                            <div class="text-xs text-text-secondary">Al seleccionar un nuevo archivo, se reemplazará el existente</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Documentos Vehiculares -->
                    <div class="section-accordion" data-section="vehiculos">
                        <div class="section-header bg-gradient-to-r from-bg-secondary to-bg-tertiary px-6 py-4 cursor-pointer flex items-center justify-between hover:from-bg-tertiary hover:to-bg-secondary transition-colors duration-200">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <h2 class="text-xl font-bold text-text-primary">Documentos Vehiculares</h2>
                            </div>
                            <svg class="w-5 h-5 transform transition-transform duration-200 section-icon text-text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="section-content px-6 py-6 border-t border-border-color hidden">
                            <div class="space-y-6">
                                <div class="file-upload-group">
                                    <label class="form-label mb-2">
                                        Tarjetas de Circulación (Tractor y Caja o Planas)
                                        <span class="text-xs font-normal text-text-muted">(hasta 10 archivos: PDF, document, drawing, image o spreadsheet, max 10MB c/u)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="tarjetas_circulacion_files[]" multiple 
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*,.dwg,.dxf" 
                                               class="file-input-custom" />
                                        <div class="file-input-label">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span>Seleccionar archivos adicionales</span>
                                        </div>
                                    </div>
                                    @if($alta && !empty($alta->tarjetas_circulacion_files))
                                        <div class="mt-2 p-3 glass-card border-l-4 border-success">
                                            <div class="text-sm font-medium text-success mb-1">Archivos actuales ({{ count($alta->tarjetas_circulacion_files) }}):</div>
                                            <div class="text-xs text-text-secondary">Los nuevos archivos se agregarán a los existentes</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="file-upload-group">
                                    <label class="form-label mb-2">
                                        INE del Conductor
                                        <span class="text-xs font-normal text-text-muted">(hasta 5 archivos: PDF, document, drawing, image o spreadsheet, max 10MB c/u)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="ine_conductor_files[]" multiple 
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*,.dwg,.dxf" 
                                               class="file-input-custom" />
                                        <div class="file-input-label">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span>Seleccionar archivos adicionales</span>
                                        </div>
                                    </div>
                                    @if($alta && !empty($alta->ine_conductor_files))
                                        <div class="mt-2 p-3 glass-card border-l-4 border-success">
                                            <div class="text-sm font-medium text-success mb-1">Archivos actuales ({{ count($alta->ine_conductor_files) }}):</div>
                                            <div class="text-xs text-text-secondary">Los nuevos archivos se agregarán a los existentes</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="file-upload-group">
                                    <label class="form-label mb-2">
                                        Licencia Federal Vigente del Conductor
                                        <span class="text-xs font-normal text-text-muted">(hasta 5 archivos: PDF, document, drawing, image o spreadsheet, max 10MB c/u)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="licencia_federal_files[]" multiple 
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,image/*,.dwg,.dxf" 
                                               class="file-input-custom" />
                                        <div class="file-input-label">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span>Seleccionar archivos adicionales</span>
                                        </div>
                                    </div>
                                    @if($alta && !empty($alta->licencia_federal_files))
                                        <div class="mt-2 p-3 glass-card border-l-4 border-success">
                                            <div class="text-sm font-medium text-success mb-1">Archivos actuales ({{ count($alta->licencia_federal_files) }}):</div>
                                            <div class="text-xs text-text-secondary">Los nuevos archivos se agregarán a los existentes</div>
                                        </div>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="file-upload-group">
                                        <label class="form-label mb-2">
                                            Foto del Tractor con Placa
                                            <span class="text-xs font-normal text-text-muted">(hasta 5 imágenes, max 10MB c/u)</span>
                                        </label>
                                        <div class="relative">
                                            <input type="file" name="foto_tractor_files[]" multiple 
                                                   accept="image/*" 
                                                   class="file-input-custom" />
                                            <div class="file-input-label">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>Seleccionar imágenes adicionales</span>
                                            </div>
                                        </div>
                                        @if($alta && !empty($alta->foto_tractor_files))
                                            <div class="mt-2 p-3 glass-card border-l-4 border-success">
                                                <div class="text-sm font-medium text-success mb-1">Imágenes actuales ({{ count($alta->foto_tractor_files) }}):</div>
                                                <div class="text-xs text-text-secondary">Las nuevas imágenes se agregarán a las existentes</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="file-upload-group">
                                        <label class="form-label mb-2">
                                            Foto de la Caja o Plataforma con Placa
                                            <span class="text-xs font-normal text-text-muted">(hasta 5 imágenes, max 10MB c/u)</span>
                                        </label>
                                        <div class="relative">
                                            <input type="file" name="foto_caja_files[]" multiple 
                                                   accept="image/*" 
                                                   class="file-input-custom" />
                                            <div class="file-input-label">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>Seleccionar imágenes adicionales</span>
                                            </div>
                                        </div>
                                        @if($alta && !empty($alta->foto_caja_files))
                                            <div class="mt-2 p-3 glass-card border-l-4 border-success">
                                                <div class="text-sm font-medium text-success mb-1">Imágenes actuales ({{ count($alta->foto_caja_files) }}):</div>
                                                <div class="text-xs text-text-secondary">Las nuevas imágenes se agregarán a las existentes</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="file-upload-group">
                                    <label class="form-label mb-2">
                                        Repuve
                                        <span class="text-xs font-normal text-text-muted">(hasta 10 archivos: PDF o PowerPoint, max 10MB c/u)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="repuve_files[]" multiple 
                                               accept=".pdf,.ppt,.pptx" 
                                               class="file-input-custom" />
                                        <div class="file-input-label">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <span>Seleccionar archivos adicionales</span>
                                        </div>
                                    </div>
                                    @if($alta && !empty($alta->repuve_files))
                                        <div class="mt-2 p-3 glass-card border-l-4 border-success">
                                            <div class="text-sm font-medium text-success mb-1">Archivos actuales ({{ count($alta->repuve_files) }}):</div>
                                            <div class="text-xs text-text-secondary">Los nuevos archivos se agregarán a los existentes</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="modern-card-header flex flex-col sm:flex-row justify-between items-center gap-4">
                        <x-button variant="ghost" href="{{ route('altas_proveedores.show', $prospecto->_id) }}">
                            Cancelar
                        </x-button>
                        <x-button type="submit" variant="primary" size="lg">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Actualizar Registro
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
    /* Estilos para acordeones */
    .section-accordion {
        border-bottom: 1px solid var(--border-color);
    }

    .section-accordion:last-child {
        border-bottom: none;
    }

    .section-content {
        transition: all 0.3s ease;
        background-color: var(--bg-glass);
    }

    .section-content.hidden {
        display: none;
    }

    .section-icon.rotate-180 {
        transform: rotate(180deg);
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
        border: 2px dashed var(--border-color);
        border-radius: 0.5rem;
        background-color: var(--bg-glass);
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .file-input-label:hover {
        border-color: var(--primary);
        background-color: var(--bg-card-hover);
        color: var(--text-primary);
    }

    input:focus, select:focus, textarea:focus {
        outline: none;
    }

    input[type="checkbox"]:checked {
        background-color: var(--primary);
        border-color: var(--primary);
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
            if (section.dataset.section === 'unidades') {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            }
            
            header.addEventListener('click', () => {
                const isHidden = content.classList.contains('hidden');
                
                if (isHidden) {
                    content.classList.remove('hidden');
                    icon.classList.add('rotate-180');
                } else {
                    content.classList.add('hidden');
                    icon.classList.remove('rotate-180');
                }
            });
        });

        // La funcionalidad de mostrar/ocultar "otros" ahora está manejada por Alpine.js

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
</x-app-layout>

