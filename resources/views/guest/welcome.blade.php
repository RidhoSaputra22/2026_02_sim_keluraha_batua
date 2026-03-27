<x-guest::layout.app title="Beranda">

    {{-- HERO SECTION --}}
    <section class="relative h-[600px] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0">
            <img alt="Kantor Kelurahan Modern" class="w-full h-full object-cover"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBUC9Hwtv_EXSKYSAlBT4bHA7FQMlbje-rVBN6IoULW4hXdJtYK4wRVoGDkxXvj0EqHiuMEryN7T73OHY51E6Ohpos84Sb6PKodwGkqDigsKsRIJlb3jpiNKh3EWK7MANm5wN-dRDQuhwip0xvSAN7HX8i5QC34rVmFRc0QLlk0ct07U4oKQvd2TwctczmS9aRP1aMYOecH0BF9BXwZ8esnksvNjv1cE5-y3HjaDCJjvoej0k93Ezz1Wa4wivGeFgdwrWdIB-S6bhp-" />
            <div class="absolute inset-0 bg-gradient-to-r from-primary/90 to-primary/40"></div>
        </div>
        <div class="relative z-10 max-w-7xl px-4 text-center text-white">
            <h1 class="text-5xl font-extrabold mb-6 leading-tight">
                Selamat Datang di Website Resmi <br />
                <span class="text-yellow-300">Kelurahan Batua Raya</span>
            </h1>
            <p class="text-xl mb-10 opacity-90 font-light max-w-2xl mx-auto">
                Akses informasi publik, layanan administrasi, dan kabar terkini lingkungan Kelurahan secara cepat,
                transparan, dan akuntabel.
            </p>

            <x-guest::ui.search-bar action="{{ route('guest.search') }}"
                placeholder="Cari layanan, berita, dokumen, UMKM, atau wisata..." button-text="CARI">
                <div class="mt-4 flex flex-wrap items-center     justify-center gap-2 text-sm">
                    <span class="opacity-80">Populer:</span>
                    <a class="bg-white/20 hover:bg-white/30 backdrop-blur-md px-3 py-1 rounded-full"
                        href="{{ route('guest.search', ['q' => 'cek ktp']) }}">Cek KTP</a>
                    <a class="bg-white/20 hover:bg-white/30 backdrop-blur-md px-3 py-1 rounded-full"
                        href="{{ route('guest.search', ['q' => 'izin usaha']) }}">Izin Usaha</a>
                    <a class="bg-white/20 hover:bg-white/30 backdrop-blur-md px-3 py-1 rounded-full"
                        href="{{ route('guest.search', ['q' => 'administrasi kependudukan']) }}">Administrasi</a>
                </div>
            </x-guest::ui.search-bar>
        </div>
    </section>

    {{-- LAYANAN UNGGULAN --}}
    <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-guest::ui.section-header title="Layanan Unggulan"
            subtitle="Pilih jenis layanan masyarakat yang Anda butuhkan di bawah ini." size="md" />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <a href="{{ route('guest.surat-online') }}" class="block">
                <x-guest::ui.card variant="bordered" padding="lg"
                    class="group h-full hover:shadow-xl hover:-translate-y-2 transition-all cursor-pointer">
                    <div
                        class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                        <x-guest::ui.icon name="badge" size="lg" color="text-primary group-hover:text-white" />
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900">Adm. Kependudukan</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6">
                        Pengurusan KTP, KK, akta, domisili, surat usaha, SKTM, dan surat keterangan warga lainnya.
                    </p>
                    <div class="flex items-center text-primary font-bold text-sm">
                        LIHAT DETAIL
                        <x-guest::ui.icon name="arrow_forward" size="sm" class="ml-2" />
                    </div>
                </x-guest::ui.card>
            </a>

            <a href="{{ route('guest.umkm') }}" class="block">
                <x-guest::ui.card variant="bordered" padding="lg"
                    class="group h-full hover:shadow-xl hover:-translate-y-2 transition-all cursor-pointer">
                    <div
                        class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                        <x-guest::ui.icon name="store" size="lg" color="text-primary group-hover:text-white" />
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900">UMKM & Usaha Warga</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6">
                        Direktori usaha lokal, sektor UMKM, dan kontak pemilik usaha yang terdata di kelurahan.
                    </p>
                    <div class="flex items-center text-primary font-bold text-sm">
                        LIHAT DETAIL
                        <x-guest::ui.icon name="arrow_forward" size="sm" class="ml-2" />
                    </div>
                </x-guest::ui.card>
            </a>

            <a href="{{ route('guest.pengaduan') }}" class="block">
                <x-guest::ui.card variant="bordered" padding="lg"
                    class="group h-full hover:shadow-xl hover:-translate-y-2 transition-all cursor-pointer">
                    <div
                        class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                        <x-guest::ui.icon name="report_problem" size="lg"
                            color="text-primary group-hover:text-white" />
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900">Pengaduan</h3>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6">Sampaikan keluhan dan laporan warga terkait
                        fasilitas umum atau keamanan.</p>
                    <div class="flex items-center text-primary font-bold text-sm">
                        LIHAT DETAIL
                        <x-guest::ui.icon name="arrow_forward" size="sm" class="ml-2" />
                    </div>
                </x-guest::ui.card>
            </a>
        </div>
    </section>

    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900">Berita & Informasi Terkini</h2>
                    <p class="text-slate-500 mt-2">Konten ini ditarik langsung dari modul berita dan publikasi yang
                        dikelola admin.</p>
                </div>
                <a href="{{ route('guest.publikasi') }}" class="text-primary font-semibold hover:underline">Lihat semua
                    publikasi</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @forelse ($beritaTerkini as $item)
                    <x-guest::ui.card padding="none" variant="bordered" class="overflow-hidden h-full">
                        <div class="aspect-[16/10] bg-slate-100 overflow-hidden">
                            @if ($item->gambar)
                                <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->judul }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-primary/5">
                                    <x-guest::ui.icon name="article" size="xl" color="text-primary" />
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <x-guest::ui.badge variant="primary" size="sm" class="mb-3">
                                {{ \App\Models\Berita::kategoriOptions()[$item->kategori] ?? ucfirst($item->kategori) }}
                            </x-guest::ui.badge>
                            <div class="text-sm text-slate-400 mb-3">
                                {{ $item->published_at?->translatedFormat('d F Y') }}</div>
                            <h3 class="text-xl font-bold text-slate-900 leading-snug mb-3">{{ $item->judul }}</h3>
                            <p class="text-sm text-slate-500 leading-6 mb-5">
                                {{ $item->ringkasan ?: \Illuminate\Support\Str::limit(strip_tags($item->isi), 140) }}
                            </p>
                            <x-guest::ui.button href="{{ route('guest.berita.show', $item) }}" variant="ghost"
                                class="!px-0">
                                Baca selengkapnya
                            </x-guest::ui.button>
                        </div>
                    </x-guest::ui.card>
                @empty
                    <div class="lg:col-span-3">
                        <x-guest::ui.card variant="bordered" padding="lg" class="text-center">
                            <x-guest::ui.icon name="newspaper" size="xl" color="text-slate-300"
                                class="mb-4" />
                            <p class="text-lg font-semibold text-slate-900">Belum ada berita yang dipublikasikan.</p>
                        </x-guest::ui.card>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <x-guest::ui.section-header title="Rekomendasi Kelurahan"
                    subtitle="Sorotan cepat untuk destinasi lokal dan dokumen yang sering dibutuhkan warga."
                    size="md" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse ($destinasiUnggulan as $destinasi)
                        <x-guest::ui.card variant="bordered" padding="none" class="overflow-hidden h-full">
                            <div class="aspect-[16/10] bg-slate-100">
                                @if ($destinasi->gambar)
                                    <img src="{{ asset('storage/' . $destinasi->gambar) }}"
                                        alt="{{ $destinasi->nama }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-primary/5">
                                        <x-guest::ui.icon name="place" size="xl" color="text-primary" />
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <x-guest::ui.badge variant="success" size="sm" class="mb-3">
                                    {{ \App\Models\DestinasiWisata::kategoriOptions()[$destinasi->kategori] ?? ucfirst($destinasi->kategori) }}
                                </x-guest::ui.badge>
                                <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $destinasi->nama }}</h3>
                                <p class="text-sm text-slate-500 leading-6">{{ $destinasi->ringkasan }}</p>
                                <div class="mt-5">
                                    <x-guest::ui.button
                                        href="{{ route('guest.parawisata', ['q' => $destinasi->nama]) }}"
                                        variant="outline">
                                        Lihat di Halaman Wisata
                                    </x-guest::ui.button>
                                </div>
                            </div>
                        </x-guest::ui.card>
                    @empty
                        <x-guest::ui.card variant="bordered" padding="lg" class="md:col-span-2">
                            <p class="text-slate-500">Belum ada destinasi unggulan yang dipublikasikan.</p>
                        </x-guest::ui.card>
                    @endforelse
                </div>
            </div>

            <div>
                <x-guest::ui.card variant="bordered" padding="lg" class="h-full">
                    <div class="flex items-center justify-between gap-3 mb-6">
                        <h3 class="text-xl font-bold text-slate-900">Dokumen Terbaru</h3>
                        <a href="{{ route('guest.publikasi') }}"
                            class="text-sm text-primary font-semibold hover:underline">Lihat semua</a>
                    </div>

                    <div class="space-y-4">
                        @forelse ($dokumenPublik as $dokumen)
                            <div class="rounded-2xl border border-slate-100 p-4">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                                        <x-guest::ui.icon name="description" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-slate-900">{{ $dokumen->judul }}</p>
                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ \App\Models\DokumenPublik::kategoriOptions()[$dokumen->kategori] ?? ucfirst($dokumen->kategori) }}
                                            •
                                            {{ $dokumen->published_at?->translatedFormat('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <x-guest::ui.button href="{{ route('guest.publikasi.download', $dokumen) }}"
                                        variant="outline" class="w-full">
                                        Unduh Dokumen
                                    </x-guest::ui.button>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada dokumen yang dipublikasikan.</p>
                        @endforelse
                    </div>
                </x-guest::ui.card>
            </div>
        </div>
    </section>

    <section class="pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="rounded-[2rem] overflow-hidden bg-primary">
            <div class="grid grid-cols-1 lg:grid-cols-3">
                <div class="lg:col-span-1 bg-primary/90 min-h-[320px]">
                    @if ($kelurahan?->foto)
                        <img src="{{ asset('storage/' . $kelurahan->foto) }}" alt="{{ $kelurahan->nama }}"
                            class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-white/30">
                            <x-guest::ui.icon name="account_balance" size="2xl" />
                        </div>
                    @endif
                </div>
                <div class="lg:col-span-2 p-8 md:p-12 text-white">
                    <h2 class="text-3xl md:text-4xl font-extrabold">Sambutan Lurah</h2>
                    <p class="mt-6 text-lg leading-8 text-white/90">
                        "{{ $kelurahan?->visi ?: 'Mewujudkan pelayanan publik kelurahan yang cepat, terbuka, dan dekat dengan kebutuhan warga.' }}"
                    </p>
                    <div class="mt-8">
                        <div class="text-2xl font-bold">{{ $kelurahan?->nama_lurah ?: 'Lurah Kelurahan' }}</div>
                        <div class="text-sm uppercase tracking-[0.2em] text-yellow-300 mt-1">
                            {{ $kelurahan?->nip_lurah ? 'NIP. ' . $kelurahan->nip_lurah : 'Pimpinan Kelurahan' }}
                        </div>
                    </div>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <x-guest::ui.button href="{{ route('guest.profil') }}" variant="secondary"
                            class="bg-white text-primary hover:bg-slate-100">
                            Lihat Profil Kelurahan
                        </x-guest::ui.button>
                        <x-guest::ui.button href="{{ route('guest.data-kelurahan') }}" variant="outline"
                            class="border-white/30 text-white hover:bg-white/10">
                            Data Kelurahan
                        </x-guest::ui.button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest::layout.app>
