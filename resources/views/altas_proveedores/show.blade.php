<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Detalles de Alta - ') }} {{ $prospecto->nombre_empresa }}
                </h2>
                <p class="text-sm text-text-muted mt-2">Información del prospecto y su proceso de alta</p>
            </div>
            <div class="flex gap-3">
                @if($alta)
                    <x-button variant="primary" href="{{ route('altas_proveedores.edit', $prospecto->_id) }}">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar Alta
                    </x-button>
                @else
                    <x-button variant="primary" href="{{ route('altas_proveedores.create', $prospecto->_id) }}">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Crear Alta
                    </x-button>
                @endif
                <x-button variant="ghost" href="{{ route('altas_proveedores.index') }}">
                    Volver al listado
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-full animate-fade-in">
        <div class="max-w-7xl mx-auto">
            <!-- Mensajes de Error -->
            @if ($errors->any())
                <div class="modern-card mb-6 border-l-4 border-error animate-slide-in-right">
                    <div class="modern-card-body">
                        <div class="flex items-center mb-3">
                            <svg class="w-5 h-5 text-error mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <h3 class="text-error font-semibold">Error</h3>
                        </div>
                        @if(session('documentos_faltantes'))
                            <p class="text-text-secondary mb-2">Faltan los siguientes documentos requeridos:</p>
                            <ul class="list-disc pl-6 space-y-1 text-text-secondary">
                                @foreach(session('documentos_faltantes') as $doc)
                                    <li>{{ $doc }}</li>
                                @endforeach
                            </ul>
                        @else
                            <ul class="list-disc pl-6 space-y-1 text-text-secondary">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Información del Prospecto -->
            <div class="modern-card animate-slide-up mb-6">
                <div class="modern-card-header">
                    <h3 class="text-lg font-bold text-text-primary">Información del Prospecto</h3>
                </div>
                <div class="modern-card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Nombre/Empresa:</label>
                            <p class="text-text-primary font-medium">{{ $prospecto->nombre_empresa ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="form-label">Teléfono:</label>
                            <p class="text-text-primary font-medium">{{ $prospecto->telefono ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="form-label">Email:</label>
                            <p class="text-text-primary font-medium">{{ $prospecto->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="form-label">Cantidad de Unidades:</label>
                            <p class="text-text-primary font-medium">{{ $prospecto->cantidad_unidades ?? 0 }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="form-label">Tipos de Unidades:</label>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @if($prospecto->tipos_unidades && count($prospecto->tipos_unidades) > 0)
                                    @foreach($prospecto->tipos_unidades as $tipo)
                                        <x-badge variant="info">{{ $tipo }}</x-badge>
                                    @endforeach
                                @else
                                    <span class="text-text-muted">Sin unidades especificadas</span>
                                @endif
                            </div>
                        </div>
                        @if($prospecto->base_linea_transporte)
                        <div>
                            <label class="form-label">Base Línea Transporte:</label>
                            <p class="text-text-primary font-medium">{{ $prospecto->base_linea_transporte }}</p>
                        </div>
                        @endif
                        @if($prospecto->corredor_linea_transporte)
                        <div>
                            <label class="form-label">Corredor Línea Transporte:</label>
                            <p class="text-text-primary font-medium">{{ $prospecto->corredor_linea_transporte }}</p>
                        </div>
                        @endif
                        @if($prospecto->nombre_quien_registro)
                        <div>
                            <label class="form-label">Registrado por:</label>
                            <p class="text-text-primary font-medium">{{ $prospecto->nombre_quien_registro }}</p>
                        </div>
                        @endif
                        @if($prospecto->notas)
                        <div class="md:col-span-2">
                            <label class="form-label">Notas:</label>
                            <p class="text-text-secondary">{{ $prospecto->notas }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Modal de Credenciales Nuevas -->
            @if(session('nuevas_credenciales'))
                @php
                    $credenciales = session('nuevas_credenciales');
                @endphp
                <div id="modal-credenciales" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 animate-fade-in" x-data="{ open: true }" x-show="open" x-cloak style="backdrop-filter: blur(4px);">
                    <div class="bg-gray-900 border-2 border-red-500 rounded-xl shadow-2xl max-w-lg w-full mx-4 animate-scale-in" @click.away="open = false" style="box-shadow: 0 0 30px rgba(255, 0, 0, 0.5);">
                        <div class="bg-gradient-to-r from-red-600 to-red-800 p-6 rounded-t-xl border-b-2 border-red-500">
                            <div class="flex items-center justify-between">
                                <h3 class="text-2xl font-bold text-white flex items-center gap-2">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Credenciales de Acceso
                                </h3>
                                <button @click="open = false" class="text-white hover:text-gray-200 transition-colors">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="p-6 bg-gray-900">
                            <div class="mb-6 p-4 bg-yellow-900 bg-opacity-50 border-2 border-yellow-500 rounded-lg">
                                <div class="flex items-start gap-2">
                                    <svg class="w-6 h-6 text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <p class="text-yellow-400 font-bold text-base mb-1">⚠️ IMPORTANTE</p>
                                        <p class="text-yellow-100 text-sm">
                                            Guarda estas credenciales de forma segura. El transportista necesitará estas credenciales para acceder al sistema.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Empresa:</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" value="{{ $credenciales['nombre_empresa'] }}" readonly 
                                               class="flex-1 px-4 py-3 border-2 border-gray-700 rounded-lg bg-gray-800 text-white font-semibold text-base"
                                               id="empresa-input" style="color: #ffffff !important;">
                                        <button onclick="copiarAlPortapapeles('empresa-input')" 
                                                class="px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Email:</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" value="{{ $credenciales['email'] }}" readonly 
                                               class="flex-1 px-4 py-3 border-2 border-gray-700 rounded-lg bg-gray-800 text-white font-mono text-base"
                                               id="email-input" style="color: #ffffff !important;">
                                        <button onclick="copiarAlPortapapeles('email-input')" 
                                                class="px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Contraseña Temporal:</label>
                                    <div class="flex items-center gap-2">
                                        <input type="text" value="{{ $credenciales['password'] }}" readonly 
                                               class="flex-1 px-4 py-3 border-2 border-red-500 rounded-lg bg-gray-800 text-red-400 font-mono text-lg font-bold tracking-wider"
                                               id="password-input" style="color: #f87171 !important; letter-spacing: 0.1em;">
                                        <button onclick="copiarAlPortapapeles('password-input')" 
                                                class="px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex gap-3">
                                <button @click="open = false" 
                                        class="flex-1 px-4 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors font-semibold">
                                    Entendido
                                </button>
                                <button onclick="copiarTodasLasCredenciales()" 
                                        class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold">
                                    Copiar Todo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <script>
                    function copiarAlPortapapeles(inputId) {
                        const input = document.getElementById(inputId);
                        if (!input) return;
                        
                        input.select();
                        input.setSelectionRange(0, 99999); // Para móviles
                        
                        try {
                            navigator.clipboard.writeText(input.value).then(() => {
                                // Mostrar notificación
                                const button = event.target.closest('button');
                                if (button) {
                                    const originalHTML = button.innerHTML;
                                    button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                                    button.classList.remove('bg-red-600', 'hover:bg-red-700');
                                    button.classList.add('bg-green-500');
                                    
                                    setTimeout(() => {
                                        button.innerHTML = originalHTML;
                                        button.classList.remove('bg-green-500');
                                        button.classList.add('bg-red-600', 'hover:bg-red-700');
                                    }, 2000);
                                }
                            });
                        } catch (err) {
                            // Fallback para navegadores antiguos
                            document.execCommand('copy');
                            const button = event.target.closest('button');
                            if (button) {
                                const originalHTML = button.innerHTML;
                                button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                                button.classList.remove('bg-red-600', 'hover:bg-red-700');
                                button.classList.add('bg-green-500');
                                
                                setTimeout(() => {
                                    button.innerHTML = originalHTML;
                                    button.classList.remove('bg-green-500');
                                    button.classList.add('bg-red-600', 'hover:bg-red-700');
                                }, 2000);
                            }
                        }
                    }
                    
                    function copiarTodasLasCredenciales() {
                        const empresa = document.getElementById('empresa-input');
                        const email = document.getElementById('email-input');
                        const password = document.getElementById('password-input');
                        
                        if (!empresa || !email || !password) return;
                        
                        const texto = `Credenciales de Acceso - ${empresa.value}\n\nEmail: ${email.value}\nContraseña: ${password.value}\n\n⚠️ IMPORTANTE: Guarda estas credenciales de forma segura.`;
                        
                        navigator.clipboard.writeText(texto).then(() => {
                            alert('✅ Todas las credenciales han sido copiadas al portapapeles');
                        }).catch(() => {
                            alert('Error al copiar. Por favor, copia manualmente.');
                        });
                    }
                </script>
                
                @php
                    session()->forget('nuevas_credenciales');
                @endphp
            @endif

            <!-- Información de la Alta -->
            @if($alta)
                <div class="modern-card animate-slide-up" style="animation-delay: 0.2s">
                    <div class="modern-card-header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-text-primary">Información de Alta</h3>
                            @php
                                $estado = strtolower(str_replace(' ', '_', $alta->status ?? 'pendiente'));
                            @endphp
                            <x-badge variant="{{ $estado }}">
                                Estado: {{ ucfirst($alta->status ?? 'pendiente') }}
                            </x-badge>
                        </div>
                    </div>
                    <div class="modern-card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Información General de Alta -->
                            <div class="glass-card">
                                <h4 class="font-semibold mb-4 text-text-primary">Datos Generales</h4>
                                <div class="space-y-3">
                                    <div>
                                        <label class="form-label">Nombre Solicita:</label>
                                        <p class="text-text-primary font-medium">{{ $alta->nombre_solicita ?? $prospecto->nombre_empresa }}</p>
                                    </div>
                                    @if($alta->unidades && count($alta->unidades) > 0)
                                    <div>
                                        <label class="form-label">Unidades:</label>
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach($alta->unidades as $unidad)
                                                <x-badge variant="primary">{{ $unidad }}</x-badge>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    @if($alta->unidades_otros)
                                    <div>
                                        <label class="form-label">Otras Unidades:</label>
                                        <p class="text-text-primary font-medium">{{ $alta->unidades_otros }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Estado de Documentos -->
                            <div class="glass-card">
                                <h4 class="font-semibold mb-4 text-text-primary">Estado de Documentación</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-text-secondary">Contrato:</span>
                                        <span class="{{ !empty($alta->contrato_files) ? 'text-green-400' : 'text-red-400' }}">
                                            {{ !empty($alta->contrato_files) ? '✓' : '✗' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-text-secondary">Formato Alta:</span>
                                        <span class="{{ !empty($alta->formato_alta_file) ? 'text-green-400' : 'text-red-400' }}">
                                            {{ !empty($alta->formato_alta_file) ? '✓' : '✗' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-text-secondary">INE Dueño:</span>
                                        <span class="{{ !empty($alta->ine_dueno_files) ? 'text-green-400' : 'text-red-400' }}">
                                            {{ !empty($alta->ine_dueno_files) ? '✓' : '✗' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-text-secondary">RFC:</span>
                                        <span class="{{ !empty($alta->rfc_consta_files) ? 'text-green-400' : 'text-red-400' }}">
                                            {{ !empty($alta->rfc_consta_files) ? '✓' : '✗' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-text-secondary">Comprobante Domicilio:</span>
                                        <span class="{{ !empty($alta->comprobante_domicilio_file) ? 'text-green-400' : 'text-red-400' }}">
                                            {{ !empty($alta->comprobante_domicilio_file) ? '✓' : '✗' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-text-secondary">Cuenta Bancaria:</span>
                                        <span class="{{ !empty($alta->cuenta_bancaria_file) ? 'text-green-400' : 'text-red-400' }}">
                                            {{ !empty($alta->cuenta_bancaria_file) ? '✓' : '✗' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Credenciales de Usuario -->
                        @if($usuario)
                            <div class="mt-6 pt-6 border-t-2 border-red-500 border-opacity-30">
                                <h4 class="text-xl font-bold mb-4 text-white flex items-center gap-3">
                                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    <span class="text-white">Credenciales de Acceso del Transportista</span>
                                </h4>
                                <div class="bg-gray-900 border-2 border-red-500 rounded-xl p-6" style="background: rgba(17, 24, 39, 0.95); backdrop-filter: blur(10px);">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-300 mb-2">Email:</label>
                                            <div class="flex items-center gap-2">
                                                <input type="text" value="{{ $usuario->email }}" readonly 
                                                       class="flex-1 px-4 py-3 border-2 border-gray-700 rounded-lg bg-gray-800 text-white font-mono text-base"
                                                       id="usuario-email-{{ $usuario->_id }}" style="color: #ffffff !important;">
                                                <button onclick="copiarAlPortapapeles('usuario-email-{{ $usuario->_id }}')" 
                                                        class="px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
                                                        title="Copiar email">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-300 mb-2">Rol:</label>
                                            <div class="mt-2">
                                                <span class="inline-block px-4 py-2 bg-red-600 text-white rounded-lg font-semibold text-sm">
                                                    {{ ucfirst($usuario->role ?? 'transportista') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-5 p-4 bg-yellow-900 bg-opacity-50 border-2 border-yellow-500 rounded-lg">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            <p class="text-sm text-yellow-100">
                                                <strong class="text-yellow-300">Nota:</strong> La contraseña no se muestra por seguridad. Si el transportista olvidó su contraseña, puede usar la función de recuperación de contraseña o contactar al administrador.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Documentos -->
                        <div class="mt-6">
                            <h4 class="text-lg font-bold mb-4 text-text-primary">Documentos Cargados</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @include('altas_proveedores.partials.documentos', ['alta' => $alta])
                            </div>
                        </div>

                        @if($alta->status !== 'alta')
                            @php
                                $documentosFaltantes = $alta->validarDocumentosRequeridos();
                                $puedeDarAlta = empty($documentosFaltantes);
                            @endphp
                            
                            @if(!$puedeDarAlta)
                                <div class="mt-6 pt-6 border-t border-border-color">
                                    <div class="alert alert-warning mb-4">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-5 h-5 text-warning mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <h3 class="font-semibold text-warning">No se puede dar de alta al proveedor</h3>
                                        </div>
                                        <p class="text-text-secondary mb-2">Faltan los siguientes documentos requeridos:</p>
                                        <ul class="list-disc pl-6 space-y-1 text-text-secondary">
                                            @foreach($documentosFaltantes as $doc)
                                                <li>{{ $doc }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <x-button variant="ghost" href="{{ route('altas_proveedores.edit', $prospecto->_id) }}">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Completar Documentación
                                    </x-button>
                                </div>
                            @else
                                <div class="mt-6 pt-6 border-t border-border-color">
                                    <div class="alert alert-success mb-4">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-success mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <p class="text-success font-medium">Todos los documentos requeridos están completos. Puede proceder a dar de alta al proveedor.</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('altas_proveedores.dar_alta', $prospecto->_id) }}" method="POST">
                                        @csrf
                                        <x-button type="submit" variant="primary" size="lg"
                                                onclick="return confirm('¿Está seguro de dar de alta a este proveedor? Esta acción no se puede deshacer.')">
                                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Dar de Alta
                                        </x-button>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @else
                <div class="modern-card animate-slide-up" style="animation-delay: 0.2s">
                    <div class="modern-card-body">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-warning" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-text-secondary">
                                    Este prospecto aún no tiene un registro de alta. 
                                    <a href="{{ route('altas_proveedores.create', $prospecto->_id) }}" class="font-medium text-primary hover:text-primary-light underline">
                                        Crear registro de alta
                                    </a>
                                    para completar la documentación.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Función global para copiar al portapapeles (disponible en toda la página)
        function copiarAlPortapapeles(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            
            input.select();
            input.setSelectionRange(0, 99999); // Para móviles
            document.execCommand('copy');
            
            // Mostrar notificación
            const button = event.target.closest('button');
            if (button) {
                const originalHTML = button.innerHTML;
                button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                button.classList.add('bg-green-500');
                
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('bg-green-500');
                }, 2000);
            }
        }
    </script>
</x-app-layout>

