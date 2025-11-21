<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Trevsa') }} - Sistema de Gestión Logística</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/css/custom-theme.css', 'resources/js/app.js'])
    </head>
    @php
        $backgroundImage = asset('images/Fondo2.jpg');
    @endphp
    <style>
        html {
            background-image: url('{{ $backgroundImage }}') !important;
            background-size: cover !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
        }
        
        /* Overlay negro sobre la imagen de fondo */
        html::before {
            content: '' !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
            z-index: -1 !important;
            pointer-events: none !important;
        }
    </style>
    <body class="font-sans antialiased">
        
        <!-- Topbar Moderno -->
        <header class="topbar">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <x-application-logo class="h-10 w-10 transition-transform duration-300 group-hover:scale-110" />
                        <div>
                            <span class="text-xl font-bold gradient-text font-display">TREVSA</span>
                            <p class="text-xs text-text-muted hidden md:block">LOGISTICS</p>
                        </div>
                    </a>
                </div>

                <!-- Navegación Desktop -->
                <nav class="hidden md:flex items-center gap-2">

                    @if(auth()->check() && (optional(auth()->user())->hasRole('admin') || optional(auth()->user())->hasPermission('proveedores.view')))

                    <a href="{{ route('prospectos_proveedores.index') }}" 
                           class="nav-link {{ request()->routeIs('prospectos_proveedores.*') || request()->routeIs('proveedores.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            P.Proveedores
                        </a>

                        <a href="{{ route('altas_proveedores.index') }}" 
                           class="nav-link {{ request()->routeIs('altas_proveedores.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Altas Proveedores
                        </a>

                                @if(auth()->check() && optional(auth()->user())->hasPermission('unidades.view'))
                                <a href="{{ route('unidades.index') }}" 
                                class="nav-link {{ request()->routeIs('unidades.*') ? 'active' : '' }}">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Unidades D.
                                </a>
                            @endif

                        <a href="{{ route('clientes.index') }}" 
                           class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            P.Clientes
                        </a>
                    @endif
                </nav>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-4">
                    <!-- Menú Móvil -->
                    <div class="md:hidden relative" x-data="{ open: false }">
                        <button 
                            @click="open = !open"
                            class="p-2 rounded-lg transition-all duration-200 hover:bg-bg-card text-text-secondary hover:text-text-primary"
                            aria-label="Menú"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu Móvil -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="dropdown-menu absolute right-0 mt-2 w-64 max-h-[80vh] overflow-y-auto"
                             style="display: none;">
                            
                            
                            @if(auth()->check() && (optional(auth()->user())->hasRole('admin') || optional(auth()->user())->hasPermission('proveedores.view')))
                                <a href="{{ route('transportistas.index') }}" 
                                   class="dropdown-item flex items-center {{ request()->routeIs('transportistas.*') ? 'bg-bg-glass-strong' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Transportistas
                                </a>
                                
                                <a href="{{ route('registro.index') }}" 
                                   class="dropdown-item flex items-center {{ request()->routeIs('registro.*') ? 'bg-bg-glass-strong' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Servicios
                                </a>
                                
                                <a href="{{ route('clientes.index') }}" 
                                   class="dropdown-item flex items-center {{ request()->routeIs('clientes.*') ? 'bg-bg-glass-strong' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Clientes
                                </a>
                                
                                <a href="{{ route('prospectos_proveedores.index') }}" 
                                   class="dropdown-item flex items-center {{ request()->routeIs('prospectos_proveedores.*') || request()->routeIs('proveedores.*') ? 'bg-bg-glass-strong' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Proveedores
                                </a>
                                
                                <a href="{{ route('unidades.index') }}" 
                                   class="dropdown-item flex items-center {{ request()->routeIs('unidades.*') ? 'bg-bg-glass-strong' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Unidades
                                </a>
                                
                                <a href="{{ route('altas_proveedores.index') }}" 
                                   class="dropdown-item flex items-center {{ request()->routeIs('altas_proveedores.*') ? 'bg-bg-glass-strong' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    Altas
                                </a>
                                
                                <a href="{{ route('tarifas.index') }}" 
                                   class="dropdown-item flex items-center {{ request()->routeIs('tarifas.*') ? 'bg-bg-glass-strong' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Tarifas
                                </a>
                            @endif

                            @if(auth()->check() && optional(auth()->user())->hasPermission('unidades.view'))
                                <a href="{{ route('unidades.index') }}" 
                                   class="dropdown-item flex items-center {{ request()->routeIs('unidades.*') ? 'bg-bg-glass-strong' : '' }}">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Unidades
                                </a>
                            @endif
                            
                            <div class="border-t border-border-color my-1"></div>
                            
                            <a href="{{ route('profile.edit') }}" class="dropdown-item flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ __('Perfil') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item flex items-center w-full text-left text-error">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    {{ __('Cerrar Sesión') }}
                                </button>
                            </form>
                        </div>
                    </div>
                    

                    <!-- User Menu -->
                    <div class="relative hidden md:block" x-data="{ open: false }">
                        <button 
                            @click="open = !open"
                            class="flex items-center gap-2 text-text-secondary hover:text-text-primary transition-all duration-200 p-2 rounded-lg hover:bg-bg-card"
                        >
                            <div class="w-10 h-10 bg-gradient-to-br from-primary to-accent rounded-full flex items-center justify-center text-white font-semibold shadow-lg">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="font-medium hidden lg:block">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="dropdown-menu absolute right-0 mt-2 w-48"
                             style="display: none;">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ __('Perfil') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item flex items-center w-full text-left text-error">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    {{ __('Cerrar Sesión') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="min-h-screen p-6 animate-fade-in">
            @if(isset($header))
                <div class="mb-6 animate-slide-up">
                    {{ $header }}
                </div>
            @elseif(View::hasSection('header'))
                <div class="mb-6 animate-slide-up">
                    @yield('header')
                </div>
            @endif

            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>

        @stack('scripts')
    </body>
</html>
