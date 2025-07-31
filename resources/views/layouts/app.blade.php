<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
<!-- Favicon default 32x32 -->
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/agung/logo-S247.png') }}">
<!-- Ukuran lebih besar -->
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/agung/logo-S247.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/agung/logo-S247.png') }}">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Styles -->
  @livewireStyles

  @stack('styles')
</head>

<body class="font-sans antialiased">
  <x-banner />

  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    @livewire('navigation-menu')

    <!-- Page Heading -->
    @if (isset($header))
    <header class="bg-white shadow dark:bg-gray-800">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      {{ $header }}
      </div>
    </header>
  @endif

    <!-- Page Content -->
    <main>
      {{ $slot }}
    </main>
    @include('layouts.footer')
  </div>


  @stack('modals')

  @livewireScripts

  @stack('scripts')
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</html>