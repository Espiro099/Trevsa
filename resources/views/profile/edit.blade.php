<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between animate-slide-up">
            <div>
                <h2 class="text-4xl font-bold font-display gradient-text">
                    {{ __('Configuración de Perfil') }}
                </h2>
                <p class="text-sm text-text-muted mt-2">
                    {{ __('Administra tu información personal, contraseña y acciones de tu cuenta.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-0 space-y-8 animate-fade-in">
        <div class="modern-card">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="modern-card">
            @include('profile.partials.update-password-form')
        </div>

        <div class="modern-card border border-error/20">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
