{{-- Layout khusus Admin. Tidak menyertakan navbar/footer publik. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'Panel Admin — TitipTemu')</title>
    <meta name="description" content="Area verifikasi Admin TitipTemu.">

    @vite(['css/app.css', 'js/app.js'])
</head>
<body class="admin-body">
    <a class="skip-link" href="#konten">Lewati ke konten utama</a>

    <div class="admin-shell">
        @include('partials.admin-sidebar')

        <div class="admin-main">
            <main id="konten" class="admin-content" tabindex="-1">
                @include('partials.flash')

                @yield('content')
            </main>
        </div>
    </div>

    @include('partials.confirm-modal')
</body>
</html>
