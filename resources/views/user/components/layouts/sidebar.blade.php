{{--
    Sidebar Component
    Responsive sidebar with role-based menu visibility.
    Uses auth()->user()->hasRole() to conditionally show menu groups.
--}}

@php
    $user = auth()->user();
@endphp

<aside
    class="fixed top-0 left-0 z-50 h-screen w-64 bg-base-100 shadow-lg transition-transform duration-300 overflow-y-auto flex flex-col"
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
            <h2 class="font-bold text-base-content text-sm leading-tight">SIM RW</h2>
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
    <ul class="menu menu-sm px-3 py-4 gap-1 w-full flex-1">

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
        {{-- Peta Kelurahan — Admin & RT/RW --}}
        {{-- ============================================================ --}}
        @if ($user && $user->hasRole(['admin', 'rt_rw']))
            <li class="menu-title mt-3">
                <span class="text-xs uppercase tracking-wider text-base-content/40">Peta</span>
            </li>
            <li>
                <a href="{{ route('peta.index') }}" class="{{ request()->routeIs('peta.index') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    Peta Kelurahan
                </a>
            </li>
            @if ($user->isAdmin())
                <li>
                    <a href="{{ route('admin.peta-layer.index') }}"
                        class="{{ request()->routeIs('admin.peta-layer.*') ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Kelola Peta
                    </a>
                </li>
            @endif
        @endif

        {{-- ============================================================ --}}
        {{-- Data Master — Admin only --}}
        {{-- ============================================================ --}}
        @if ($user && $user->hasRole('admin'))
            <li class="menu-title mt-3">
                <span class="text-xs uppercase tracking-wider text-base-content/40">Data Master</span>
            </li>
            <li>
                <details
                    {{ request()->routeIs('master.rw.*', 'master.rt.*', 'master.pengurus.*', 'master.profil-wilayah.*') ? 'open' : '' }}>
                    <summary>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        Kelola Wilayah
                    </summary>
                    <ul>
                        <li><a href="{{ route('master.rw.index') }}"
                                class="{{ request()->routeIs('master.rw.*') ? 'active' : '' }}">Data RW</a></li>
                        <li><a href="{{ route('master.rt.index') }}"
                                class="{{ request()->routeIs('master.rt.*') ? 'active' : '' }}">Data RT</a></li>
                        <li><a href="{{ route('master.pengurus.index') }}"
                                class="{{ request()->routeIs('master.pengurus.*') ? 'active' : '' }}">Pengurus
                                RT/RW</a></li>
                        @php $firstKelurahan = \App\Models\Kelurahan::first(); @endphp
                        @if ($firstKelurahan)
                            <li><a href="{{ route('master.profil-wilayah.kelurahan.show', $firstKelurahan) }}"
                                    class="{{ request()->routeIs('master.profil-wilayah.kelurahan.*') ? 'active' : '' }}">Profil
                                    Kelurahan</a></li>
                        @endif
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
                <details {{ request()->routeIs('kependudukan.*') ? 'open' : '' }}>
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
                        <li><a href="{{ route('data-umum.petugas-kebersihan.index') }}"
                                class="{{ request()->routeIs('data-umum.petugas-kebersihan.*') ? 'active' : '' }}">Petugas
                                Kebersihan</a></li>
                        <li><a href="{{ route('data-umum.kendaraan.index') }}"
                                class="{{ request()->routeIs('data-umum.kendaraan.*') ? 'active' : '' }}">Kendaraan</a>
                        </li>
                        <li><a href="{{ route('data-umum.kontrakan.index') }}"
                                class="{{ request()->routeIs('data-umum.kontrakan.*') ? 'active' : '' }}">Kontrakan &
                                Kost</a></li>
                        <li><a href="{{ route('data-umum.asrama.index') }}"
                                class="{{ request()->routeIs('data-umum.asrama.*') ? 'active' : '' }}">Asrama</a></li>
                        <li><a href="{{ route('data-umum.pbb.index') }}"
                                class="{{ request()->routeIs('data-umum.pbb.*') ? 'active' : '' }}">PBB</a></li>
                        <li><a href="{{ route('data-umum.retribusi-sampah.index') }}"
                                class="{{ request()->routeIs('data-umum.retribusi-sampah.*') ? 'active' : '' }}">Retribusi
                                Sampah</a></li>
                    </ul>
                </details>
            </li>
        @endif

        @if ($user && $user->hasRole('admin'))
            <li class="menu-title mt-3">
                <span class="text-xs uppercase tracking-wider text-base-content/40">Website Publik</span>
            </li>
            <li>
                <details {{ request()->routeIs('admin.website.*') ? 'open' : '' }}>
                    <summary>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                        </svg>

                        Website Publik
                    </summary>
                    <ul>
                        <li><a href="{{ route('admin.website.berita.index') }}"
                                class="{{ request()->routeIs('admin.website.berita.*') ? 'active' : '' }}">Berita</a>
                        </li>
                        <li><a href="{{ route('admin.website.dokumen-publik.index') }}"
                                class="{{ request()->routeIs('admin.website.dokumen-publik.*') ? 'active' : '' }}">Dokumen
                                Publik</a></li>
                        <li><a href="{{ route('admin.website.layanan-surat.index') }}"
                                class="{{ request()->routeIs('admin.website.layanan-surat.*') ? 'active' : '' }}">Layanan
                                Surat</a></li>
                        <li><a href="{{ route('admin.website.destinasi-wisata.index') }}"
                                class="{{ request()->routeIs('admin.website.destinasi-wisata.*') ? 'active' : '' }}">Destinasi
                                Wisata</a></li>
                        <li><a href="{{ route('admin.website.pengaduan-warga.index') }}"
                                class="{{ request()->routeIs('admin.website.pengaduan-warga.*') ? 'active' : '' }}">Pengaduan
                                Warga</a></li>
                    </ul>
                </details>
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

    <div class="px-3 pb-4 pt-2 border-t border-base-200 mt-auto">
        <ul class="menu menu-sm gap-1 w-full">
            <li>
                <a href="{{ route('about.index') }}"
                    class="{{ request()->routeIs('about.index') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tentang Aplikasi
                </a>
            </li>
        </ul>
    </div>
</aside>
