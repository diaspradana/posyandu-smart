<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Dashboard') - Posyandu Smart
    </title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/dashboard.css') }}"
    >

    @yield('head')
</head>

<body>

<div class="app-wrapper">

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar">

        <div class="sidebar-header">

            <div class="brand-icon">
                <span>🏥</span>
            </div>

            <div class="brand-text">
                <strong>POSYANDU SMART</strong>
                <span>{{ auth()->user()->role === 'kader' ? 'KADER POSYANDU' : 'ADMIN PUSKESMAS' }}</span>
            </div>

        </div>

        <div class="sidebar-profile">

            <div class="profile-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>

            <div class="profile-info">

                <strong>
                    {{ auth()->user()->name ?? 'Admin Puskesmas' }}
                </strong>

                <span>
                    {{ auth()->user()->role === 'kader' ? 'Kader Posyandu' : 'Admin Puskesmas' }}
                </span>

            </div>

        </div>

        <nav class="sidebar-nav">

            @if(auth()->user()->role === 'kader')
                {{-- ==================== KADER POSYANDU SIDEBAR ==================== --}}
                {{-- 1. Dashboard --}}
                <a
                    href="{{ route('kader.dashboard') }}"
                    class="nav-item {{ request()->routeIs('kader.dashboard') || request()->routeIs('dashboard') ? 'active' : '' }}"
                >
                    <span class="nav-icon">🏠</span>
                    <span>Dashboard</span>
                </a>

                {{-- 2. Master Data (Dropdown Accordion) --}}
                @php
                    $isDataWargaActive = request()->routeIs('kader.warga.balita') || request()->routeIs('kader.warga.ibu-hamil');
                @endphp
                <div class="nav-dropdown {{ $isDataWargaActive ? 'open active' : '' }}">
                    <button type="button" class="nav-dropdown-toggle {{ $isDataWargaActive ? 'active' : '' }}">
                        <span class="nav-icon">👥</span>
                        <span>Master Data</span>
                        <span class="dropdown-arrow">▾</span>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a
                            href="{{ route('kader.warga.balita') }}"
                            class="dropdown-item {{ request()->routeIs('kader.warga.balita') ? 'active' : '' }}"
                        >
                            <span class="dropdown-bullet">├─</span>
                            <span>Balita</span>
                        </a>

                        <a
                            href="{{ route('kader.warga.ibu-hamil') }}"
                            class="dropdown-item {{ request()->routeIs('kader.warga.ibu-hamil') ? 'active' : '' }}"
                        >
                            <span class="dropdown-bullet">└─</span>
                            <span>Ibu Hamil</span>
                        </a>
                    </div>
                </div>

                {{-- 3. Pemeriksaan (Dropdown Accordion) --}}
                @php
                    $isPemeriksaanKaderActive = request()->routeIs('kader.kegiatan.pemeriksaan*');
                @endphp
                <div class="nav-dropdown {{ $isPemeriksaanKaderActive ? 'open active' : '' }}">
                    <button type="button" class="nav-dropdown-toggle {{ $isPemeriksaanKaderActive ? 'active' : '' }}">
                        <span class="nav-icon">🩺</span>
                        <span>Pemeriksaan</span>
                        <span class="dropdown-arrow">▾</span>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a
                            href="{{ route('kader.kegiatan.pemeriksaan', ['tab' => 'balita']) }}"
                            class="dropdown-item {{ request()->routeIs('kader.kegiatan.pemeriksaan') && request('tab', 'balita') === 'balita' ? 'active' : '' }}"
                        >
                            <span class="dropdown-bullet">├─</span>
                            <span>Pemeriksaan Balita</span>
                        </a>

                        <a
                            href="{{ route('kader.kegiatan.pemeriksaan', ['tab' => 'ibu_hamil']) }}"
                            class="dropdown-item {{ request()->routeIs('kader.kegiatan.pemeriksaan') && request('tab') === 'ibu_hamil' ? 'active' : '' }}"
                        >
                            <span class="dropdown-bullet">└─</span>
                            <span>Pemeriksaan Ibu Hamil</span>
                        </a>
                    </div>
                </div>

                {{-- 4. Monitoring (Dropdown Accordion) --}}
                @php
                    $isMonitoringKaderActive = request()->routeIs('kader.monitoring*') || request()->routeIs('kader.ai-detail*');
                @endphp
                <div class="nav-dropdown {{ $isMonitoringKaderActive ? 'open active' : '' }}">
                    <button type="button" class="nav-dropdown-toggle {{ $isMonitoringKaderActive ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        <span>Monitoring AI</span>
                        <span class="dropdown-arrow">▾</span>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a
                            href="{{ route('kader.monitoring.balita') }}"
                            class="dropdown-item {{ request()->routeIs('kader.monitoring.balita') || request()->routeIs('kader.monitoring-stunting') || (request()->routeIs('kader.ai-detail*') && !request()->routeIs('kader.ai-detail.ibu-hamil')) ? 'active' : '' }}"
                        >
                            <span class="dropdown-bullet">├─</span>
                            <span>Monitoring Balita</span>
                        </a>

                        <a
                            href="{{ route('kader.monitoring.ibu-hamil') }}"
                            class="dropdown-item {{ request()->routeIs('kader.monitoring.ibu-hamil') || request()->routeIs('kader.ai-detail.ibu-hamil') ? 'active' : '' }}"
                        >
                            <span class="dropdown-bullet">└─</span>
                            <span>Monitoring Ibu Hamil</span>
                        </a>
                    </div>
                </div>

                {{-- 5. Kehadiran --}}
                <a
                    href="{{ route('kader.kegiatan.kehadiran') }}"
                    class="nav-item {{ request()->routeIs('kader.kegiatan.kehadiran') ? 'active' : '' }}"
                >
                    <span class="nav-icon">📋</span>
                    <span>Kehadiran</span>
                </a>

                {{-- 6. Jadwal Posyandu --}}
                <a
                    href="{{ route('kader.kegiatan.jadwal') }}"
                    class="nav-item {{ request()->routeIs('kader.kegiatan.jadwal') ? 'active' : '' }}"
                >
                    <span class="nav-icon">📅</span>
                    <span>Jadwal Posyandu</span>
                </a>

                {{-- 7. Laporan --}}
                <a
                    href="{{ route('kader.laporan') }}"
                    class="nav-item {{ request()->routeIs('kader.laporan') ? 'active' : '' }}"
                >
                    <span class="nav-icon">📄</span>
                    <span>Laporan</span>
                </a>

            @else
                {{-- ==================== ADMIN PUSKESMAS SIDEBAR ==================== --}}
                {{-- 1. Dashboard --}}
                <a
                    href="{{ route('dashboard') }}"
                    class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                >
                    <span class="nav-icon">🏠</span>
                    <span>Dashboard</span>
                </a>

                {{-- 2. Master Data (Dropdown Accordion) --}}
                @php
                    $isMasterDataActive = request()->routeIs('tapos.*') || request()->routeIs('balita.*') || request()->routeIs('ibu-hamil.*');
                @endphp
                <div class="nav-dropdown {{ $isMasterDataActive ? 'open active' : '' }}">
                    <button type="button" class="nav-dropdown-toggle {{ $isMasterDataActive ? 'active' : '' }}">
                        <span class="nav-icon">👥</span>
                        <span>Master Data</span>
                        <span class="dropdown-arrow">▾</span>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a
                            href="{{ route('tapos.index') }}"
                            class="dropdown-item {{ request()->routeIs('tapos.*') ? 'active' : '' }}"
                        >
                            <span class="dropdown-bullet">├─</span>
                            <span>Tapos</span>
                        </a>

                        <a
                            href="{{ route('balita.index') }}"
                            class="dropdown-item {{ request()->routeIs('balita.*') ? 'active' : '' }}"
                        >
                            <span class="dropdown-bullet">├─</span>
                            <span>Balita</span>
                        </a>

                        <a
                            href="{{ route('ibu-hamil.index') }}"
                            class="dropdown-item {{ request()->routeIs('ibu-hamil.*') ? 'active' : '' }}"
                        >
                            <span class="dropdown-bullet">└─</span>
                            <span>Ibu Hamil</span>
                        </a>
                    </div>
                </div>

                {{-- 3. Monitoring (Dropdown Accordion) --}}
                @php
                    $isAdminMonitoringActive = request()->routeIs('admin.monitoring*');
                @endphp
                <div class="nav-dropdown {{ $isAdminMonitoringActive ? 'open active' : '' }}">
                    <button type="button" class="nav-dropdown-toggle {{ $isAdminMonitoringActive ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        <span>Monitoring Wilayah</span>
                        <span class="nav-badge ai">AI Early Warning</span>
                        <span class="dropdown-arrow">▾</span>
                    </button>
                    <div class="nav-dropdown-menu">
                        <a
                            href="{{ route('admin.monitoring.balita') }}"
                            class="dropdown-item {{ request()->routeIs('admin.monitoring.balita') ? 'active' : '' }}"
                        >
                            <span class="dropdown-bullet">├─</span>
                            <span>Monitoring Balita</span>
                        </a>

                        <a
                            href="{{ route('admin.monitoring.ibu-hamil') }}"
                            class="dropdown-item {{ request()->routeIs('admin.monitoring.ibu-hamil') ? 'active' : '' }}"
                        >
                            <span class="dropdown-bullet">└─</span>
                            <span>Monitoring Ibu Hamil</span>
                        </a>
                    </div>
                </div>

                {{-- 4. Rekap Kehadiran --}}
                <a
                    href="{{ route('admin.kehadiran') }}"
                    class="nav-item {{ request()->routeIs('admin.kehadiran') ? 'active' : '' }}"
                >
                    <span class="nav-icon">📋</span>
                    <span>Rekap Kehadiran</span>
                </a>

                {{-- 5. Validasi Pemeriksaan --}}
                <a
                    href="{{ route('admin.validasi.index') }}"
                    class="nav-item {{ request()->routeIs('admin.validasi.*') ? 'active' : '' }}"
                >
                    <span class="nav-icon">🛡️</span>
                    <span>Validasi Pemeriksaan</span>
                    @php
                        $pendingCount = \App\Models\PemeriksaanBalita::where('status_validasi', 'pending')->count() + \App\Models\PemeriksaanIbuHamil::where('status_validasi', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="nav-badge count" style="background:#ef4444; color:#fff; border-radius:10px; padding:2px 7px; font-size:11px; font-weight:700;">{{ $pendingCount }}</span>
                    @endif
                </a>

                {{-- 6. Jadwal Posyandu --}}
                <a
                    href="{{ route('admin.jadwal.index') }}"
                    class="nav-item {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}"
                >
                    <span class="nav-icon">📅</span>
                    <span>Jadwal Posyandu</span>
                </a>

                {{-- 7. Laporan --}}
                <a
                    href="{{ route('admin.laporan') }}"
                    class="nav-item {{ request()->routeIs('admin.laporan') ? 'active' : '' }}"
                >
                    <span class="nav-icon">📄</span>
                    <span>Laporan</span>
                </a>
            @endif

        </nav>

        <div class="sidebar-bottom">

            @if(auth()->user()->role === 'kader')
                <a
                    href="{{ route('kader.profil') }}"
                    class="nav-item {{ request()->routeIs('kader.profil') ? 'active' : '' }}"
                >
                    <span class="nav-icon">⚙️</span>
                    <span>Profil</span>
                </a>
            @else
                <a
                    href="{{ route('admin.pengaturan') }}"
                    class="nav-item {{ request()->routeIs('admin.pengaturan') ? 'active' : '' }}"
                >
                    <span class="nav-icon">⚙️</span>
                    <span>Pengaturan</span>
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="logout-button">
                    <span class="nav-icon">🚪</span>
                    <span>Keluar</span>
                </button>
            </form>

        </div>


    </aside>

    {{-- OVERLAY MOBILE --}}
    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>

    {{-- MAIN --}}
    <main class="main-content">

        {{-- NAVBAR --}}
        <header class="topbar">

            <button
                class="mobile-menu"
                id="mobileMenu"
                type="button"
            >
                ☰
            </button>

            <div class="topbar-title">

                <span class="breadcrumb">
                    Posyandu Smart
                </span>

                <strong>
                    @yield('page-title', 'Dashboard')
                </strong>

            </div>

            <div class="topbar-actions">

                <button class="notification-button">
                    ♧

                    <span class="notification-dot"></span>
                </button>

                <div class="topbar-user">

                    <div class="topbar-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>

                    <div class="topbar-user-info">

                        <strong>
                            {{ auth()->user()->name ?? 'Pengguna' }}
                        </strong>

                        <span>
                            {{ auth()->user()->role === 'kader'
                                ? 'Kader'
                                : 'Admin Puskesmas'
                            }}
                        </span>

                    </div>

                </div>

            </div>

        </header>

        {{-- CONTENT --}}
        <section class="page-content">

            @yield('content')

        </section>

    </main>

</div>

<script src="{{ asset('js/dashboard.js') }}"></script>

@yield('scripts')

</body>
</html>