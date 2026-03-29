<x-guest::layout.app title="Pariwisata">
    <x-guest::ui.hero size="xl" background="white">
        <x-slot:title>
            Jelajahi <span class="text-primary">Pariwisata & Rekomendasi Lokal</span>
        </x-slot:title>

        <x-slot:subtitle>
            Halaman ini menampilkan destinasi, kuliner, ruang terbuka, dan rekomendasi lokal yang dikelola dari panel admin website publik.
        </x-slot:subtitle>

        <x-slot:actions>
            <x-guest::ui.search-bar action="{{ route('guest.parawisata') }}"
                placeholder="Cari destinasi, kuliner, ruang terbuka, atau layanan publik..." button-text="Cari"
                value="{{ request('q') }}" />
        </x-slot:actions>
    </x-guest::ui.hero>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8 space-y-8">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('guest.parawisata', array_filter(['q' => request('q')])) }}"
                        class="px-4 py-2 rounded-full border {{ !request('kategori') ? 'bg-primary text-white border-primary' : 'border-slate-200 text-slate-600' }}">
                        Semua
                    </a>
                    @foreach (\App\Models\DestinasiWisata::kategoriOptions() as $value => $label)
                        <a href="{{ route('guest.parawisata', array_filter(['kategori' => $value, 'q' => request('q')])) }}"
                            class="px-4 py-2 rounded-full border {{ request('kategori') === $value ? 'bg-primary text-white border-primary' : 'border-slate-200 text-slate-600' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse ($destinasiWisata as $destinasi)
                        <x-guest::ui.card variant="bordered" padding="none" class="overflow-hidden h-full">
                            <div class="aspect-[16/10] bg-slate-100">
                                @if ($destinasi->gambar)
                                    <img src="{{ asset('storage/' . $destinasi->gambar) }}" alt="{{ $destinasi->nama }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-primary/5">
                                        <x-guest::ui.icon name="place" size="xl" color="text-primary" />
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <div class="flex items-center justify-between gap-4 mb-3">
                                    <x-guest::ui.badge variant="primary" size="sm">
                                        {{ \App\Models\DestinasiWisata::kategoriOptions()[$destinasi->kategori] ?? ucfirst($destinasi->kategori) }}
                                    </x-guest::ui.badge>
                                    @if ($destinasi->is_featured)
                                        <span class="text-xs font-semibold text-amber-600">Rekomendasi</span>
                                    @endif
                                </div>

                                <h3 class="text-xl font-bold text-slate-900">{{ $destinasi->nama }}</h3>
                                <p class="text-sm text-slate-500 leading-6 mt-3">{{ $destinasi->ringkasan }}</p>

                                <div class="mt-5 space-y-2 text-sm text-slate-600">
                                    @if ($destinasi->alamat)
                                        <div class="flex items-start gap-2">
                                            <x-guest::ui.icon name="place" size="sm" color="text-primary" class="mt-0.5" />
                                            <span>{{ $destinasi->alamat }}</span>
                                        </div>
                                    @endif
                                    @if ($destinasi->jam_operasional)
                                        <div class="flex items-start gap-2">
                                            <x-guest::ui.icon name="schedule" size="sm" color="text-primary" class="mt-0.5" />
                                            <span>{{ $destinasi->jam_operasional }}</span>
                                        </div>
                                    @endif
                                    @if ($destinasi->harga_tiket)
                                        <div class="flex items-start gap-2">
                                            <x-guest::ui.icon name="payments" size="sm" color="text-primary" class="mt-0.5" />
                                            <span>{{ $destinasi->harga_tiket }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if ($destinasi->deskripsi)
                                    <div class="mt-5 text-sm text-slate-500 leading-6">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($destinasi->deskripsi), 150) }}
                                    </div>
                                @endif

                                <div class="mt-6 flex flex-wrap gap-3">
                                    @if ($destinasi->maps_url)
                                        <x-guest::ui.button href="{{ $destinasi->maps_url }}" target="_blank" rel="noreferrer" variant="outline">
                                            Buka Maps
                                        </x-guest::ui.button>
                                    @endif
                                    @if ($destinasi->kontak)
                                        <x-guest::ui.button href="tel:{{ preg_replace('/\D+/', '', $destinasi->kontak) }}" variant="ghost">
                                            {{ $destinasi->kontak }}
                                        </x-guest::ui.button>
                                    @endif
                                </div>
                            </div>
                        </x-guest::ui.card>
                    @empty
                        <div class="md:col-span-2">
                            <x-guest::ui.card variant="bordered" padding="lg" class="text-center">
                                <x-guest::ui.icon name="place" size="xl" color="text-slate-300" class="mb-4" />
                                <p class="text-lg font-semibold text-slate-900">Belum ada destinasi yang sesuai.</p>
                            </x-guest::ui.card>
                        </div>
                    @endforelse
                </div>

                <div>{{ $destinasiWisata->links('vendor.pagination.guest-light') }}</div>
            </div>

            <aside class="lg:col-span-4 space-y-6">
                <x-guest::ui.card variant="bordered" padding="lg">
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Rekomendasi Utama</h3>
                    <div class="space-y-4">
                        @forelse ($destinasiUnggulan as $item)
                            <div class="rounded-2xl border border-slate-100 p-4">
                                <div class="font-semibold text-slate-900">{{ $item->nama }}</div>
                                <div class="text-sm text-slate-500 mt-1">{{ $item->ringkasan }}</div>
                                <div class="text-xs text-primary mt-3">
                                    {{ \App\Models\DestinasiWisata::kategoriOptions()[$item->kategori] ?? ucfirst($item->kategori) }}
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada rekomendasi unggulan.</p>
                        @endforelse
                    </div>
                </x-guest::ui.card>

                <x-guest::ui.card padding="lg" class="bg-primary text-white">
                    <h3 class="text-xl font-bold mb-3">Punya Rekomendasi Tempat?</h3>
                    <p class="text-sm text-white/80 leading-6 mb-5">
                        Anda bisa menyampaikan masukan atau usulan lokasi yang layak dipublikasikan melalui halaman pengaduan atau kontak.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <x-guest::ui.button href="{{ route('guest.pengaduan') }}" variant="secondary"
                            class="bg-white text-primary hover:bg-slate-100">
                            Sampaikan Usulan
                        </x-guest::ui.button>
                        <x-guest::ui.button href="{{ route('guest.kontak') }}" variant="outline"
                            class="border-white/30 text-white hover:bg-white/10">
                            Hubungi Kami
                        </x-guest::ui.button>
                    </div>
                </x-guest::ui.card>
            </aside>
        </div>
    </main>
</x-guest::layout.app>
