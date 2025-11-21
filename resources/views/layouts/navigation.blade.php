<!-- Sidebar Navigation -->
<aside 
    class="sidebar fixed left-0 top-0 h-screen z-30 bg-bg-glass backdrop-blur-strong border-r border-border-color flex flex-col transition-all duration-300 ease-in-out hidden md:flex shadow-lg"
    :class="sidebarCollapsed ? 'w-16' : 'w-64'"
    style="background: var(--bg-glass); backdrop-filter: var(--backdrop-blur-strong); -webkit-backdrop-filter: var(--backdrop-blur-strong);"
>
    <!-- Logo Section -->
    <div class="flex items-center h-16 px-6 border-b border-border-color relative" style="background: var(--bg-glass-strong);">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
            <x-application-logo class="h-8 w-8 transition-transform duration-200 group-hover:scale-110" />
            <div x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                <span class="text-lg font-bold gradient-text font-display">TREVSA</span>
                <p class="text-xs text-text-muted">LOGISTICS</p>
            </div>
        </a>
        
        <!-- Toggle Button - Siempre visible -->
        <button 
            @click="toggleSidebar"
            class="sidebar-toggle absolute -right-4 top-4 w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-primary-dark transition-all duration-200 shadow-glow hover:shadow-neon active:scale-90 transform z-50"
            :title="sidebarCollapsed ? 'Expandir' : 'Colapsar'"
        >
            <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto scrollbar-hide">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           :title="sidebarCollapsed ? 'Dashboard' : ''">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-2">Dashboard</span>
        </a>

        @if(auth()->check() && optional(auth()->user())->role === 'admin')
            <!-- Transportistas -->
            <a href="{{ route('transportistas.index') }}" 
               class="nav-link {{ request()->routeIs('transportistas.*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Transportistas' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-2">Transportistas</span>
            </a>

            <!-- Registro Servicio Clientes -->
            <a href="{{ route('registro.index') }}" 
               class="nav-link {{ request()->routeIs('registro.*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Servicio Clientes' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-2">Servicio Clientes</span>
            </a>

            <!-- Registro Prospectos Clientes -->
            <a href="{{ route('clientes.index') }}" 
               class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Prospectos Clientes' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-2">Prospectos Clientes</span>
            </a>

            <!-- Prospectos de Proveedores -->
            <a href="{{ route('prospectos_proveedores.index') }}" 
               class="nav-link {{ request()->routeIs('prospectos_proveedores.*') || request()->routeIs('proveedores.*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Prospectos Proveedores' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-2">Prospectos Proveedores</span>
            </a>

            <!-- Registro Transportes Disponibles -->
            <a href="{{ route('unidades.index') }}" 
               class="nav-link {{ request()->routeIs('unidades.*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Unidades Disponibles' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-2">Unidades Disponibles</span>
            </a>

            <!-- Altas Proveedores -->
            <a href="{{ route('altas_proveedores.index') }}" 
               class="nav-link {{ request()->routeIs('altas_proveedores.*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Altas Proveedores' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-2">Altas Proveedores</span>
            </a>

            <!-- Sistema de Cálculo Automático de Tarifas -->
            <a href="{{ route('tarifas.index') }}" 
               class="nav-link {{ request()->routeIs('tarifas.*') ? 'active' : '' }}"
               :title="sidebarCollapsed ? 'Cálculo de Tarifas' : ''">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-2">Cálculo de Tarifas</span>
            </a>
        @endif
    </nav>

    <!-- Bottom Section -->
    <div class="border-t border-border-color p-4" style="background: var(--bg-glass-strong);">
        <a href="{{ route('profile.edit') }}" 
           class="nav-link"
           :title="sidebarCollapsed ? 'Mi Perfil' : ''">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-x-2" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 -translate-x-2">Mi Perfil</span>
        </a>
    </div>
</aside>

<!-- Sidebar Mobile - Ya está manejado en app.blade.php -->

<!-- Overlay para mobile - Ya está manejado en app.blade.php -->
