{{-- Layout khusus form masuk Admin. Tidak memuat navigasi publik maupun sidebar Admin. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'Masuk Admin — TitipTemu')</title>
    <meta name="description" content="@yield('description', 'Halaman masuk khusus Admin TitipTemu.')">

    @vite(['css/app.css', 'js/app.js'])
</head>
<body class="admin-auth-body">
    <a class="skip-link" href="#konten">Lewati ke konten utama</a>

    <main id="konten" class="admin-auth-main">
        <div class="admin-auth-brand">
            @include('partials.admin-brand')
        </div>

        @include('partials.flash')

        @yield('content')
    </main>
</body>
</html>
