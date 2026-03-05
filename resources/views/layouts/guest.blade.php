<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/icomoon-v1.0/style.css') }}?v8">
    <link rel="icon" type="image/png" href="/favicon.png">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#9d0154]" style="background-color: #9d0154;">
    <div class="font-outfit text-gray-900 antialiased min-h-screen flex flex-col">
        <div class="flex-grow flex items-center justify-center py-10">
            {{ $slot }}
        </div>
        @include('partials.footer')
    </div>

</body>

</html>