{{--
    Navbar Component - Top Navigation Bar (Interactive)

    Usage:
    <x-guest::layout.navbar />
--}}

@php
$activeRoute = request()->route()?->getName() ?? '';

$navItems = [
    ['route' => 'guest.profil', 'label' => 'Profil'],
    ['route' => 'guest.data-kelurahan', 'label' => 'Data Kelurahan'],
    ['route' => 'guest.cek-data', 'label' => 'Cek Data'],
    ['route' => 'guest.surat-online', 'label' => 'Surat Online'],
    ['route' => 'guest.publikasi', 'label' => 'Publikasi'],
    ['route' => 'guest.parawisata', 'label' => 'Parawisata'],
    ['route' => 'guest.umkm', 'label' => 'UMKM'],
    ['route' => 'guest.pengaduan', 'label' => 'Pengaduan'],
    ['route' => 'guest.kontak', 'label' => 'Kontak'],
];
@endphp

<div class="sticky top-0 z-50 bg-white" x-data="{
    isScroll: false,
    mobileOpen: false,
    progress: 0,
    updateScroll() {
        this.isScroll = (window.pageYOffset > 10);
    },
    updateProgress() {
        const doc = document.documentElement;
        const scrollTop = doc.scrollTop || document.body.scrollTop;
        const scrollHeight = doc.scrollHeight || document.body.scrollHeight;
        const clientHeight = doc.clientHeight || window.innerHeight;
        const max = Math.max(1, scrollHeight - clientHeight);
        this.progress = Math.min(100, Math.max(0, (scrollTop / max) * 100));
    }
}" x-init="updateScroll(); updateProgress();"
   x-on:scroll.window="updateScroll(); updateProgress();"
   x-on:resize.window="updateProgress()">
    <nav class="bg-white"
        :class="isScroll ? 'shadow-md bg-white transition-all translate-y-1 duration-300 ease-in-out' : 'transition-all duration-300 ease-in-out'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                {{-- Logo Section --}}
                <a href="{{ route('guest.welcome') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="font-bold text-lg leading-tight text-primary">Kelurahan Batua Raya</h1>
                        <p class="text-xs text-slate-500">Pemerintahan Kota Makassar</p>
                    </div>
                </a>

                {{-- Desktop Navigation --}}
                <div class="hidden lg:flex space-x-6">
                    @foreach ($navItems as $item)
                        <x-guest::layout.nav-link
                            href="{{ route($item['route']) }}"
                            :active="$activeRoute === $item['route']">
                            {{ $item['label'] }}
                        </x-guest::layout.nav-link>
                    @endforeach
                </div>

                {{-- Mobile Menu Button --}}
                <div class="lg:hidden">
                    <button class="p-2 rounded-lg hover:bg-slate-100 transition-colors" type="button"
                        x-on:click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen" aria-label="Toggle menu">
                        <span class="material-icons" x-text="mobileOpen ? 'close' : 'menu'"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu Panel --}}
        <div class="lg:hidden border-t border-primary/10" x-cloak x-show="mobileOpen" x-transition>
            <div class="px-4 py-3 space-y-1 bg-white">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="block px-3 py-2 rounded-lg transition-colors {{ $activeRoute === $item['route'] ? 'bg-primary/10 text-primary font-semibold' : 'hover:bg-slate-100' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="h-1 bg-transparent" x-cloak x-show="progress > 0">
            <div x-cloak class="h-1 bg-primary transition-[width] duration-100 ease-linear" :style="`width: ${progress}%`"></div>
        </div>
    </nav>
</div>
