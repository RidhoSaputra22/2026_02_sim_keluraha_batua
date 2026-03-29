<x-guest::layout.app title="UMKM">
    <x-guest::ui.hero size="xl" background="white">
        <x-slot:title>
            Direktori <span class="text-primary">UMKM Kelurahan</span>
        </x-slot:title>

        <x-slot:subtitle>
            Data UMKM ini terhubung langsung dengan modul data usaha, sehingga warga dapat mencari usaha lokal berdasarkan nama, kategori, sektor, dan status.
        </x-slot:subtitle>

        <x-slot:actions>
            <x-guest::ui.search-bar action="{{ route('guest.umkm') }}"
                placeholder="Cari nama UMKM, pemilik, atau sektor usaha..." button-text="Cari"
                value="{{ request('q') }}" />
        </x-slot:actions>
    </x-guest::ui.hero>

    <main>
        <section class="py-14 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <x-guest::ui.stat-card title="Total UMKM" :value="(string) $totalUmkm" icon="store" description="Unit usaha terdata" color="primary" :counter-value="$totalUmkm" />
                    <x-guest::ui.stat-card title="UMKM Aktif" :value="(string) $totalUmkmAktif" icon="verified" description="Status aktif" color="success" :counter-value="$totalUmkmAktif" />
                    @foreach ($topJenisUsaha as $jenis)
                        <x-guest::ui.stat-card :title="$jenis->nama" :value="(string) $jenis->umkms_count" icon="storefront"
                            description="Unit usaha" color="{{ $loop->first ? 'warning' : 'info' }}" :counter-value="$jenis->umkms_count" />
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-10 bg-slate-50 border-y border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <form method="GET" action="{{ route('guest.umkm') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="hidden" name="q" value="{{ request('q') }}">

                    <div>
                        <label class="text-sm font-semibold text-slate-700 mb-2 block">Jenis Usaha</label>
                        <select name="jenis_usaha_id" class="w-full p-3 rounded-lg border border-slate-200 focus:border-primary focus:ring-1 focus:ring-primary">
                            <option value="">Semua Jenis</option>
                            @foreach ($jenisUsahaList as $jenis)
                                <option value="{{ $jenis->id }}" @selected(request('jenis_usaha_id') == $jenis->id)>{{ $jenis->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700 mb-2 block">Sektor</label>
                        <select name="sektor_umkm" class="w-full p-3 rounded-lg border border-slate-200 focus:border-primary focus:ring-1 focus:ring-primary">
                            <option value="">Semua Sektor</option>
                            @foreach ($sektorList as $sektor)
                                <option value="{{ $sektor }}" @selected(request('sektor_umkm') === $sektor)>{{ $sektor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700 mb-2 block">Status</label>
                        <select name="status" class="w-full p-3 rounded-lg border border-slate-200 focus:border-primary focus:ring-1 focus:ring-primary">
                            <option value="">Semua Status</option>
                            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                            <option value="tidak_aktif" @selected(request('status') === 'tidak_aktif')>Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-3">
                        <x-guest::ui.button type="submit" class="w-full">Terapkan Filter</x-guest::ui.button>
                        <a href="{{ route('guest.umkm') }}" class="px-4 py-3 rounded-lg border border-slate-200 text-slate-600 hover:border-primary hover:text-primary">Reset</a>
                    </div>
                </form>
            </div>
        </section>

        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse ($umkmList as $umkm)
                        <x-guest::ui.card variant="bordered" padding="lg" class="h-full">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                @if ($umkm->jenisUsaha)
                                    <x-guest::ui.badge variant="primary" size="sm">{{ $umkm->jenisUsaha->nama }}</x-guest::ui.badge>
                                @else
                                    <x-guest::ui.badge variant="warning" size="sm">Belum dikategorikan</x-guest::ui.badge>
                                @endif

                                @if ($umkm->status)
                                    <span class="text-xs font-semibold {{ $umkm->status === 'aktif' ? 'text-green-600' : 'text-slate-400' }}">
                                        {{ ucfirst(str_replace('_', ' ', $umkm->status)) }}
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-xl font-bold text-slate-900">{{ $umkm->nama_ukm }}</h3>
                            <p class="text-sm text-slate-500 mt-2">Pemilik: {{ $umkm->nama_pemilik }}</p>

                            <div class="mt-5 space-y-3 text-sm text-slate-600">
                                @if ($umkm->sektor_umkm)
                                    <div class="flex items-start gap-2">
                                        <x-guest::ui.icon name="category" size="sm" color="text-primary" class="mt-0.5" />
                                        <span>{{ $umkm->sektor_umkm }}</span>
                                    </div>
                                @endif
                                @if ($umkm->alamat)
                                    <div class="flex items-start gap-2">
                                        <x-guest::ui.icon name="place" size="sm" color="text-primary" class="mt-0.5" />
                                        <span>{{ $umkm->alamat }}</span>
                                    </div>
                                @endif
                                @if ($umkm->rt)
                                    <div class="flex items-start gap-2">
                                        <x-guest::ui.icon name="map" size="sm" color="text-primary" class="mt-0.5" />
                                        <span>RT {{ $umkm->rt->nomor }} / RW {{ $umkm->rt->rw->nomor ?? '-' }}</span>
                                    </div>
                                @endif
                            </div>

                            @if ($umkm->no_hp)
                                <div class="mt-6">
                                    <x-guest::ui.button href="https://wa.me/{{ preg_replace('/\D+/', '', $umkm->no_hp) }}" target="_blank" rel="noreferrer" variant="outline" class="w-full">
                                        {{ $umkm->no_hp }}
                                    </x-guest::ui.button>
                                </div>
                            @endif
                        </x-guest::ui.card>
                    @empty
                        <div class="xl:col-span-3">
                            <x-guest::ui.card variant="bordered" padding="lg" class="text-center">
                                <x-guest::ui.icon name="store" size="xl" color="text-slate-300" class="mb-4" />
                                <p class="text-lg font-semibold text-slate-900">Belum ada UMKM yang sesuai filter.</p>
                            </x-guest::ui.card>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">{{ $umkmList->links('vendor.pagination.guest-light') }}</div>
            </div>
        </section>
    </main>
</x-guest::layout.app>
