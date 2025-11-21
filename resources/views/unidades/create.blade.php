<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Registrar Unidad Disponible') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Completa el formulario para registrar una nueva unidad disponible</p>
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

                <form action="{{ route('unidades.store') }}" method="POST" id="unidadForm">
                    @csrf

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
                                value=""
                                required
                            >
                            <input type="hidden" name="transporte_proveedor_id" id="transporte_proveedor_id" value="{{ old('transporte_proveedor_id') }}" required>
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
                            <!-- Unidad 1 -->
                            <div class="unidad-item border border-border rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="form-label">Tipo de Unidad</label>
                                        <select name="unidades_disponibles[]" class="form-input unidad-select" onchange="handleUnidadChange(this, 0)">
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
                                <div id="otro-texto-0" class="mt-3 hidden">
                                    <label class="form-label">Especifique el tipo de unidad <span class="text-red-500">*</span></label>
                                    <input type="text" name="unidad_otro_texto[0]" class="form-input" placeholder="Escriba el tipo de unidad">
                                </div>
                            </div>
                        </div>

                        <button type="button" id="add-unidad-btn" class="mt-3 text-sm text-primary hover:underline" onclick="addUnidad()">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Agregar otra unidad
                        </button>
                    </div>

                    <!-- Lugar Disponible -->
                    <div class="mb-6">
                        <label class="form-label">Lugar Disponible</label>
                        <input type="text" name="lugar_disponible" value="{{ old('lugar_disponible') }}" class="form-input" placeholder="Ej: Ciudad de México">
                    </div>

                    <!-- Destino Sugerido -->
                    <div class="mb-6">
                        <label class="form-label">Destino Sugerido</label>
                        <input type="text" name="destino_sugerido" value="{{ old('destino_sugerido') }}" class="form-input" placeholder="Ej: Guadalajara">
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
                                <input type="date" name="fecha_disponible" id="fecha_disponible" value="{{ old('fecha_disponible') }}" class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">Hora Disponible</label>
                                <input type="time" name="hora_disponible" id="hora_disponible" value="{{ old('hora_disponible') }}" class="form-input">
                            </div>
                        </div>
                        <p class="text-xs text-text-muted mt-1">Puede seleccionar una fecha y hora específica o usar el botón para completar automáticamente</p>
                    </div>

                    <!-- Estatus -->
                    <div class="mb-6">
                        <label class="form-label">Estatus</label>
                        <select name="estatus" class="form-input">
                            <option value="disponible" {{ old('estatus', 'disponible') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="reservada" {{ old('estatus') == 'reservada' ? 'selected' : '' }}>Reservada</option>
                            <option value="en_uso" {{ old('estatus') == 'en_uso' ? 'selected' : '' }}>En Uso</option>
                            <option value="no_disponible" {{ old('estatus') == 'no_disponible' ? 'selected' : '' }}>No Disponible</option>
                        </select>
                    </div>

                    <!-- Notas -->
                    <div class="mb-6">
                        <label class="form-label">Notas</label>
                        <textarea name="notas" rows="3" class="form-input" placeholder="Notas adicionales...">{{ old('notas') }}</textarea>
                    </div>

                    <!-- Botones -->
                    <div class="flex justify-end gap-3 mt-8">
                        <x-button variant="outline" type="button" id="cancel-btn">
                            Cancelar
                        </x-button>
                        <x-button variant="primary" type="submit">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar Unidad
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    @php
        $oldTransporteId = old('transporte_proveedor_id');
        
        // Procesar proveedores para JavaScript
        $proveedoresData = $proveedoresAlta->map(function($pa) {
            return [
                'id' => (string)$pa->_id,
                'text' => $pa->proveedor->nombre_empresa ?? 'N/A',
                'nombre_empresa' => $pa->proveedor->nombre_empresa ?? 'N/A',
            ];
        })->values()->toArray();
        
        // Convertir a JSON como string para evitar problemas con el linter
        $proveedoresDataJson = json_encode($proveedoresData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        $oldTransporteIdJson = json_encode($oldTransporteId, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        
        // URL para el botón de cancelar (como JSON string)
        $unidadesIndexUrlJson = json_encode(route('unidades.index'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    @endphp
    <script>
        // Variables de configuración desde PHP
        const oldTransporteProveedorId = JSON.parse('{!! $oldTransporteIdJson !!}');
        
        // Búsqueda de transportista
        let searchTimeout;
        let selectedIndex = -1;
        const transportistaSearch = document.getElementById('transportista-search');
        const transportistaResults = document.getElementById('transportista-results');
        const transportistaLoading = document.getElementById('transportista-loading');
        const transporteProveedorId = document.getElementById('transporte_proveedor_id');
        const searchContainer = document.getElementById('transportista-search-container');

        // Cargar proveedores al inicio
        let proveedoresData = JSON.parse('{!! $proveedoresDataJson !!}');

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
        if (oldTransporteProveedorId) {
            const selectedId = oldTransporteProveedorId;
            const selectedProveedor = proveedoresData.find(p => p.id === selectedId);
            if (selectedProveedor) {
                transportistaSearch.value = selectedProveedor.text;
                transporteProveedorId.value = selectedProveedor.id;
            }
        }

        let unidadCount = 1; // Ya tenemos una unidad inicial

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

        // Manejar "Otros" en la primera unidad si está seleccionada
        document.addEventListener('DOMContentLoaded', function() {
            const firstSelect = document.querySelector('.unidad-select');
            if (firstSelect && firstSelect.value === 'Otros') {
                handleUnidadChange(firstSelect, 0);
            }
            
            // Event listener para el botón de cancelar
            const cancelBtn = document.getElementById('cancel-btn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    window.location.href = JSON.parse('{!! $unidadesIndexUrlJson !!}');
                });
            }
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
                            return false;
                        }
                        // Actualizar el valor del select con el texto ingresado
                        select.value = otroTexto.value.trim();
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
