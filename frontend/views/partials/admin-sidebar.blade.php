@php
    $adminNavigation = [
        ['route' => 'admin.dashboard', 'params' => [], 'label' => 'Dashboard', 'jenis' => 'all'],
        ['route' => 'admin.reports.search', 'params' => [], 'label' => 'Cari laporan', 'jenis' => null],
        ['route' => 'admin.dashboard', 'params' => ['jenis' => 'lost', 'status' => 'all'], 'label' => 'Barang Hilang', 'jenis' => 'lost'],
        ['route' => 'admin.dashboard', 'params' => ['jenis' => 'found', 'status' => 'all'], 'label' => 'Barang Ditemukan', 'jenis' => 'found'],
    ];
@endphp

<aside class="admin-sidebar" id="adminSidebar" aria-label="Navigasi Admin">
    <div class="admin-sidebar-head">
        @include('partials.admin-brand')
        <p class="admin-sidebar-caption">Area verifikasi internal</p>
        <p class="admin-sidebar-user">Halo, {{ Auth::guard('admin')->user()?->name }}</p>
    </div>

    <nav class="admin-nav" aria-label="Menu Admin">
        @foreach ($adminNavigation as $item)
            @php
                $url = route($item['route'], $item['params']);
                $isActive = $item['jenis'] === null
                    ? request()->routeIs($item['route'])
                    : request()->routeIs('admin.dashboard') && request()->query('jenis', 'all') === $item['jenis'];
            @endphp
            <a class="admin-nav-link {{ $isActive ? 'active' : '' }}" href="{{ $url }}"
                @if ($isActive) aria-current="page" @endif>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <form class="admin-logout" method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-light w-100">Keluar Admin</button>
    </form>
</aside>
