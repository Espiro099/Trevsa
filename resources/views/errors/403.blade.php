@extends('layouts.app')

@section('title', 'Acceso Denegado')
@section('content')
<div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded shadow text-center">
        <h1 class="text-4xl font-bold text-red-600 mb-4">403</h1>
        <h2 class="text-2xl font-semibold mb-2">Acceso Denegado</h2>
        <p class="mb-4">No tienes permisos para acceder a esta página.</p>
        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-brand-red text-white rounded">Ir al Dashboard</a>
    </div>
</div>
@endsection
