@php
  $sessionConfig = session('config');
  $defaultCustomer = App\Models\Customer::default()->first();

  $config = [
      'customer' => $defaultCustomer ? $defaultCustomer->toArray() : null,
      'change' => $sessionConfig ? ($sessionConfig->change == '0') : false,
      'print' => $sessionConfig ? ($sessionConfig->print == '0') : false,
      'width_ticket' => $sessionConfig ? $sessionConfig->width_ticket : 80,
      'format_percentage_tip' => $sessionConfig ? $sessionConfig->format_percentage_tip : 0,
      'percentage_tip' => $sessionConfig ? $sessionConfig->percentage_tip : 0,
  ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @isset($title)
    <title>{{ $title }}</title>
  @else
    <title>@yield('title')</title>
  @endisset

  <!-- Fonts and styles -->
  <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">
  <link rel="stylesheet" href="{{ url('vendor/icomoon-v1.0/style.css') }}?v9">

  <!-- International Telephone Input -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">

  <style id="page-rule">
    @page {
      size: 80mm 178mm;
      margin: 0cm;
    }
  </style>

  <!-- Scripts -->
  <script src="{{ url('ts/app.js') }}" defer></script>

  {{-- Cargar assets compilados directamente sin Vite --}}
  <link rel="stylesheet" href="{{ url('build/assets/app-fd737ff0.css') }}">
  <link rel="stylesheet" href="{{ url('build/assets/app-b11c4747.css') }}">
  <script src="{{ url('build/assets/app-f737933f.js') }}" defer></script>
   <script src="{{ url('build/assets/app-533acf65.js') }}" defer></script>


  @livewireStyles

</head>

<body class="antialiased scroll-smooth font-manrope">

  <div class="min-h-screen bg-gray-100 no-print text-slate-800">

    <div x-data x-init='$store.config.set({{ json_encode($config) }});'></div>

    <x-loads.alpine />

    <livewire:admin.menu>

    <livewire:admin.cash-opening.create />

      <main class="pt-12 md:pt-14 min-h-screen {{ request()->routeIs('admin.quick-sales.create') || request()->routeIs('admin.direct-sale.create') ? 'md:pl-14' : 'md:pl-52 lg:pl-60' }}">
        {{ $slot }}
      </main>

  </div>

  @include('pdfs.ticket-open-cash-register')
  @include('partials.footer')

  @stack('html')

  @stack('js')

  <!-- International Telephone Input JS -->
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

  @livewireScripts
</body>

</html>
