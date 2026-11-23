@php
    $navigation = [
        ['route' => 'home', 'label' => 'Beranda'],
        ['route' => 'reports.index', 'params' => ['hilang'], 'label' => 'Barang Hilang'],
        ['route' => 'reports.index', 'params' => ['ditemukan'], 'label' => 'Barang Ditemukan'],
        ['route' => 'guide', 'label' => 'Panduan'],
    ];
@endphp

<header class="site-header">
    <nav class="navbar navbar-expand-lg" aria-label="Navigasi utama">
        <div class="container">
            @include('partials.brand')

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navigasiPublik" aria-controls="navigasiPublik"
                aria-expanded="false" aria-label="Navigasi utama">
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
                    <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-2 nav-actions">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary nav-icon-button"
                            aria-label="Akun" title="Akun">
                            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="3.5"></circle>
                                <path d="M5 20c0-3.6 3.1-5.5 7-5.5s7 1.9 7 5.5"></path>
                            </svg>
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle w-100" type="button"
                                id="buatLaporanMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                Buat Laporan
                            </button>
                            <ul class="dropdown-menu dropdown-menu-lg-end" aria-labelledby="buatLaporanMenu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('missing-reports.create') }}">
                                        Laporkan Kehilangan Barang
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('reports.create') }}">
                                        Laporkan Barang Temuan
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
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
