@php
    $footerNavigation = [
        ['route' => 'home', 'label' => 'Beranda'],
        ['route' => 'reports.index', 'params' => ['hilang'], 'label' => 'Barang Hilang'],
        ['route' => 'reports.index', 'params' => ['ditemukan'], 'label' => 'Barang Ditemukan'],
        ['route' => 'guide', 'label' => 'Panduan'],
    ];
@endphp

<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                @include('partials.brand')
                <p class="mt-3 mb-0" style="max-width: 24rem;">
                    Portal pelaporan barang temuan dan pencarian barang
                    untuk lingkungan kampus.
                </p>
            </div>

            <div class="col-6 col-lg-3">
                <h2 class="footer-heading">Navigasi</h2>
                <ul class="footer-links">
                    @foreach ($footerNavigation as $item)
                        @if (Route::has($item['route']))
                            <li><a href="{{ route($item['route'], $item['params'] ?? []) }}">{{ $item['label'] }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>

            <div class="col-6 col-lg-4">
                <h2 class="footer-heading">Tentang</h2>
                <p class="mb-0">
                    Laporan kami periksa sebelum ditampilkan. Bukti pengembalian
                    bersifat privat dan hanya digunakan untuk verifikasi.
                </p>
            </div>
        </div>

        <div class="footer-bottom d-flex flex-column flex-sm-row justify-content-between gap-2">
            <span>&copy; {{ now()->year }} TitipTemu</span>
            <span>Dikembangkan oleh Kelompok 6 Opensource</span>
        </div>
    </div>
</footer>
