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
    @if($user)
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
                class="{{ request()->routeIs('dashboard*', 'admin.dashboard', 'operator.dashboard', 'verifikator.dashboard', 'penandatangan.dashboard', 'rtrw.dashboard', 'warga.dashboard') ? 'active' : '' }}">
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
        @if($user && $user->hasRole('admin'))
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
                            class="{{ request()->routeIs('master.pegawai.*') ? 'active' : '' }}">Pegawai / Staff</a>
                    </li>
                    <!-- <li><a href="{{ route('master.jenis-surat.index') }}"
                            class="{{ request()->routeIs('master.jenis-surat.*') ? 'active' : '' }}">Jenis Surat</a>
                    </li> -->
                    <!-- <li><a href="{{ route('master.template-surat.index') }}"
                            class="{{ request()->routeIs('master.template-surat.*') ? 'active' : '' }}">Template
                            Surat</a></li> -->
                    <li><a href="{{ route('master.referensi.index') }}"
                            class="{{ request()->routeIs('master.referensi.*') ? 'active' : '' }}">Data Referensi</a>
                    </li>
                </ul>
            </details>
        </li>
        @endif

        {{-- ============================================================ --}}
        {{-- Kependudukan — Admin, Operator --}}
        {{-- ============================================================ --}}
        @if($user && $user->hasRole(['admin', 'operator', 'rt_rw']))

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
        {{-- Persuratan — Admin, Operator, Verifikator, Penandatangan (warga has separate menu) --}}
        {{-- ============================================================ --}}
        <!-- @if($user && $user->hasRole(['admin', 'operator', 'verifikator', 'penandatangan']))
        <li class="menu-title mt-3">
            <span class="text-xs uppercase tracking-wider text-base-content/40">Persuratan</span>
        </li>
        <li>
            <details {{ request()->routeIs('persuratan.*') ? 'open' : '' }}>
                <summary>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Persuratan
                </summary>
                <ul>
                    {{-- Permohonan: Admin, Operator only (warga use warga.permohonan.* routes) --}}
                    @if($user->hasRole(['admin', 'operator']))
                    <li><a href="{{ route('persuratan.permohonan.index') }}"
                            class="{{ request()->routeIs('persuratan.permohonan.*') ? 'active' : '' }}">Permohonan
                            Surat</a></li>
                    @endif

                    {{-- Verifikasi: Admin, Verifikator --}}
                    @if($user->hasRole(['admin', 'verifikator']))
                    <li><a href="{{ route('persuratan.verifikasi.index') }}"
                            class="{{ request()->routeIs('persuratan.verifikasi.*') ? 'active' : '' }}">Verifikasi</a>
                    </li>
                    @endif

                    {{-- Tanda Tangan: Admin, Penandatangan --}}
                    @if($user->hasRole(['admin', 'penandatangan']))
                    <li><a href="{{ route('persuratan.tanda-tangan.index') }}"
                            class="{{ request()->routeIs('persuratan.tanda-tangan.*') ? 'active' : '' }}">Tanda
                            Tangan</a></li>
                    @endif

                    {{-- Arsip: Admin, Operator --}}
                    @if($user->hasRole(['admin', 'operator']))
                    <li><a href="{{ route('persuratan.arsip.index') }}"
                            class="{{ request()->routeIs('persuratan.arsip.*') ? 'active' : '' }}">Arsip Surat</a></li>
                    @endif

                    {{-- Tracking: Semua role persuratan --}}
                    <li><a href="{{ route('persuratan.tracking.index') }}"
                            class="{{ request()->routeIs('persuratan.tracking.*') ? 'active' : '' }}">Tracking
                            Layanan</a></li>
                </ul>
            </details>
        </li>
        @endif -->

        {{-- ============================================================ --}}
        {{-- Ekspedisi — Admin, Operator --}}
        {{-- ============================================================ --}}
        <!-- @if($user && $user->hasRole(['admin', 'operator']))
        <li class="menu-title mt-3">
            <span class="text-xs uppercase tracking-wider text-base-content/40">Ekspedisi</span>
        </li>
        <li>
            <a href="{{ route('ekspedisi.index') }}" class="{{ request()->routeIs('ekspedisi.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                Ekspedisi Surat
            </a>
        </li>
        @endif -->

        {{-- ============================================================ --}}
        {{-- Data Usaha / PK5 — Admin, Operator --}}
        {{-- ============================================================ --}}
        @if($user && $user->hasRole(['admin', 'operator', 'rt_rw']))
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
                            class="{{ request()->routeIs('usaha.laporan') ? 'active' : '' }}">Laporan Usaha</a></li>
                </ul>
            </details>
        </li>
        @endif

        {{-- ============================================================ --}}
        {{-- Data Umum — Admin, Operator --}}
        {{-- ============================================================ --}}
        @if($user && $user->hasRole(['admin', 'operator', 'rt_rw']))
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
                            class="{{ request()->routeIs('data-umum.sekolah.*') ? 'active' : '' }}">Sekolah</a></li>
                    <li><a href="{{ route('data-umum.tempat-ibadah.index') }}"
                            class="{{ request()->routeIs('data-umum.tempat-ibadah.*') ? 'active' : '' }}">Tempat
                            Ibadah</a></li>
                    <!-- <li><a href="{{ route('data-umum.petugas-kebersihan.index') }}"
                            class="{{ request()->routeIs('data-umum.petugas-kebersihan.*') ? 'active' : '' }}">Petugas
                            Kebersihan</a></li>
                    <li><a href="{{ route('data-umum.kendaraan.index') }}"
                            class="{{ request()->routeIs('data-umum.kendaraan.*') ? 'active' : '' }}">Kendaraan</a></li> -->
                </ul>
            </details>
        </li>
        @endif

        {{-- ============================================================ --}}
        {{-- Agenda & Kegiatan — Admin, Operator --}}
        {{-- ============================================================ --}}
        @if($user && $user->hasRole(['admin', 'operator']))
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
        {{-- Laporan — Admin, Operator, Verifikator, Penandatangan --}}
        {{-- ============================================================ --}}
        @if($user && $user->hasRole(['admin', 'operator', 'verifikator', 'penandatangan']))
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
                    {{-- Laporan Kependudukan: Admin, Operator only --}}
                    @if($user->hasRole(['admin', 'operator']))
                    <li><a href="{{ route('laporan.kependudukan') }}"
                            class="{{ request()->routeIs('laporan.kependudukan') ? 'active' : '' }}">Kependudukan</a>
                    </li>
                    @endif

                    {{-- Laporan Persuratan: All roles that handle surat workflow --}}
                    <!-- <li><a href="{{ route('laporan.persuratan') }}"
                            class="{{ request()->routeIs('laporan.persuratan') ? 'active' : '' }}">Persuratan</a></li> -->

                    {{-- Laporan Usaha: Admin, Operator only --}}
                    @if($user->hasRole(['admin', 'operator']))
                    <li><a href="{{ route('laporan.usaha') }}"
                            class="{{ request()->routeIs('laporan.usaha') ? 'active' : '' }}">Data Usaha</a></li>
                    @endif
                </ul>
            </details>
        </li>
        @endif

        {{-- ============================================================ --}}
        {{-- RT/RW: menu khusus pengantar & data warga --}}
        {{-- ============================================================ --}}
        @if($user && $user->hasRole('rt_rw'))
        <li class="menu-title mt-3">
            <span class="text-xs uppercase tracking-wider text-base-content/40">Layanan RT/RW</span>
        </li>
        <!-- <li>
            <a href="{{ route('rtrw.warga.index') }}" class="{{ request()->routeIs('rtrw.warga.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Data Warga
            </a>
        </li>
        <li>
            <a href="{{ route('rtrw.keluarga.index') }}"
                class="{{ request()->routeIs('rtrw.keluarga.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Data Keluarga
            </a>
        </li> -->
        <!-- <li>
            <a href="{{ route('rtrw.pengantar.index') }}"
                class="{{ request()->routeIs('rtrw.pengantar.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Surat Pengantar
            </a>
        </li> -->
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

        {{-- ============================================================ --}}
        {{-- Warga: menu khusus layanan mandiri --}}
        {{-- ============================================================ --}}
        @if($user && $user->hasRole('warga'))
        <li class="menu-title mt-3">
            <span class="text-xs uppercase tracking-wider text-base-content/40">Layanan Saya</span>
        </li>
        <li>
            <a href="{{ route('warga.permohonan.index') }}"
                class="{{ request()->routeIs('warga.permohonan.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Ajukan Permohonan
            </a>
        </li>
        <li>
            <a href="{{ route('warga.riwayat.index') }}"
                class="{{ request()->routeIs('warga.riwayat.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Riwayat & Tracking
            </a>
        </li>
        @endif

        {{-- ============================================================ --}}
        {{-- Survey Kepuasan — All roles (optional) --}}
        {{-- ============================================================ --}}
        <!-- @if($user)
        <div class="divider my-1 px-2"></div>
        <li>
            <a href="{{ route('survey.index') }}" class="{{ request()->routeIs('survey.*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Survey Kepuasan
            </a>
        </li>
        @endif -->

        {{-- ============================================================ --}}
        {{-- Administrasi Sistem — Admin only --}}
        {{-- ============================================================ --}}
        <!-- @if($user && $user->hasRole('admin'))
        <li class="menu-title mt-3">
            <span class="text-xs uppercase tracking-wider text-base-content/40">Administrasi</span>
        </li>
        <li>
            <details {{ request()->routeIs('admin.*') ? 'open' : '' }}>
                <summary>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan
                </summary>
                <ul>
                    <li><a href="{{ route('admin.users.index') }}"
                            class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Pengguna</a></li>
                    <li><a href="{{ route('admin.roles.index') }}"
                            class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">Role & Hak Akses</a></li>
                    <li><a href="{{ route('admin.audit-log') }}"
                            class="{{ request()->routeIs('admin.audit-log') ? 'active' : '' }}">Audit Log</a></li>
                </ul>
            </details>
        </li>
        @endif -->
    </ul>
</aside>
