<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Business Frontpage - Start Bootstrap Template</title>
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ Vite::asset('resources/assets-frontoffice/favicon.ico') }}" />
        <!-- Bootstrap icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap) -->
        @vite(['resources/assets-frontoffice/css/styles.css'])
    </head>
    <body>
        <!-- Navbar -->
        @include('frontoffice.partials.navbarfront')

        <!-- Header -->
        @include('frontoffice.partials.header')

        <!-- Content -->
        @yield('content')

        <!-- Footer -->
        @include('frontoffice.partials.footerfront')

        <!-- Bootstrap core JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS -->
        @vite(['resources/assets-frontoffice/js/scripts.js'])
        <!-- SB Forms JS -->
        <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
    </body>
</html>