@php
    $navigation = [
        ['route' => 'home', 'label' => 'Beranda'],
        ['route' => 'reports.index', 'params' => ['lost'], 'label' => 'Barang Hilang'],
        ['route' => 'reports.index', 'params' => ['found'], 'label' => 'Barang Ditemukan'],
        ['route' => 'guide', 'label' => 'Panduan'],
    ];
@endphp

<header class="site-header">
    <nav class="navbar navbar-expand-lg" aria-label="Navigasi utama">
        <div class="container">
            @include('partials.brand')

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navigasiPublik" aria-controls="navigasiPublik"
                aria-expanded="false" aria-label="Buka navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navigasiPublik">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    @foreach ($navigation as $item)
                        @if (Route::has($item['route']))
                            @php
                                $isActive = request()->routeIs($item['route'])
                                    && (! isset($item['params'][0]) || request()->route('type') === $item['params'][0]);
                            @endphp
                            <li class="nav-item">
                                <a class="nav-link {{ $isActive ? 'active' : '' }}"
                                    href="{{ route($item['route'], $item['params'] ?? []) }}"
                                    @if ($isActive) aria-current="page" @endif>
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>

                @if (auth('web')->check())
                    <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">Akun</a>
                        <span class="nav-user">Halo, {{ auth('web')->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100">Keluar</button>
                        </form>
                    </div>
                    <a href="{{ route('reports.create') }}" class="btn btn-primary ms-lg-2 mt-2 mt-lg-0">Laporkan Barang Temuan</a>
                @else
                    <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
                    </div>
                @endif
            </div>
        </div>
    </nav>
</header>
