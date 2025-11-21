<section class="space-y-6">
    <div class="modern-card-header flex items-center gap-3 text-error">
        <div class="p-2 rounded-xl bg-error/10 text-error shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <h2 class="text-lg font-semibold">
                {{ __('Eliminar cuenta') }}
            </h2>
            <p class="text-sm text-text-muted">
                {{ __('Esta acción es irreversible. Asegúrate de respaldar tu información antes de continuar.') }}
            </p>
        </div>
    </div>

    <div class="modern-card-body space-y-6 pt-0">
        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ __('Eliminar cuenta') }}
        </x-danger-button>

        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-6">
                @csrf
                @method('delete')

                <div class="space-y-2">
                    <h2 class="text-lg font-semibold text-text-primary">
                        {{ __('¿Seguro que deseas eliminar tu cuenta?') }}
                    </h2>
                    <p class="text-sm text-text-muted">
                        {{ __('Al eliminar tu cuenta se perderán todos tus datos y recursos de forma permanente. Ingresa tu contraseña para confirmar la eliminación.') }}
                    </p>
                </div>

                <div>
                    <x-input-label for="password" value="{{ __('Contraseña') }}" class="form-label" />
                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-full form-input"
                        placeholder="••••••••"
                    />
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="flex justify-end gap-3 responsive-actions">
                    <x-secondary-button x-on:click="$dispatch('close')">
                        {{ __('Cancelar') }}
                    </x-secondary-button>

                    <x-danger-button class="inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ __('Eliminar definitivamente') }}
                    </x-danger-button>
                </div>
            </form>
        </x-modal>
    </div>
</section>
