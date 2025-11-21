<section class="space-y-6">
    <div class="modern-card-header flex items-center gap-3">
        <div class="p-2 rounded-xl bg-gradient-to-br from-secondary to-primary text-white shadow-md">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.657 0-3.343-.879-4.121C10.343 6 8.657 6 7 6c-1.657 0-3.343 0-4.121.879C2 7.657 2 9.343 2 11v2c0 1.657 0 3.343.879 4.121C3.657 18 5.343 18 7 18c1.657 0 3.343 0 4.121-.879C12 16.343 12 14.657 12 13m0-2h3m0 0V9m0 2v2m5-4v4m0 4h-3m3 0h3" />
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-semibold text-text-primary">
                {{ __('Actualizar contraseña') }}
            </h2>
            <p class="text-sm text-text-muted">
                {{ __('Utiliza una contraseña segura y única para proteger tu cuenta.') }}
            </p>
        </div>
    </div>

    <div class="modern-card-body space-y-6 pt-0">
        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            @method('put')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="update_password_current_password" :value="__('Contraseña actual')" class="form-label" />
                    <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full form-input" autocomplete="current-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="update_password_password" :value="__('Nueva contraseña')" class="form-label" />
                    <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full form-input" autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="update_password_password_confirmation" :value="__('Confirmar contraseña')" class="form-label" />
                    <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full form-input" autocomplete="new-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('Guardar contraseña') }}
                </x-primary-button>

                @if (session('status') === 'password-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm font-semibold text-success"
                    >{{ __('Contraseña actualizada.') }}</p>
                @endif
            </div>
        </form>
    </div>
</section>
