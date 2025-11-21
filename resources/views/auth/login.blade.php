<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-md w-full">
            <!-- Logo y Título -->
            <div class="text-center mb-8 animate-fade-in">
                <div class="flex justify-center mb-4">
                    <img class="h-16 w-auto object-contain" 
                         src="{{ asset('images/Trevsa.jpeg') }}" 
                         alt="Trevsa Logo"
                         style="filter: drop-shadow(0 2px 10px rgba(255, 0, 0, 0.3));">
                </div>
                <h2 class="text-3xl font-bold font-display gradient-text mb-2">
                    {{ __('Iniciar Sesión') }}
                </h2>
                <p class="text-text-muted text-sm">
                    Sistema de Gestión Logística
                </p>
            </div>

            <!-- Card de Login -->
             
            <div class="glass-card animate-slide-up" style="animation-delay: 0.1s">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />
                

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="form-label">
                            {{ __('Email') }}
                        </label>
                        <input id="email" 
                               class="form-input"
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="username"
                               placeholder="correo@ejemplo.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="form-label">
                                {{ __('Contraseña') }}
                            </label>
                            @if (Route::has('password.request'))
                                <a class="text-xs text-primary hover:text-primary-light transition-colors duration-200" 
                                   href="{{ route('password.request') }}">
                                    {{ __('¿Olvidaste tu contraseña?') }}
                                </a>
                            @endif
                        </div>
                        <input id="password" 
                               class="form-input"
                               type="password"
                               name="password"
                               required 
                               autocomplete="current-password"
                               placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" 
                               name="remember" 
                               type="checkbox" 
                               class="w-4 h-4 text-primary border-border-color rounded focus:ring-primary focus:ring-2 bg-bg-glass cursor-pointer">
                        <label for="remember_me" class="ml-2 block text-sm text-text-secondary cursor-pointer">
                            {{ __('Recordarme') }}
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="btn-primary w-full">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            {{ __('Iniciar Sesión') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6 animate-fade-in" style="animation-delay: 0.3s">
                <p class="text-xs text-text-muted">
                    © {{ date('Y') }} Trevsa. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
