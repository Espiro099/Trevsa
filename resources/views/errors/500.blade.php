@extends('errors::minimal')

@section('title', __('Error interno del servidor'))
@section('code', '500')
@section('message')
    <div class="space-y-2">
        <span>{{ __('Ha ocurrido un error inesperado en el sistema.') }}</span>
        <span class="block text-sm text-gray-400">
            {{ __('Nuestro equipo ha sido notificado. Intenta nuevamente más tarde o contacta al soporte si el problema persiste.') }}
        </span>
        @isset($requestId)
            <span class="block text-xs text-gray-500">{{ __('ID de seguimiento: :id', ['id' => $requestId]) }}</span>
        @endisset
    </div>
@endsection

