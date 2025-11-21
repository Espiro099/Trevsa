<section class="space-y-6">
    <div class="modern-card-header flex items-center gap-3">
        <div class="p-2 rounded-xl bg-gradient-to-br from-primary to-accent text-white shadow-md">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A1 1 0 015 17V7a2 2 0 012-2h3l2-2h2l2 2h3a2 2 0 012 2v10a1 1 0 01-.121.485m-13.758 0A1 1 0 007 18h10a1 1 0 00.879-.516m-13.758 0L5 17m13.758 0L19 17"/>
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-text-primary">
                {{ __('Información del Perfil') }}
            </h2>
            <p class="text-sm text-text-muted">
                {{ __('Actualiza tu información personal y dirección de correo.') }}
            </p>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <div class="modern-card-body space-y-6 pt-0">
        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('patch')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="name" :value="__('Nombre')" class="form-label" />
                    <x-text-input 
                        id="name" 
                        name="name" 
                        type="text" 
                        class="mt-1 block w-full form-input" 
                        :value="old('name', $user->name)" 
                        required 
                        autofocus 
                        autocomplete="name" 
                        placeholder="{{ __('Nombre completo') }}"
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Correo electrónico')" class="form-label" />
                    <x-text-input 
                        id="email" 
                        name="email" 
                        type="email" 
                        class="mt-1 block w-full form-input" 
                        :value="old('email', $user->email)" 
                        required 
                        autocomplete="username" 
                        placeholder="nombre@ejemplo.com"
                    />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="bg-amber-50 border border-amber-200 text-amber-700 rounded-xl px-4 py-3 mt-4">
                            <p class="text-sm font-medium flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('Tu correo electrónico no está verificado.') }}
                            </p>
                            <button 
                                form="send-verification" 
                                class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-amber-700 hover:text-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-400 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M20 4l-8 8-4-4-4 4" />
                                </svg>
                                {{ __('Reenviar correo de verificación') }}
                            </button>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 text-xs text-amber-600 font-medium">
                                    {{ __('Se ha enviado un nuevo enlace de verificación a tu correo electrónico.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('Guardar cambios') }}
                </x-primary-button>

                @if (session('status') === 'profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm font-semibold text-success"
                    >{{ __('Cambios guardados correctamente.') }}</p>
                @endif
            </div>
        </form>
    </div>
</section>
