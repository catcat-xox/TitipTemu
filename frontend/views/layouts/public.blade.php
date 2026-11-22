{{-- Layout untuk halaman Guest dan User. Admin tidak boleh memakai layout ini. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'TitipTemu — Informasi Barang Hilang Kampus')</title>
    <meta name="description" content="@yield('description', 'TitipTemu membantu civitas akademika melaporkan barang temuan dan memudahkan pemiliknya mencari barang.')">

    @vite(['css/app.css', 'js/app.js'])
</head>
<body class="d-flex flex-column min-vh-100">
    <a class="skip-link" href="#konten">Lewati ke konten utama</a>

    @include('partials.navbar-public')

    <main id="konten" class="flex-grow-1">
        @include('partials.flash')

        @yield('content')
    </main>

    @include('partials.footer-public')
</body>
</html>
