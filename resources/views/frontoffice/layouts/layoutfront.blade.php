<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="description" content=""/>
    <meta name="author" content=""/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Business Frontpage - Start Bootstrap Template</title>

    <link rel="icon" type="image/x-icon" href="{{ Vite::asset('resources/assets-frontoffice/favicon.ico') }}"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet"/>
    @vite(['resources/assets-frontoffice/css/styles.css'])

    {{-- ✅ Les pages peuvent injecter du CSS ici --}}
    @stack('styles')
  </head>

  <body>
    {{-- Navbar --}}
    @include('frontoffice.partials.navbarfront')

    {{-- Header --}}
    @include('frontoffice.partials.header')

    {{-- Content --}}
    @yield('content')

    {{-- Footer --}}
    @include('frontoffice.partials.footerfront')

    {{-- JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/assets-frontoffice/js/scripts.js'])

    {{-- ✅ Les pages peuvent injecter du JS ici --}}
    @stack('scripts')

  <x-ecobot />

  </body>
</html>
