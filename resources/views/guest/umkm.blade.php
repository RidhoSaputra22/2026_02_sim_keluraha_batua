<x-guest::layout.app title="UMKM">

    {{-- HERO SECTION --}}
    <x-guest::ui.hero size="xl" background="white" class="relative border-b border-slate-100">
        <x-slot:title>
            Dukung Produk Lokal <span class="text-primary">Kelurahan Kami</span>
        </x-slot:title>

        <x-slot:subtitle>
            Temukan berbagai produk berkualitas dari pengrajin, kuliner, dan jasa kreatif terbaik di lingkungan sekitar
            kita.
        </x-slot:subtitle>

        <x-slot:actions>
            <x-guest::ui.search-bar placeholder="Cari produk atau nama UMKM..." button-text="Cari"
                action="/umkm/search">

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block w-full mb-2">
                        Pencarian Populer:
                    </span>
                    <x-guest::ui.badge size="sm">Makanan</x-guest::ui.badge>
                    <x-guest::ui.badge size="sm">Kerajinan</x-guest::ui.badge>
                    <x-guest::ui.badge size="sm">Fashion</x-guest::ui.badge>
                    <x-guest::ui.badge size="sm">Jasa</x-guest::ui.badge>
                </div>
            </x-guest::ui.search-bar>

        </x-slot:actions>
    </x-guest::ui.hero>

    <main>

        {{-- STATISTICS SECTION --}}
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <x-guest::ui.section-header title="UMKM di Kelurahan Kami"
                    subtitle="Data terkini Usaha Mikro Kecil dan Menengah" size="md" />

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <x-guest::ui.stat-card title="Total UMKM" :value="(string) $totalUmkm" icon="store"
                        description="Unit usaha terdaftar" color="primary" />

                    @foreach ($jenisUsahaList->take(3) as $jenis)
                        <x-guest::ui.stat-card :title="$jenis->nama" :value="(string) $jenis->umkms_count" icon="storefront"
                            description="Unit usaha" color="{{ ['warning', 'success', 'info'][$loop->index] ?? 'primary' }}" />
                    @endforeach
                </div>
            </div>
        </section>

        {{-- CATEGORY FILTER --}}
        <section class="py-12 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h3 class="text-lg font-bold mb-6">Kategori Produk</h3>

                <div class="flex flex-wrap gap-3">
                    <x-guest::ui.button variant="primary" size="sm">
                        <x-guest::ui.icon name="apps" size="sm" />
                        Semua ({{ $totalUmkm }})
                    </x-guest::ui.button>
                    @foreach ($jenisUsahaList as $jenis)
                        <x-guest::ui.button variant="outline" size="sm">
                            <x-guest::ui.icon name="storefront" size="sm" />
                            {{ $jenis->nama }} ({{ $jenis->umkms_count }})
                        </x-guest::ui.button>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- UMKM LISTING --}}
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-2xl font-bold">UMKM Unggulan</h3>

                    <x-guest::ui.select name="sort" :options="[
                            ['value' => 'newest', 'label' => 'Terbaru'],
                            ['value' => 'popular', 'label' => 'Terpopuler'],
                            ['value' => 'name', 'label' => 'Nama A-Z'],
                        ]" placeholder="Urutkan" class="w-48" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    @forelse ($umkmList as $umkm)
                        <x-guest::ui.card variant="bordered" padding="sm">
                            <div class="aspect-video bg-slate-100 rounded-lg mb-4 flex items-center justify-center">
                                <x-guest::ui.icon name="store" size="xl" color="text-slate-400" />
                            </div>

                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    @if ($umkm->jenisUsaha)
                                        <x-guest::ui.badge variant="primary" size="sm">
                                            {{ $umkm->jenisUsaha->nama }}
                                        </x-guest::ui.badge>
                                    @endif
                                    @if ($umkm->sektor_umkm)
                                        <span class="text-xs text-slate-500">{{ $umkm->sektor_umkm }}</span>
                                    @endif
                                </div>

                                <h3 class="text-lg font-bold mb-2">{{ $umkm->nama_ukm }}</h3>
                                <p class="text-sm text-slate-600 mb-2">
                                    Pemilik: {{ $umkm->nama_pemilik }}
                                </p>

                                @if ($umkm->alamat)
                                    <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                                        <x-guest::ui.icon name="place" size="sm" />
                                        <span>{{ $umkm->alamat }}</span>
                                    </div>
                                @endif

                                @if ($umkm->rt)
                                    <div class="flex items-center gap-2 text-sm text-slate-500 mb-4">
                                        <x-guest::ui.icon name="location_on" size="sm" />
                                        <span>RT {{ $umkm->rt->nomor }} / RW {{ $umkm->rt->rw->nomor ?? '-' }}</span>
                                    </div>
                                @endif

                                @if ($umkm->no_hp)
                                    <x-slot:footer>
                                        <x-guest::ui.button variant="primary" size="sm" class="w-full" icon="phone">
                                            {{ $umkm->no_hp }}
                                        </x-guest::ui.button>
                                    </x-slot:footer>
                                @endif
                            </div>
                        </x-guest::ui.card>
                    @empty
                        <div class="col-span-3 text-center py-12 text-slate-500">
                            <x-guest::ui.icon name="store" size="xl" color="text-slate-300" class="mb-4" />
                            <p class="text-lg font-semibold">Belum ada data UMKM</p>
                            <p class="text-sm mt-2">Data UMKM akan ditampilkan di sini.</p>
                        </div>
                    @endforelse

                </div>

                {{-- Load More Button --}}
                <div class="mt-12 text-center">
                    <x-guest::ui.button variant="outline" size="lg">
                        Lihat Lebih Banyak
                    </x-guest::ui.button>
                </div>
            </div>
        </section>

        {{-- CALL TO ACTION --}}
        <section class="py-16 bg-primary/5">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <x-guest::ui.icon name="store" size="xl" color="text-primary" class="mb-6" />

                <h2 class="text-3xl font-bold mb-4">Punya UMKM?</h2>
                <p class="text-lg text-slate-600 mb-8">
                    Daftarkan usaha Anda di portal kami dan jangkau lebih banyak pelanggan di wilayah kelurahan.
                </p>

                <x-guest::ui.button size="lg" icon="add_business">
                    Daftarkan UMKM Anda
                </x-guest::ui.button>
            </div>
        </section>

    </main>

</x-guest::layout.app>
