@extends('errors::minimal')

@section('title', __('Página no encontrada'))
@section('code', '404')
@section('message')
    <div class="space-y-2">
        <span>{{ __('No encontramos la página que buscabas.') }}</span>
        @isset($path)
            <span class="block text-sm text-gray-400">{{ __('Ruta solicitada: :path', ['path' => $path]) }}</span>
        @endisset
        @isset($requestId)
            <span class="block text-xs text-gray-500">{{ __('ID de seguimiento: :id', ['id' => $requestId]) }}</span>
        @endisset
    </div>
@endsection

