<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Editar Unidad Disponible') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Modifica la información de la unidad disponible</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto animate-fade-in">
        <div class="modern-card">
            <div class="modern-card-header">
                <h4 class="text-lg font-bold font-display text-text-primary">Información de la Unidad Disponible</h4>
            </div>
            <div class="modern-card-body">
                @if ($errors->any())
                    <div class="alert alert-error mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('unidades.update', $unidad->_id) }}" method="POST" id="unidadForm">
                    @csrf
                    @method('PUT')

                    <!-- Nombre Transportista -->
                    <div class="mb-6">
                        <label class="form-label">Nombre Transportista <span class="text-red-500">*</span></label>
                        <div class="relative" id="transportista-search-container">
                            <input 
                                type="text" 
                                id="transportista-search" 
                                class="form-input" 
                                placeholder="Buscar transportista..." 
                                autocomplete="off"
                                value="{{ old('transporte_proveedor_id') ? ($proveedoresAlta->firstWhere('_id', old('transporte_proveedor_id'))->proveedor->nombre_empresa ?? '') : ($unidad->transporteProveedor && $unidad->transporteProveedor->proveedor ? $unidad->transporteProveedor->proveedor->nombre_empresa : '') }}"
                                required
                            >
                            <input type="hidden" name="transporte_proveedor_id" id="transporte_proveedor_id" value="{{ old('transporte_proveedor_id', $unidad->transporte_proveedor_id) }}" required>
                            <div id="transportista-results" class="hidden absolute z-50 w-full mt-1 bg-bg-glass-strong backdrop-blur-strong border border-border-color rounded-lg shadow-lg max-h-80 overflow-y-auto" style="background: var(--bg-glass-strong); backdrop-filter: var(--backdrop-blur-strong); -webkit-backdrop-filter: var(--backdrop-blur-strong);"></div>
                            <div id="transportista-loading" class="hidden absolute z-50 w-full mt-1 bg-bg-glass-strong backdrop-blur-strong border border-border-color rounded-lg shadow-lg p-4 text-center">
                                <div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-primary"></div>
                                <p class="text-text-muted text-sm mt-2">Buscando...</p>
                            </div>
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                <svg class="w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-text-muted mt-1">Busque o seleccione un transportista de las altas proveedores</p>
                    </div>

                    <!-- Unidades Disponibles (hasta 3) -->
                    <div class="mb-6">
                        <label class="form-label">Unidades Disponibles <span class="text-red-500">*</span></label>
                        <p class="text-xs text-text-muted mb-3">Puede seleccionar hasta 3 unidades. Especifique la cantidad para cada una.</p>
                        
                        <div id="unidades-container" class="space-y-4">
                            @php
                                $unidadesExistentes = old('unidades_disponibles', $unidad->unidades_disponibles ?? []);
                                $cantidadesExistentes = old('cantidades_unidades', $unidad->cantidades_unidades ?? []);
                                $unidadesCount = count($unidadesExistentes) > 0 ? count($unidadesExistentes) : 1;
                            @endphp
                            
                            @for($i = 0; $i < $unidadesCount; $i++)
                                <div class="unidad-item border border-border rounded-lg p-4">
                                    @if($i > 0)
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-sm font-semibold text-text-primary">Unidad {{ $i + 1 }}</span>
                                            <button type="button" class="text-red-500 hover:text-red-700 text-sm" onclick="removeUnidad(this)">
                                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Eliminar
                                            </button>
                                        </div>
                                    @endif
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="form-label">Tipo de Unidad</label>
                                            @php
                                                $unidadActual = $unidadesExistentes[$i] ?? '';
                                                $cantidadActual = isset($cantidadesExistentes[$unidadActual]) ? $cantidadesExistentes[$unidadActual] : (isset($cantidadesExistentes[$i]) ? $cantidadesExistentes[$i] : 1);
                                                $esOtros = $unidadActual && !in_array($unidadActual, ['Caja 53 FT', 'Caja 48 ft', 'Plataforma 40', 'Plataforma 48', 'Plataforma 53', 'Lowboy', 'Camion', 'Rabon', 'Camioneta 3.5', 'Torton']);
                                            @endphp
                                            <select name="unidades_disponibles[]" class="form-input unidad-select" data-index="{{ $i }}" onchange="handleUnidadChange(this, parseInt(this.getAttribute('data-index')))">
                                                <option value="">Seleccione una unidad</option>
                                                <option value="Caja 53 FT" {{ $unidadActual == 'Caja 53 FT' ? 'selected' : '' }}>Caja 53 FT</option>
                                                <option value="Caja 48 ft" {{ $unidadActual == 'Caja 48 ft' ? 'selected' : '' }}>Caja 48 ft</option>
                                                <option value="Plataforma 40" {{ $unidadActual == 'Plataforma 40' ? 'selected' : '' }}>Plataforma 40</option>
                                                <option value="Plataforma 48" {{ $unidadActual == 'Plataforma 48' ? 'selected' : '' }}>Plataforma 48</option>
                                                <option value="Plataforma 53" {{ $unidadActual == 'Plataforma 53' ? 'selected' : '' }}>Plataforma 53</option>
                                                <option value="Lowboy" {{ $unidadActual == 'Lowboy' ? 'selected' : '' }}>Lowboy</option>
                                                <option value="Camion" {{ $unidadActual == 'Camion' ? 'selected' : '' }}>Camion</option>
                                                <option value="Rabon" {{ $unidadActual == 'Rabon' ? 'selected' : '' }}>Rabon</option>
                                                <option value="Camioneta 3.5" {{ $unidadActual == 'Camioneta 3.5' ? 'selected' : '' }}>Camioneta 3.5</option>
                                                <option value="Torton" {{ $unidadActual == 'Torton' ? 'selected' : '' }}>Torton</option>
                                                <option value="Otros" {{ $esOtros ? 'selected' : '' }}>Otros</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="form-label">Cantidad</label>
                                            <input type="number" name="cantidades_unidades[]" min="1" value="{{ old("cantidades_unidades.{$i}", $cantidadActual) }}" class="form-input" placeholder="Cantidad">
                                        </div>
                                    </div>
                                    <div id="otro-texto-{{ $i }}" class="mt-3 {{ $esOtros ? '' : 'hidden' }}">
                                        <label class="form-label">Especifique el tipo de unidad <span class="text-red-500">*</span></label>
                                        <input type="text" name="unidad_otro_texto[{{ $i }}]" class="form-input" placeholder="Escriba el tipo de unidad" value="{{ $esOtros ? $unidadActual : old("unidad_otro_texto.{$i}") }}">
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <button type="button" id="add-unidad-btn" class="mt-3 text-sm text-primary hover:underline {{ $unidadesCount >= 3 ? 'hidden' : '' }}" onclick="addUnidad()">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Agregar otra unidad
                        </button>
                    </div>

                    <!-- Lugar Disponible -->
                    <div class="mb-6">
                        <label class="form-label">Lugar Disponible</label>
                        <input type="text" name="lugar_disponible" value="{{ old('lugar_disponible', $unidad->lugar_disponible) }}" class="form-input" placeholder="Ej: Ciudad de México">
                    </div>

                    <!-- Destino Sugerido -->
                    <div class="mb-6">
                        <label class="form-label">Destino Sugerido</label>
                        <input type="text" name="destino_sugerido" value="{{ old('destino_sugerido', $unidad->destino_sugerido) }}" class="form-input" placeholder="Ej: Guadalajara">
                    </div>

                    <!-- Fecha y Hora Disponible -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <label class="form-label">Fecha y Hora Disponible <span class="text-red-500">*</span></label>
                            </div>
                            <button 
                                type="button" 
                                id="automatizar-fecha-btn"
                                class="text-sm text-primary hover:text-primary-dark flex items-center gap-1 transition-colors duration-200"
                                onclick="automatizarFecha()"
                                title="Completar con fecha y hora actual"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Automatizar Fecha
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Fecha Disponible <span class="text-red-500">*</span></label>
                                <input type="date" name="fecha_disponible" id="fecha_disponible" value="{{ old('fecha_disponible', optional($unidad->fecha_disponible)->toDateString()) }}" class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">Hora Disponible</label>
                                <input type="time" name="hora_disponible" id="hora_disponible" value="{{ old('hora_disponible', $unidad->hora_disponible) }}" class="form-input">
                            </div>
                        </div>
                        <p class="text-xs text-text-muted mt-1">Puede seleccionar una fecha y hora específica o usar el botón para completar automáticamente</p>
                    </div>

                    <!-- Estatus -->
                    <div class="mb-6">
                        <label class="form-label">Estatus</label>
                        <select name="estatus" class="form-input">
                            <option value="disponible" {{ old('estatus', $unidad->estatus ?? 'disponible') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="reservada" {{ old('estatus', $unidad->estatus) == 'reservada' ? 'selected' : '' }}>Reservada</option>
                            <option value="en_uso" {{ old('estatus', $unidad->estatus) == 'en_uso' ? 'selected' : '' }}>En Uso</option>
                            <option value="no_disponible" {{ old('estatus', $unidad->estatus) == 'no_disponible' ? 'selected' : '' }}>No Disponible</option>
                        </select>
                    </div>

                    <!-- Notas -->
                    <div class="mb-6">
                        <label class="form-label">Notas</label>
                        <textarea name="notas" rows="3" class="form-input" placeholder="Notas adicionales...">{{ old('notas', $unidad->notas) }}</textarea>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 mt-8">
                        <x-button variant="outline" type="button" data-href="{{ route('unidades.index') }}" onclick="window.location.href=this.getAttribute('data-href')">
                            Cancelar
                        </x-button>
                        <x-button variant="primary" type="submit">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Actualizar Unidad
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    @php
        // Procesar proveedores para JavaScript
        $proveedoresData = $proveedoresAlta->map(function($pa) {
            return [
                'id' => (string)$pa->_id,
                'text' => $pa->proveedor->nombre_empresa ?? 'N/A',
                'nombre_empresa' => $pa->proveedor->nombre_empresa ?? 'N/A',
            ];
        })->values()->toArray();

        // Cargar proveedor seleccionado si existe
        $selectedId = old('transporte_proveedor_id', $unidad->transporte_proveedor_id);
        $selectedNombre = '';
        if ($selectedId) {
            $selectedProveedorAlta = $proveedoresAlta->firstWhere('_id', $selectedId);
            if ($selectedProveedorAlta && $selectedProveedorAlta->proveedor) {
                $selectedNombre = $selectedProveedorAlta->proveedor->nombre_empresa;
            }
        }

        // Preparar datos para JavaScript
        $proveedoresDataJson = json_encode($proveedoresData);
        $unidadesCountJson = json_encode($unidadesCount);
        $selectedIdJson = $selectedId ? json_encode((string)$selectedId) : 'null';
        $selectedNombreJson = $selectedNombre ? json_encode($selectedNombre) : 'null';
        $hasSelectedProveedor = $selectedId && $selectedNombre;
    @endphp
    <script>
        // Búsqueda de transportista
        let searchTimeout;
        let selectedIndex = -1;
        const transportistaSearch = document.getElementById('transportista-search');
        const transportistaResults = document.getElementById('transportista-results');
        const transportistaLoading = document.getElementById('transportista-loading');
        const transporteProveedorId = document.getElementById('transporte_proveedor_id');
        const searchContainer = document.getElementById('transportista-search-container');

        // Cargar proveedores al inicio
        let proveedoresData = <?php echo $proveedoresDataJson; ?>;

        function mostrarResultados(proveedores, highlightTerm = '') {
            transportistaLoading.classList.add('hidden');
            
            if (proveedores.length === 0) {
                transportistaResults.innerHTML = `
                    <div class="p-4 text-center">
                        <svg class="w-12 h-12 text-text-muted mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-text-muted text-sm">No se encontraron resultados</p>
                        <p class="text-text-muted text-xs mt-1">Intente con otro término de búsqueda</p>
                    </div>
                `;
                transportistaResults.classList.remove('hidden');
                return;
            }

            // Limpiar resultados previos
            transportistaResults.innerHTML = '';
            selectedIndex = -1;
            
            proveedores.forEach((proveedor, index) => {
                const div = document.createElement('div');
                div.className = 'p-3 hover:bg-primary/10 cursor-pointer border-b border-border-color last:border-b-0 transition-colors duration-150 resultado-item';
                div.setAttribute('data-index', index);
                
                // Resaltar término de búsqueda si existe
                let nombreDisplay = proveedor.text;
                if (highlightTerm) {
                    const regex = new RegExp(`(${highlightTerm})`, 'gi');
                    nombreDisplay = proveedor.text.replace(regex, '<mark class="bg-primary/30 text-text-primary">$1</mark>');
                }
                
                div.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="font-semibold text-text-primary">${nombreDisplay}</div>
                            <div class="text-xs text-text-muted mt-1">ID: ${proveedor.id}</div>
                        </div>
                        <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                `;
                
                div.addEventListener('click', function() {
                    seleccionarTransportista(proveedor.id, proveedor.text);
                });
                
                div.addEventListener('mouseenter', function() {
                    selectedIndex = index;
                    actualizarSeleccion();
                });
                
                transportistaResults.appendChild(div);
            });
            
            transportistaResults.classList.remove('hidden');
        }

        function actualizarSeleccion() {
            const items = transportistaResults.querySelectorAll('.resultado-item');
            items.forEach((item, index) => {
                if (index === selectedIndex) {
                    item.classList.add('bg-primary/10');
                } else {
                    item.classList.remove('bg-primary/10');
                }
            });
        }

        function seleccionarTransportista(id, nombre) {
            transporteProveedorId.value = id;
            transportistaSearch.value = nombre;
            transportistaResults.classList.add('hidden');
            transportistaLoading.classList.add('hidden');
            selectedIndex = -1;
        }

        function buscarProveedores(term, mostrarTodos = false) {
            if (mostrarTodos) {
                // Mostrar todos los proveedores
                mostrarResultados(proveedoresData);
                return;
            }

            if (term.length < 1) {
                transportistaResults.classList.add('hidden');
                transportistaLoading.classList.add('hidden');
                if (term.length === 0) {
                    transporteProveedorId.value = '';
                }
                return;
            }

            // Mostrar indicador de carga
            transportistaLoading.classList.remove('hidden');
            transportistaResults.classList.add('hidden');

            clearTimeout(searchTimeout);
            
            searchTimeout = setTimeout(() => {
                // Filtrar localmente primero
                const filtrados = proveedoresData.filter(p => 
                    p.text.toLowerCase().includes(term.toLowerCase()) ||
                    p.id.toLowerCase().includes(term.toLowerCase())
                );

                if (filtrados.length > 0) {
                    mostrarResultados(filtrados, term);
                } else {
                    // Si no hay resultados locales, buscar en el servidor
                    fetch(`{{ route('unidades.buscar_proveedores') }}?term=${encodeURIComponent(term)}`)
                        .then(response => response.json())
                        .then(data => {
                            mostrarResultados(data, term);
                        })
                        .catch(error => {
                            console.error('Error al buscar proveedores:', error);
                            transportistaLoading.classList.add('hidden');
                            transportistaResults.innerHTML = `
                                <div class="p-4 text-center">
                                    <p class="text-error text-sm">Error al buscar proveedores</p>
                                </div>
                            `;
                            transportistaResults.classList.remove('hidden');
                        });
                }
            }, 300);
        }

        // Mostrar todos los proveedores al hacer clic o focus
        transportistaSearch.addEventListener('focus', function() {
            if (transportistaSearch.value.trim().length === 0) {
                mostrarResultados(proveedoresData);
            }
        });

        transportistaSearch.addEventListener('click', function() {
            if (transportistaSearch.value.trim().length === 0) {
                mostrarResultados(proveedoresData);
            }
        });

        transportistaSearch.addEventListener('input', function(e) {
            const term = e.target.value.trim();
            buscarProveedores(term);
        });

        // Navegación con teclado
        transportistaSearch.addEventListener('keydown', function(e) {
            const items = transportistaResults.querySelectorAll('.resultado-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (items.length > 0) {
                    selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                    actualizarSeleccion();
                    items[selectedIndex].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (items.length > 0) {
                    selectedIndex = Math.max(selectedIndex - 1, -1);
                    actualizarSeleccion();
                    if (selectedIndex >= 0) {
                        items[selectedIndex].scrollIntoView({ block: 'nearest' });
                    }
                }
            } else if (e.key === 'Enter' && selectedIndex >= 0 && items[selectedIndex]) {
                e.preventDefault();
                items[selectedIndex].click();
            } else if (e.key === 'Escape') {
                transportistaResults.classList.add('hidden');
                transportistaLoading.classList.add('hidden');
            }
        });

        // Cerrar resultados al hacer click fuera
        document.addEventListener('click', function(e) {
            if (!searchContainer.contains(e.target)) {
                transportistaResults.classList.add('hidden');
                transportistaLoading.classList.add('hidden');
            }
        });

        // Cargar proveedor seleccionado si existe
        <?php if ($hasSelectedProveedor): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const selectedId = <?php echo $selectedIdJson; ?>;
            transportistaSearch.value = <?php echo $selectedNombreJson; ?>;
            transporteProveedorId.value = selectedId;
        });
        <?php endif; ?>

        let unidadCount = <?php echo $unidadesCountJson; ?>;

        function addUnidad() {
            if (unidadCount >= 3) {
                alert('Solo puede agregar hasta 3 unidades');
                return;
            }

            const container = document.getElementById('unidades-container');
            const newUnidad = document.createElement('div');
            newUnidad.className = 'unidad-item border border-border rounded-lg p-4';
            newUnidad.innerHTML = `
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-semibold text-text-primary">Unidad ${unidadCount + 1}</span>
                    <button type="button" class="text-red-500 hover:text-red-700 text-sm" onclick="removeUnidad(this)">
                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Eliminar
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Tipo de Unidad</label>
                        <select name="unidades_disponibles[]" class="form-input unidad-select" onchange="handleUnidadChange(this, ${unidadCount})">
                            <option value="">Seleccione una unidad</option>
                            <option value="Caja 53 FT">Caja 53 FT</option>
                            <option value="Caja 48 ft">Caja 48 ft</option>
                            <option value="Plataforma 40">Plataforma 40</option>
                            <option value="Plataforma 48">Plataforma 48</option>
                            <option value="Plataforma 53">Plataforma 53</option>
                            <option value="Lowboy">Lowboy</option>
                            <option value="Camion">Camion</option>
                            <option value="Rabon">Rabon</option>
                            <option value="Camioneta 3.5">Camioneta 3.5</option>
                            <option value="Torton">Torton</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="cantidades_unidades[]" min="1" value="1" class="form-input" placeholder="Cantidad">
                    </div>
                </div>
                <div id="otro-texto-${unidadCount}" class="mt-3 hidden">
                    <label class="form-label">Especifique el tipo de unidad <span class="text-red-500">*</span></label>
                    <input type="text" name="unidad_otro_texto[${unidadCount}]" class="form-input" placeholder="Escriba el tipo de unidad">
                </div>
            `;
            container.appendChild(newUnidad);
            unidadCount++;

            // Actualizar botón de agregar
            if (unidadCount >= 3) {
                document.getElementById('add-unidad-btn').style.display = 'none';
            }
        }

        function removeUnidad(button) {
            const unidadItem = button.closest('.unidad-item');
            unidadItem.remove();
            unidadCount--;

            // Mostrar botón de agregar si hay menos de 3
            if (unidadCount < 3) {
                document.getElementById('add-unidad-btn').style.display = 'block';
            }
        }

        function handleUnidadChange(select, index) {
            const otroTextoDiv = document.getElementById(`otro-texto-${index}`);
            const otroTextoInput = otroTextoDiv.querySelector('input');
            
            if (select.value === 'Otros') {
                otroTextoDiv.classList.remove('hidden');
                otroTextoInput.required = true;
            } else {
                otroTextoDiv.classList.add('hidden');
                otroTextoInput.required = false;
                otroTextoInput.value = '';
            }
        }

        // Manejar "Otros" en unidades existentes
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('.unidad-select');
            selects.forEach((select, index) => {
                if (select.value === 'Otros') {
                    handleUnidadChange(select, index);
                }
            });
        });

        // Validación del formulario
        document.getElementById('unidadForm').addEventListener('submit', function(e) {
            // Validar transportista
            if (!transporteProveedorId.value || transporteProveedorId.value === '') {
                e.preventDefault();
                alert('Debe seleccionar un transportista');
                transportistaSearch.focus();
                return false;
            }

            // Validar unidades
            const unidades = document.querySelectorAll('select[name="unidades_disponibles[]"]');
            let hasValidUnidad = false;

            unidades.forEach(select => {
                if (select.value && select.value !== '') {
                    hasValidUnidad = true;
                    if (select.value === 'Otros') {
                        const index = Array.from(unidades).indexOf(select);
                        const otroTexto = document.querySelector(`input[name="unidad_otro_texto[${index}]"]`);
                        if (!otroTexto || !otroTexto.value.trim()) {
                            e.preventDefault();
                            alert('Debe especificar el tipo de unidad cuando selecciona "Otros"');
                            otroTexto?.focus();
                            return false;
                        }
                    }
                }
            });

            if (!hasValidUnidad) {
                e.preventDefault();
                alert('Debe seleccionar al menos una unidad disponible');
                return false;
            }
        });

        // Función para automatizar fecha y hora
        function automatizarFecha() {
            const fechaInput = document.getElementById('fecha_disponible');
            const horaInput = document.getElementById('hora_disponible');
            
            if (!fechaInput || !horaInput) {
                return;
            }

            // Obtener fecha y hora actual
            const ahora = new Date();
            
            // Formatear fecha como YYYY-MM-DD
            const año = ahora.getFullYear();
            const mes = String(ahora.getMonth() + 1).padStart(2, '0');
            const dia = String(ahora.getDate()).padStart(2, '0');
            const fechaFormateada = `${año}-${mes}-${dia}`;
            
            // Formatear hora como HH:MM
            const horas = String(ahora.getHours()).padStart(2, '0');
            const minutos = String(ahora.getMinutes()).padStart(2, '0');
            const horaFormateada = `${horas}:${minutos}`;
            
            // Asignar valores a los campos
            fechaInput.value = fechaFormateada;
            horaInput.value = horaFormateada;
            
            // Agregar efecto visual de confirmación
            const btn = document.getElementById('automatizar-fecha-btn');
            if (btn) {
                const originalText = btn.innerHTML;
                const originalClass = btn.className;
                btn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #10b981;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span style="color: #10b981;">Completado</span>
                `;
                btn.style.color = '#10b981';
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.className = originalClass;
                    btn.style.color = '';
                }, 2000);
            }
        }
    </script>
    @endpush
</x-app-layout>
