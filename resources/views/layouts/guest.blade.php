<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="{{ url('vendor/icomoon-v1.0/style.css') }}?v8">

        <!-- Scripts -->
        {{-- Cargar assets compilados directamente sin Vite --}}
        <link rel="stylesheet" href="{{ url('build/assets/app-fd737ff0.css') }}">
        <script src="{{ url('build/assets/app-f737933f.js') }}" defer></script>
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>

        @include('partials.footer')

    </body>
</html>
