@php
    $adminNavigation = [
        ['route' => 'admin.dashboard', 'status' => null, 'label' => 'Dashboard'],
        ['route' => 'admin.reports.search', 'status' => null, 'label' => 'Cari laporan'],
        ['route' => 'admin.dashboard', 'status' => 'pending', 'label' => 'Menunggu verifikasi'],
        ['route' => 'admin.dashboard', 'status' => 'approved', 'label' => 'Masih dicari'],
        ['route' => 'admin.dashboard', 'status' => 'return_pending', 'label' => 'Pengembalian diperiksa'],
        ['route' => 'admin.dashboard', 'status' => 'returned', 'label' => 'Sudah kembali'],
        ['route' => 'admin.dashboard', 'status' => 'archived', 'label' => 'Diarsipkan'],
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
                $url = route($item['route'], $item['status'] ? ['status' => $item['status']] : []);
                $isActive = request()->routeIs($item['route'])
                    && (
                        $item['route'] === 'admin.reports.search'
                        || request()->query('status') === $item['status']
                    );
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
