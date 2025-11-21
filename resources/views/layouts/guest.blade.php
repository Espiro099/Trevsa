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
    <body class="font-sans antialiased login-page">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative" style="position: relative; z-index: 1;">
            {{ $slot }}
        </div>
    </body>
</html>
