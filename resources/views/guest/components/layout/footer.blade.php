{{--
    Footer Component

    Usage:
    <x-guest::layout.footer />

    With custom content:
    <x-guest::layout.footer>
        <x-slot:description>Custom description</x-slot:description>
    </x-guest::layout.footer>
--}}

@props([
'address' => 'Jl. Batua Raya, Kec. Manggala, Kota Makassar',
'phone' => '(0411) 123-4567',
'email' => 'kelurahan.batuaraya@makassar.go.id',
'year' => date('Y'),
])

<footer class="bg-white border-t border-slate-200 py-12 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-12">
            {{-- Brand Section --}}
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <a href="{{ route('guest.welcome') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10  rounded-lg flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <h1 class="font-bold text-lg leading-tight text-primary">Kelurahan Batua Raya</h1>
                            <p class="text-xs text-slate-500">Pemerintahan Kota Makassar</p>
                        </div>
                    </a>
                </div>
                @isset($description)
                {{ $description }}
                @else
                <p class="text-slate-500 text-sm leading-relaxed">
                    Memberikan kemudahan akses informasi dan layanan publik bagi seluruh warga demi terwujudnya
                    masyarakat yang sejahtera.
                </p>
                @endisset
            </div>

            {{-- Contact Information --}}
            <div>
                <h5 class="font-bold mb-6">Hubungi Kami</h5>
                <ul class="space-y-4 text-sm text-slate-500">
                    <li class="flex items-start gap-3">
                        <span class="material-icons text-primary text-sm mt-0.5">place</span>
                        <span>{{ $address }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-icons text-primary text-sm">call</span>
                        <span>{{ $phone }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-icons text-primary text-sm">email</span>
                        <span>{{ $email }}</span>
                    </li>
                </ul>
            </div>

            {{-- Social Media --}}
            <div>
                <h5 class="font-bold mb-6">Media Sosial</h5>
                <div class="flex gap-4">
                    <a class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-primary hover:text-white transition-all"
                        href="#" aria-label="Facebook">
                        <span class="material-icons text-sm">facebook</span>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-primary hover:text-white transition-all"
                        href="#" aria-label="Website">
                        <span class="material-icons text-sm">language</span>
                    </a>
                    <a class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-primary hover:text-white transition-all"
                        href="#" aria-label="Instagram">
                        <span class="material-icons text-sm">camera_alt</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Copyright --}}
        <div class="border-t border-slate-100 mt-12 pt-8 text-center text-slate-400 text-xs">
            © {{ $year }} Kelurahan Batua Raya. Hak Cipta Dilindungi Undang-Undang.
        </div>
    </div>
</footer>
