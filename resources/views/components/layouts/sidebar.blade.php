{{--
    Sidebar Component
    Responsive sidebar with collapsible menu groups matching SIM Kelurahan modules.
--}}

<aside class="fixed top-0 left-0 z-50 h-screen w-64 bg-base-100 shadow-lg transition-transform duration-300 overflow-y-auto"
    :class="{
        'translate-x-0': sidebarMobileOpen || sidebarOpen,
        '-translate-x-full lg:translate-x-0': !sidebarMobileOpen && sidebarOpen,
        '-translate-x-full': !sidebarOpen && !sidebarMobileOpen
    }">

    {{-- Brand header --}}
    <div class="flex items-center gap-3 px-4 py-5 border-b border-base-200">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-content" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>
        <div>
            <h2 class="font-bold text-base-content text-sm leading-tight">SIM Kelurahan</h2>
            <p class="text-xs text-base-content/60">Batua</p>
        </div>
        {{-- Close button for mobile --}}
        <button class="btn btn-ghost btn-sm btn-circle ml-auto lg:hidden" @click="sidebarMobileOpen = false">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Navigation menu --}}
    <ul class="menu menu-sm px-3 py-4 gap-1">

        {{-- Dashboard --}}
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Dashboard
            </a>
        </li>

        {{-- Data Master --}}
        <li>
            <details {{ request()->routeIs('master.*') ? 'open' : '' }}>
                <summary>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    </svg>
                    Data Master
                </summary>
                <ul>
                    <li><a href="#" class="{{ request()->routeIs('master.wilayah.*') ? 'active' : '' }}">Wilayah (RW/RT)</a></li>
                    <li><a href="#" class="{{ request()->routeIs('master.penandatangan.*') ? 'active' : '' }}">Penandatangan</a></li>
                    <li><a href="#" class="{{ request()->routeIs('master.jenis-surat.*') ? 'active' : '' }}">Jenis Surat</a></li>
                    <li><a href="#" class="{{ request()->routeIs('master.template-surat.*') ? 'active' : '' }}">Template Surat</a></li>
                    <li><a href="#" class="{{ request()->routeIs('master.referensi.*') ? 'active' : '' }}">Data Referensi</a></li>
                </ul>
            </details>
        </li>

        {{-- Kependudukan --}}
        <li>
            <details {{ request()->routeIs('kependudukan.*') ? 'open' : '' }}>
                <summary>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Kependudukan
                </summary>
                <ul>
                    <li><a href="#" class="{{ request()->routeIs('kependudukan.penduduk.*') ? 'active' : '' }}">Data Penduduk</a></li>
                    <li><a href="#" class="{{ request()->routeIs('kependudukan.kk.*') ? 'active' : '' }}">Kartu Keluarga</a></li>
                    <li><a href="#" class="{{ request()->routeIs('kependudukan.mutasi.*') ? 'active' : '' }}">Mutasi Penduduk</a></li>
                    <li><a href="#" class="{{ request()->routeIs('kependudukan.kelahiran.*') ? 'active' : '' }}">Kelahiran</a></li>
                    <li><a href="#" class="{{ request()->routeIs('kependudukan.kematian.*') ? 'active' : '' }}">Kematian</a></li>
                </ul>
            </details>
        </li>

        {{-- Persuratan --}}
        <li>
            <details {{ request()->routeIs('persuratan.*') ? 'open' : '' }}>
                <summary>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Persuratan
                </summary>
                <ul>
                    <li><a href="#" class="{{ request()->routeIs('persuratan.permohonan.*') ? 'active' : '' }}">Permohonan Surat</a></li>
                    <li><a href="#" class="{{ request()->routeIs('persuratan.verifikasi.*') ? 'active' : '' }}">Verifikasi</a></li>
                    <li><a href="#" class="{{ request()->routeIs('persuratan.tanda-tangan.*') ? 'active' : '' }}">Tanda Tangan</a></li>
                    <li><a href="#" class="{{ request()->routeIs('persuratan.arsip.*') ? 'active' : '' }}">Arsip Surat</a></li>
                    <li><a href="#" class="{{ request()->routeIs('persuratan.tracking.*') ? 'active' : '' }}">Tracking Layanan</a></li>
                </ul>
            </details>
        </li>

        {{-- Data Usaha / PK5 --}}
        <li>
            <details {{ request()->routeIs('usaha.*') ? 'open' : '' }}>
                <summary>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Data Usaha / PK5
                </summary>
                <ul>
                    <li><a href="#" class="{{ request()->routeIs('usaha.index') ? 'active' : '' }}">Daftar Usaha</a></li>
                    <li><a href="#" class="{{ request()->routeIs('usaha.jenis.*') ? 'active' : '' }}">Jenis Usaha</a></li>
                    <li><a href="#" class="{{ request()->routeIs('usaha.laporan') ? 'active' : '' }}">Laporan Usaha</a></li>
                </ul>
            </details>
        </li>

        {{-- Laporan --}}
        <li>
            <details {{ request()->routeIs('laporan.*') ? 'open' : '' }}>
                <summary>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Laporan
                </summary>
                <ul>
                    <li><a href="#" class="{{ request()->routeIs('laporan.kependudukan') ? 'active' : '' }}">Kependudukan</a></li>
                    <li><a href="#" class="{{ request()->routeIs('laporan.persuratan') ? 'active' : '' }}">Persuratan</a></li>
                    <li><a href="#" class="{{ request()->routeIs('laporan.usaha') ? 'active' : '' }}">Data Usaha</a></li>
                </ul>
            </details>
        </li>

        <div class="divider my-1 px-2"></div>

        {{-- Administrasi Sistem --}}
        <li>
            <details {{ request()->routeIs('admin.*') ? 'open' : '' }}>
                <summary>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan
                </summary>
                <ul>
                    <li><a href="#" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Pengguna</a></li>
                    <li><a href="#" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">Role & Hak Akses</a></li>
                    <li><a href="#" class="{{ request()->routeIs('admin.audit-log') ? 'active' : '' }}">Audit Log</a></li>
                </ul>
            </details>
        </li>
    </ul>
</aside>
