{{--
    Sidebar Component
    Responsive sidebar with role-based menu visibility.
    Uses auth()->user()->hasRole() to conditionally show menu groups.
--}}

@php
    $user = auth()->user();
@endphp

<aside
    class="fixed top-0 left-0 z-50 h-screen w-64 bg-base-100 shadow-lg transition-transform duration-300 overflow-y-auto"
    :class="{
        'translate-x-0': sidebarMobileOpen,
        '-translate-x-full lg:translate-x-0': !sidebarMobileOpen
    }">

    {{-- Brand header --}}
    <div class="flex items-center gap-3 px-4 py-5 border-b border-base-200">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-content" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <div>
            <h2 class="font-bold text-base-content text-sm leading-tight">SIM Kelurahan</h2>
            <p class="text-xs text-base-content/60">Batua</p>
        </div>
        {{-- Close button for mobile --}}
        <button class="btn btn-ghost btn-sm btn-circle ml-auto lg:hidden" @click="sidebarMobileOpen = false">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- User role badge --}}
    @if ($user)
        <div class="px-4 py-3 border-b border-base-200">
            <div class="flex items-center gap-2">
                <x-ui.avatar :name="$user->name" size="sm" />
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ $user->name }}</p>
                    <p class="text-xs text-base-content/60">
                        {{ \App\Models\Role::roleLabels()[$user->getRoleName()] ?? 'User' }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Navigation menu --}}
    <ul class="menu menu-sm px-3 py-4 gap-1 w-full">

        {{-- Dashboard — Semua role --}}
        <li>
            <a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard*', 'admin.dashboard', 'rtrw.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Dashboard
            </a>
        </li>

        {{-- ============================================================ --}}
        {{-- Data Master — Admin only --}}
        {{-- ============================================================ --}}
        @if ($user && $user->hasRole('admin'))
            <li class="menu-title mt-3">
                <span class="text-xs uppercase tracking-wider text-base-content/40">Data Master</span>
            </li>
            <li>
                <details {{ request()->routeIs('master.*') ? 'open' : '' }}>
                    <summary>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                        Data Master
                    </summary>
                    <ul>
                        <li><a href="{{ route('master.wilayah.index') }}"
                                class="{{ request()->routeIs('master.wilayah.*', 'master.wilayah.*') ? 'active' : '' }}">Wilayah
                                (RW/RT)</a></li>
                        <li><a href="{{ route('master.penandatangan.index') }}"
                                class="{{ request()->routeIs('master.penandatangan.*', 'master.penandatangan.*') ? 'active' : '' }}">Penandatangan</a>
                        </li>
                        <li><a href="{{ route('master.pegawai.index') }}"
                                class="{{ request()->routeIs('master.pegawai.*') ? 'active' : '' }}">Pegawai /
                                Staff</a>
                        </li>
                        <li><a href="{{ route('master.referensi.index') }}"
                                class="{{ request()->routeIs('master.referensi.*') ? 'active' : '' }}">Data
                                Referensi</a>
                        </li>
                    </ul>
                </details>
            </li>
        @endif

        {{-- ============================================================ --}}
        {{-- Kependudukan — Admin, RT/RW --}}
        {{-- ============================================================ --}}
        @if ($user && $user->hasRole(['admin', 'rt_rw']))
            <li class="menu-title mt-3">
                <span class="text-xs uppercase tracking-wider text-base-content/40">Kependudukan</span>
            </li>
            <li>
                <details
                    {{ request()->routeIs('kependudukan.*', 'kependudukan.penduduk.*', 'admin.keluarga.*') ? 'open' : '' }}>
                    <summary>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Kependudukan
                    </summary>
                    <ul>
                        <li><a href="{{ route('kependudukan.penduduk.index') }}"
                                class="{{ request()->routeIs('kependudukan.penduduk.*', 'kependudukan.penduduk.*') ? 'active' : '' }}">Data
                                Penduduk</a></li>
                        <li><a href="{{ route('kependudukan.keluarga.index') }}"
                                class="{{ request()->routeIs('kependudukan.keluarga.*', 'kependudukan.kk.*') ? 'active' : '' }}">Kartu
                                Keluarga</a></li>
                        <li><a href="{{ route('kependudukan.mutasi.index') }}"
                                class="{{ request()->routeIs('kependudukan.mutasi.*') ? 'active' : '' }}">Mutasi
                                Penduduk</a></li>
                        <li><a href="{{ route('kependudukan.kelahiran.index') }}"
                                class="{{ request()->routeIs('kependudukan.kelahiran.*') ? 'active' : '' }}">Kelahiran</a>
                        </li>
                        <li><a href="{{ route('kependudukan.kematian.index') }}"
                                class="{{ request()->routeIs('kependudukan.kematian.*') ? 'active' : '' }}">Kematian</a>
                        </li>
                    </ul>
                </details>
            </li>
        @endif

        {{-- ============================================================ --}}
        {{-- Data Usaha / PK5 — Admin, RT/RW --}}
        {{-- ============================================================ --}}
        @if ($user && $user->hasRole(['admin', 'rt_rw']))
            <li class="menu-title mt-3">
                <span class="text-xs uppercase tracking-wider text-base-content/40">Data Usaha</span>
            </li>
            <li>
                <details {{ request()->routeIs('usaha.*') ? 'open' : '' }}>
                    <summary>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Data Usaha / PK5
                    </summary>
                    <ul>
                        <li><a href="{{ route('usaha.index') }}"
                                class="{{ request()->routeIs('usaha.index') ? 'active' : '' }}">Daftar Usaha</a></li>
                        <li><a href="{{ route('usaha.jenis.index') }}"
                                class="{{ request()->routeIs('usaha.jenis.*') ? 'active' : '' }}">Jenis Usaha</a></li>
                        <li><a href="{{ route('usaha.laporan') }}"
                                class="{{ request()->routeIs('usaha.laporan') ? 'active' : '' }}">Laporan Usaha</a>
                        </li>
                    </ul>
                </details>
            </li>
        @endif

        {{-- ============================================================ --}}
        {{-- Data Umum — Admin, RT/RW --}}
        {{-- ============================================================ --}}
        @if ($user && $user->hasRole(['admin', 'rt_rw']))
            <li class="menu-title mt-3">
                <span class="text-xs uppercase tracking-wider text-base-content/40">Data Umum</span>
            </li>
            <li>
                <details {{ request()->routeIs('data-umum.*') ? 'open' : '' }}>
                    <summary>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9" />
                        </svg>
                        Data Umum
                    </summary>
                    <ul>
                        <li><a href="{{ route('data-umum.faskes.index') }}"
                                class="{{ request()->routeIs('data-umum.faskes.*') ? 'active' : '' }}">Fasilitas
                                Kesehatan</a></li>
                        <li><a href="{{ route('data-umum.sekolah.index') }}"
                                class="{{ request()->routeIs('data-umum.sekolah.*') ? 'active' : '' }}">Sekolah</a>
                        </li>
                        <li><a href="{{ route('data-umum.tempat-ibadah.index') }}"
                                class="{{ request()->routeIs('data-umum.tempat-ibadah.*') ? 'active' : '' }}">Tempat
                                Ibadah</a></li>
                    </ul>
                </details>
            </li>
        @endif

        {{-- ============================================================ --}}
        {{-- Agenda & Kegiatan — Admin only --}}
        {{-- ============================================================ --}}
        @if ($user && $user->hasRole('admin'))
            <li class="menu-title mt-3">
                <span class="text-xs uppercase tracking-wider text-base-content/40">Agenda & Kegiatan</span>
            </li>
            <li>
                <a href="{{ route('agenda.index') }}" class="{{ request()->routeIs('agenda.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Agenda & Kegiatan
                </a>
            </li>
        @endif

        {{-- ============================================================ --}}
        {{-- Laporan — Admin only --}}
        {{-- ============================================================ --}}
        @if ($user && $user->hasRole('admin'))
            <li class="menu-title mt-3">
                <span class="text-xs uppercase tracking-wider text-base-content/40">Laporan</span>
            </li>
            <li>
                <details {{ request()->routeIs('laporan.*') ? 'open' : '' }}>
                    <summary>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Laporan
                    </summary>
                    <ul>
                        <li><a href="{{ route('laporan.kependudukan') }}"
                                class="{{ request()->routeIs('laporan.kependudukan') ? 'active' : '' }}">Kependudukan</a>
                        </li>

                        <li><a href="{{ route('laporan.usaha') }}"
                                class="{{ request()->routeIs('laporan.usaha') ? 'active' : '' }}">Data Usaha</a>
                        </li>
                    </ul>
                </details>
            </li>
        @endif

        {{-- ============================================================ --}}
        {{-- RT/RW: menu khusus pengantar & data warga --}}
        {{-- ============================================================ --}}
        @if ($user && $user->hasRole('rt_rw'))
            <li class="menu-title mt-3">
                <span class="text-xs uppercase tracking-wider text-base-content/40">Layanan RT/RW</span>
            </li>
            <li>
                <a href="{{ route('rtrw.laporan.index') }}"
                    class="{{ request()->routeIs('rtrw.laporan.*') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Laporan Wilayah
                </a>
            </li>
        @endif

    </ul>
</aside>
