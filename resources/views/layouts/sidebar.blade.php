{{-- resources/views/backend/layouts/sidebar.blade.php --}}
<div class="sidebar-area" id="sidebar-area">
    <div class="logo position-relative">
        <a href="{{ route('dashboard') }}" class="d-block text-decoration-none">
            <img src="{{ asset('backend/assets/images/LOGO-KERIPIK-KELADI.jpg') }}" width="80" alt="logo-icon">
        </a>
        <button
            class="sidebar-burger-menu bg-transparent p-0 border-0 opacity-0 z-n1 position-absolute top-50 end-0 translate-middle-y"
            id="sidebar-burger-menu">
            <i data-feather="x"></i>
        </button>
    </div>
    <aside id="layout-menu" class="layout-menu menu-vertical menu active" data-simplebar>
        <ul class="menu-inner">
            <!-- Dashboard -->
            <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i data-feather="grid" class="menu-icon tf-icons"></i>
                    <span class="title">Dashboard</span>
                </a>
            </li>

            @if (auth()->check() && auth()->user()->isPenjual())
                
                <!-- Menu DATA MASTER -->
                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">DATA MASTER</span>
                </li>

                <!-- Jenis Keripik -->
                <li class="menu-item {{ request()->routeIs('jenis-keripik.*') ? 'active' : '' }}">
                    <a href="{{ route('jenis-keripik.index') }}" class="menu-link">
                        <i data-feather="shopping-bag" class="menu-icon tf-icons"></i>
                        <span class="title">Jenis Keripik</span>
                    </a>
                </li>

                <!-- Bahan Baku -->
                <li class="menu-item {{ request()->routeIs('bahan-baku.*') ? 'active' : '' }}">
                    <a href="{{ route('bahan-baku.index') }}" class="menu-link">
                        <i data-feather="package" class="menu-icon tf-icons"></i>
                        <span class="title">Bahan Baku</span>
                    </a>
                </li>

                <!-- Komposisi -->
                <li class="menu-item {{ request()->routeIs('komposisi.*') ? 'active' : '' }}">
                    <a href="{{ route('komposisi.index') }}" class="menu-link">
                        <i data-feather="git-merge" class="menu-icon tf-icons"></i>
                        <span class="title">Biaya Produksi</span>
                    </a>
                </li>

                <!-- Menu STOK -->
                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">STOK</span>
                </li>

                <!-- Stok Keripik -->
                <li class="menu-item {{ request()->routeIs('stok-keripik.*') ? 'active' : '' }}">
                    <a href="{{ route('stok-keripik.index') }}" class="menu-link">
                        <i data-feather="box" class="menu-icon tf-icons"></i>
                        <span class="title">Stok Keripik</span>
                    </a>
                </li>

                <!-- Stok Bahan Baku -->
                <li class="menu-item {{ request()->routeIs('stok-bahan-baku.*') ? 'active' : '' }}">
                    <a href="{{ route('stok-bahan-baku.index') }}" class="menu-link">
                        <i data-feather="archive" class="menu-icon tf-icons"></i>
                        <span class="title">Stok Bahan Baku</span>
                    </a>
                </li>

                <!-- Menu TRANSAKSI -->
                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">TRANSAKSI</span>
                </li>

                <!-- Penjualan -->
                <li class="menu-item {{ request()->routeIs('penjualan.*') ? 'active' : '' }}">
                    <a href="{{ route('penjualan.index') }}" class="menu-link">
                        <i data-feather="shopping-cart" class="menu-icon tf-icons"></i>
                        <span class="title">Penjualan</span>
                    </a>
                </li>
            @endif

            @if(auth()->check() && auth()->user()->isOwner())

                <li class="menu-title small text-uppercase">
                    <span class="menu-title-text">LAPORAN</span>
                </li>

                <li class="menu-item {{ request()->routeIs('laporan.index') ? 'active' : '' }}">
                    <a href="{{ route('laporan.index') }}" class="menu-link">
                        <i data-feather="file-text" class="menu-icon tf-icons"></i>
                        <span class="title">Lihat Laporan</span>
                    </a>
                </li>

                <!-- Laporan Laba Rugi -->
                <li class="menu-item {{ request()->routeIs('laba-rugi') ? 'active' : '' }}">
                    <a href="{{ route('laba-rugi') }}" class="menu-link">
                        <i data-feather="trending-up" class="menu-icon tf-icons"></i>
                        <span class="title">Laba Rugi</span>
                    </a>
                </li>


                <!-- Dashboard Keuangan -->
                <li class="menu-item {{ request()->routeIs('laporan.dashboard-keuangan') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-keuangan') }}" class="menu-link">
                        <i data-feather="bar-chart-2" class="menu-icon tf-icons"></i>
                        <span class="title">Dashboard Keuangan</span>
                    </a>
                </li>

                
            @endif

            <!-- Laporan Penjualan -->
            

            <!-- Menu MANAJEMEN USER (Hanya untuk Owner) -->
            @if(auth()->check() && auth()->user()->isOwner())
            {{-- <li class="menu-title small text-uppercase">
                <span class="menu-title-text">MANAJEMEN</span>
            </li>

            <!-- Data User -->
            <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <a href="{{ route('users.index') }}" class="menu-link">
                    <i data-feather="users" class="menu-icon tf-icons"></i>
                    <span class="title">Data User</span>
                </a>
            </li> --}}
            @endif

            <!-- Menu SETTINGS -->
            {{-- <li class="menu-title small text-uppercase">
                <span class="menu-title-text">SETTINGS</span>
            </li> --}}

            <!-- Profil -->
            {{-- <li class="menu-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                <a href="{{ route('profile') }}" class="menu-link">
                    <i data-feather="user" class="menu-icon tf-icons"></i>
                    <span class="title">Profil</span>
                </a>
            </li> --}}

            <!-- Logout -->
            <li class="menu-item">
                <a href="#" class="menu-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i data-feather="log-out" class="menu-icon tf-icons"></i>
                    <span class="title">Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </aside>
</div>