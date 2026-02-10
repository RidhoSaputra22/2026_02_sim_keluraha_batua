{{--
    Navbar Component
    Top navigation bar with hamburger, title, search, notifications, user dropdown
--}}

@props(['title' => 'Dashboard'])

<header class="navbar bg-base-100 shadow-sm sticky top-0 z-30 px-4 lg:px-6">
    <div class="flex-none gap-2">
        {{-- Sidebar toggle (desktop) --}}
        <button class="btn btn-ghost btn-sm btn-square hidden lg:flex" @click="sidebarOpen = !sidebarOpen">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- Sidebar toggle (mobile) --}}
        <button class="btn btn-ghost btn-sm btn-square lg:hidden" @click="sidebarMobileOpen = true">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- Page title --}}
        <h1 class="text-lg font-semibold text-base-content hidden sm:block">{{ $title }}</h1>
    </div>

    <div class="flex-1"></div>

    <div class="flex-none flex items-center gap-2">
        {{-- Search --}}
        <div class="form-control hidden md:block">
            <div class="relative">
                <input type="text" placeholder="Cari..." class="input input-sm input-bordered w-48 lg:w-64 pr-8" />
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 absolute right-2.5 top-2.5 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        {{-- Theme toggle --}}
        <label class="btn btn-ghost btn-sm btn-circle swap swap-rotate">
            <input type="checkbox" class="theme-controller" value="dark" />
            <svg class="swap-off fill-current w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z" />
            </svg>
            <svg class="swap-on fill-current w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13ZM18,22A10.07,10.07,0,0,1,2,12,10,10,0,0,1,9.35,2.17a6.17,6.17,0,0,0,4.48,11.67A6.17,6.17,0,0,0,18,22Z" />
            </svg>
        </label>

        {{-- Notifications --}}
        <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-ghost btn-sm btn-circle indicator">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="badge badge-xs badge-primary indicator-item"></span>
            </label>
            <div tabindex="0" class="dropdown-content menu bg-base-100 rounded-box w-72 shadow-lg mt-2 p-0">
                <div class="p-3 border-b border-base-200">
                    <h3 class="font-semibold text-sm">Notifikasi</h3>
                </div>
                <div class="p-2 max-h-60 overflow-y-auto">
                    <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-base-200 cursor-pointer">
                        <div class="w-2 h-2 bg-primary rounded-full mt-2 shrink-0"></div>
                        <div>
                            <p class="text-sm">Surat menunggu verifikasi</p>
                            <p class="text-xs text-base-content/60">2 menit yang lalu</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-base-200 cursor-pointer">
                        <div class="w-2 h-2 bg-warning rounded-full mt-2 shrink-0"></div>
                        <div>
                            <p class="text-sm">Data penduduk perlu dilengkapi</p>
                            <p class="text-xs text-base-content/60">1 jam yang lalu</p>
                        </div>
                    </div>
                </div>
                <div class="p-2 border-t border-base-200">
                    <a href="#" class="btn btn-ghost btn-sm btn-block text-primary">Lihat Semua</a>
                </div>
            </div>
        </div>

        {{-- User dropdown --}}
        <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-ghost btn-sm gap-2">
                <x-ui.avatar :name="auth()->user()->name ?? 'Admin'" size="sm" />
                <span class="hidden md:inline text-sm font-medium">{{ auth()->user()->name ?? 'Admin' }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </label>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box w-52 shadow-lg mt-2 p-2">
                <li class="menu-title">
                    <span class="text-xs text-base-content/60">{{ auth()->user()->email ?? 'admin@kelurahan.go.id' }}</span>
                </li>
                <li>
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profil Saya
                    </a>
                </li>
                <li>
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Pengaturan
                    </a>
                </li>
                <div class="divider my-1"></div>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center gap-2 text-error">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
