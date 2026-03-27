<x-guest::layout.app title="Data Kelurahan">
    @php
        $luasKelurahan = filled($kelurahan?->luas_area)
            ? number_format((float) $kelurahan->luas_area, 2, ',', '.') . ' km²'
            : null;

        $headlineStats = [
            [
                'label' => 'Total Penduduk',
                'value' => number_format($totalPenduduk),
                'meta' => 'jiwa terdata',
                'icon' => 'people',
            ],
            [
                'label' => 'Kepala Keluarga',
                'value' => number_format($totalKK),
                'meta' => ($avgAnggotaKeluarga > 0 ? number_format($avgAnggotaKeluarga, 1, ',', '.') . ' rata-rata anggota/KK' : 'Data keluarga aktif'),
                'icon' => 'home',
            ],
            [
                'label' => 'Cakupan Wilayah',
                'value' => number_format($totalRw) . ' RW',
                'meta' => number_format($totalRt) . ' RT aktif',
                'icon' => 'map',
            ],
            [
                'label' => 'Arsip & Layanan',
                'value' => number_format($totalDokumen + $totalLayanan),
                'meta' => number_format($totalDokumen) . ' dokumen, ' . number_format($totalLayanan) . ' layanan',
                'icon' => 'folder',
            ],
        ];

        $dataPillars = [
            [
                'title' => 'Kependudukan',
                'icon' => 'people',
                'description' => 'Informasi demografi warga, komposisi gender, status data, dan struktur keluarga yang tercatat di sistem.',
                'headline' => number_format($totalPenduduk) . ' jiwa',
                'detail' => number_format($totalKK) . ' KK • ' . number_format($totalLakiLaki) . ' laki-laki • ' . number_format($totalPerempuan) . ' perempuan',
                'href' => route('guest.cek-data'),
                'link' => 'Cek data warga',
            ],
            [
                'title' => 'Lahan & Wilayah',
                'icon' => 'map',
                'description' => 'Gambaran struktur wilayah administratif, cakupan RW/RT, dan profil kelurahan yang dikelola secara digital.',
                'headline' => number_format($totalRw) . ' RW / ' . number_format($totalRt) . ' RT',
                'detail' => $luasKelurahan ?: 'Profil luas wilayah tersedia di data kelurahan',
                'href' => route('guest.profil'),
                'link' => 'Lihat profil wilayah',
            ],
            [
                'title' => 'Administrasi',
                'icon' => 'description',
                'description' => 'Akses cepat ke layanan surat, arsip publik, berita, dan kanal pelayanan administrasi kelurahan.',
                'headline' => number_format($totalLayanan) . ' layanan aktif',
                'detail' => number_format($totalDokumen) . ' dokumen publik • ' . number_format($totalBerita) . ' berita terbit',
                'href' => route('guest.administrasi'),
                'link' => 'Buka layanan administrasi',
            ],
        ];

        $facilityStats = [
            ['label' => 'UMKM', 'value' => number_format($totalUmkm), 'icon' => 'store'],
            ['label' => 'Sekolah', 'value' => number_format($totalSekolah), 'icon' => 'school'],
            ['label' => 'Faskes', 'value' => number_format($totalFaskes), 'icon' => 'local_hospital'],
            ['label' => 'Tempat Ibadah', 'value' => number_format($totalTempatIbadah), 'icon' => 'account_balance'],
        ];

        $popularLinks = [
            ['label' => 'Jumlah Penduduk', 'href' => route('guest.search', ['q' => 'jumlah penduduk'])],
            ['label' => 'Peta Wilayah', 'href' => route('guest.search', ['q' => 'profil wilayah'])],
            ['label' => 'Izin Domisili', 'href' => route('guest.search', ['q' => 'surat domisili'])],
        ];

        $quickAccess = [
            [
                'title' => 'Cek Data Warga',
                'description' => 'Validasi data penduduk secara mandiri menggunakan NIK.',
                'href' => route('guest.cek-data'),
                'icon' => 'verified_user',
            ],
            [
                'title' => 'Layanan Surat',
                'description' => 'Lihat persyaratan dan estimasi pengurusan administrasi.',
                'href' => route('guest.administrasi'),
                'icon' => 'description',
            ],
            [
                'title' => 'Dokumen Publik',
                'description' => 'Unduh laporan, formulir, dan arsip resmi kelurahan.',
                'href' => route('guest.publikasi'),
                'icon' => 'folder',
            ],
            [
                'title' => 'Hubungi Kelurahan',
                'description' => 'Temukan alamat kantor, kontak, dan jam layanan.',
                'href' => route('guest.kontak'),
                'icon' => 'call',
            ],
        ];
    @endphp

    <x-guest::ui.hero size="xl" background="gradient" class="overflow-hidden">
        <x-slot:badge>
            <x-guest::ui.badge variant="primary" size="lg" icon="dashboard">
                Dashboard Publik Kelurahan
            </x-guest::ui.badge>
        </x-slot:badge>

        <x-slot:title>
            Pusat Data &amp; Informasi Kelurahan
        </x-slot:title>

        <x-slot:subtitle>
            Akses transparansi data kependudukan, wilayah, dan administrasi secara real-time
            untuk pelayanan publik yang lebih baik.
        </x-slot:subtitle>

        <x-slot:actions>
            <div class="w-full max-w-5xl">
                <x-guest::ui.search-bar action="{{ route('guest.search') }}"
                    placeholder="Cari data kependudukan, layanan, dokumen, atau statistik..."
                    button-text="Cari Data">
                    <div class="mt-5 flex flex-wrap items-center justify-center gap-3 text-sm text-slate-500">
                        <span>Populer:</span>
                        @foreach ($popularLinks as $item)
                            <a href="{{ $item['href'] }}"
                                class="rounded-full border border-slate-200 bg-white/90 px-4 py-1.5 text-slate-600 transition hover:border-primary/30 hover:text-primary">
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </div>
                </x-guest::ui.search-bar>
            </div>
        </x-slot:actions>
    </x-guest::ui.hero>

    <section class="relative z-10 -mt-10 pb-10 md:-mt-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($headlineStats as $stat)
                    <x-guest::ui.card variant="bordered" padding="lg"
                        class="h-full bg-white/95 shadow-xl shadow-primary/5 backdrop-blur">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                                <p class="mt-3 text-3xl font-extrabold text-slate-900">{{ $stat['value'] }}</p>
                                <p class="mt-2 text-sm text-slate-500">{{ $stat['meta'] }}</p>
                            </div>
                            <div
                                class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                <x-guest::ui.icon :name="$stat['icon']" size="lg" color="text-primary" />
                            </div>
                        </div>
                    </x-guest::ui.card>
                @endforeach
            </div>
        </div>
    </section>



    <section class="py-20 ">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h2 class="text-3xl font-extrabold text-slate-900 md:text-4xl">Highlight Statistik</h2>
                    <p class="mt-3 max-w-2xl text-base leading-7 text-slate-500">
                        Ringkasan cepat kondisi kependudukan dan wilayah berdasarkan data yang tersimpan di sistem
                        kelurahan saat ini.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <x-guest::ui.button
                        href="{{ $laporanPublik ? route('guest.publikasi.download', $laporanPublik) : route('guest.publikasi', ['dokumen_kategori' => 'laporan']) }}"
                        variant="outline" size="sm">
                        Unduh Laporan
                    </x-guest::ui.button>
                    <x-guest::ui.button href="{{ route('guest.cek-data') }}" size="sm">
                        Lihat Semua Detail
                    </x-guest::ui.button>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-3">
                <div class="space-y-6 xl:col-span-2">
                    <x-guest::ui.card variant="bordered" padding="lg" >
                        <div class="mb-8 flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Sebaran Penduduk per RW</h3>
                                <p class="mt-2 text-sm text-slate-500">
                                    Menampilkan {{ $chartRwHighlights->count() }} RW dengan jumlah penduduk terdata
                                    tertinggi.
                                </p>
                            </div>
                            <x-guest::ui.badge variant="primary" size="sm">Live Database</x-guest::ui.badge>
                        </div>

                        @if ($chartRwHighlights->isNotEmpty())
                            <div class="relative h-80 rounded-2xl border border-slate-100 bg-white px-4 py-6">
                                <div
                                    class="pointer-events-none absolute inset-x-4 inset-y-6 flex flex-col justify-between opacity-60">
                                    <div class="border-t border-dashed border-slate-200"></div>
                                    <div class="border-t border-dashed border-slate-200"></div>
                                    <div class="border-t border-dashed border-slate-200"></div>
                                    <div class="border-t border-dashed border-slate-200"></div>
                                </div>

                                <div class="relative z-10 flex h-full items-end gap-3 md:gap-5">
                                    @foreach ($chartRwHighlights as $rw)
                                        @php
                                            $barHeight = $chartRwMax > 0
                                                ? max(18, (int) round(($rw['total_penduduk'] / $chartRwMax) * 100))
                                                : 18;
                                        @endphp
                                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-4">
                                            <div class="flex h-full w-full items-end justify-center">
                                                <div class="relative w-full max-w-[90px] rounded-t-md bg-gradient-to-t from-primary to-[#ff6b70] shadow-lg shadow-primary/20"
                                                    style="height: {{ $barHeight }}%;">
                                                    <span
                                                        class="absolute -top-10 left-1/2 -translate-x-1/2 rounded-full bg-slate-900 px-3 py-1 text-xs font-semibold whitespace-nowrap text-white">
                                                        {{ number_format($rw['total_penduduk']) }} jiwa
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-sm font-bold text-slate-900">{{ $rw['label'] }}</p>
                                                <p class="text-xs text-slate-500">{{ $rw['total_rt'] }} RT</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-6 grid gap-3 md:grid-cols-3">
                                <div class="rounded-2xl bg-white p-4">
                                    <p class="text-sm text-slate-500">RW terbanyak</p>
                                    <p class="mt-2 text-lg font-bold text-slate-900">
                                        {{ $chartRwHighlights->first()['label'] ?? '-' }}
                                    </p>
                                </div>
                                <div class="rounded-2xl bg-white p-4">
                                    <p class="text-sm text-slate-500">Penduduk tertinggi</p>
                                    <p class="mt-2 text-lg font-bold text-slate-900">
                                        {{ number_format($chartRwHighlights->first()['total_penduduk'] ?? 0) }} jiwa
                                    </p>
                                </div>
                                <div class="rounded-2xl bg-white p-4">
                                    <p class="text-sm text-slate-500">Rata-rata per RW</p>
                                    <p class="mt-2 text-lg font-bold text-slate-900">
                                        {{ $totalRw > 0 ? number_format($totalPenduduk / $totalRw, 1, ',', '.') : '0' }} jiwa
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center">
                                <x-guest::ui.icon name="bar_chart" size="xl" color="text-slate-300" />
                                <p class="mt-4 text-lg font-semibold text-slate-900">Belum ada data sebaran RW.</p>
                            </div>
                        @endif
                    </x-guest::ui.card>

                    <x-guest::ui.card variant="bordered" padding="lg">
                        <div class="mb-8 flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Persebaran Warga Menurut Range Umur</h3>
                                <p class="mt-2 text-sm text-slate-500">
                                    Distribusi usia warga yang dihitung dari tanggal lahir pada NIK.
                                </p>
                            </div>
                            <x-guest::ui.badge variant="info" size="sm">
                                {{ number_format($cakupanDataUmur) }}/{{ number_format($totalPenduduk) }} data terpetakan
                            </x-guest::ui.badge>
                        </div>

                        @if ($cakupanDataUmur > 0)
                            <div class="relative h-72 rounded-2xl border border-slate-100 bg-white px-4 py-6">
                                <div
                                    class="pointer-events-none absolute inset-x-4 inset-y-6 flex flex-col justify-between opacity-60">
                                    <div class="border-t border-dashed border-slate-200"></div>
                                    <div class="border-t border-dashed border-slate-200"></div>
                                    <div class="border-t border-dashed border-slate-200"></div>
                                    <div class="border-t border-dashed border-slate-200"></div>
                                </div>

                                <div class="relative z-10 flex h-full items-end gap-3 md:gap-4">
                                    @foreach ($sebaranUmur as $item)
                                        @php
                                            $barHeight = $chartUmurMax > 0
                                                ? max(14, (int) round(($item['value'] / $chartUmurMax) * 100))
                                                : 14;
                                        @endphp
                                        <div class="flex h-full flex-1 flex-col items-center justify-end gap-4">
                                            <div class="flex h-full w-full items-end justify-center">
                                                <div class="relative w-full max-w-[88px] rounded-t-md bg-gradient-to-t from-[#0f172a] via-[#334155] to-[#94a3b8] shadow-lg shadow-slate-900/15"
                                                    style="height: {{ $barHeight }}%;">
                                                    <span
                                                        class="absolute -top-10 left-1/2 -translate-x-1/2 rounded-full bg-primary px-3 py-1 text-xs font-semibold whitespace-nowrap text-white">
                                                        {{ number_format($item['value']) }} jiwa
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <p class="text-sm font-bold text-slate-900">{{ $item['label'] }}</p>
                                                <p class="text-xs text-slate-500">{{ $item['title'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-6 grid gap-3 md:grid-cols-3">
                                <div class="rounded-2xl bg-white p-4">
                                    <p class="text-sm text-slate-500">Kelompok terbanyak</p>
                                    <p class="mt-2 text-lg font-bold text-slate-900">
                                        {{ ($kelompokUmurTerbesar['label'] ?? '-') . ' th' }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ $kelompokUmurTerbesar['title'] ?? 'Belum tersedia' }}
                                    </p>
                                </div>
                                <div class="rounded-2xl bg-white p-4">
                                    <p class="text-sm text-slate-500">Usia produktif</p>
                                    <p class="mt-2 text-lg font-bold text-slate-900">
                                        {{ number_format($usiaProduktif) }} jiwa
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">Rentang 18-59 tahun</p>
                                </div>
                                <div class="rounded-2xl bg-white p-4">
                                    <p class="text-sm text-slate-500">Lansia</p>
                                    <p class="mt-2 text-lg font-bold text-slate-900">
                                        {{ number_format($usiaLansia) }} jiwa
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">Usia 60 tahun ke atas</p>
                                </div>
                            </div>

                            <p class="mt-4 text-xs leading-6 text-slate-400">
                                Catatan: umur dihitung otomatis dari NIK yang valid. Jika ada data NIK tidak lengkap,
                                warga tersebut tidak masuk ke chart umur.
                            </p>
                        @else
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-10 text-center">
                                <x-guest::ui.icon name="equalizer" size="xl" color="text-slate-300" />
                                <p class="mt-4 text-lg font-semibold text-slate-900">Belum ada data umur yang bisa dihitung.</p>
                            </div>
                        @endif
                    </x-guest::ui.card>
                </div>

                <div class="space-y-6">
                    <x-guest::ui.card variant="bordered" padding="lg">
                        <h3 class="text-xl font-bold text-slate-900">Komposisi Gender</h3>
                        <p class="mt-2 text-sm text-slate-500">Distribusi jenis kelamin dari warga yang sudah terdata.</p>

                        <div class="mt-6 space-y-5">
                            @forelse ($komposisiGender as $item)
                                <div>
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $item['label'] }}</p>
                                            <p class="text-sm text-slate-500">{{ number_format($item['value']) }} jiwa</p>
                                        </div>
                                        <span class="text-sm font-semibold text-slate-900">{{ $item['percentage'] }}%</span>
                                    </div>
                                    <div class="mt-3 h-2.5 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full {{ $item['color'] }}"
                                            style="width: {{ max(6, $item['percentage']) }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">Belum ada data gender yang tercatat.</p>
                            @endforelse
                        </div>
                    </x-guest::ui.card>

                    <x-guest::ui.card variant="bordered" padding="lg">
                        <h3 class="text-xl font-bold text-slate-900">Status Data & Agama</h3>
                        <p class="mt-2 text-sm text-slate-500">Membantu memantau kualitas data kependudukan yang aktif.</p>

                        <div class="mt-6 space-y-4">
                            @forelse ($statusPenduduk as $item)
                                <div class="rounded-2xl border border-slate-100 p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $item['label'] }}</p>
                                            <p class="text-sm text-slate-500">{{ number_format($item['value']) }} data</p>
                                        </div>
                                        <x-guest::ui.badge variant="info" size="sm">{{ $item['percentage'] }}%</x-guest::ui.badge>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">Belum ada status data yang bisa ditampilkan.</p>
                            @endforelse
                        </div>

                        <div class="mt-6 border-t border-slate-100 pt-6">
                            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-400">Agama Tercatat</p>
                            <div class="mt-4 space-y-3">
                                @forelse ($komposisiAgama as $item)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-medium text-slate-700">{{ $item['label'] }}</span>
                                        <span class="text-slate-500">{{ number_format($item['value']) }} warga</span>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-500">Belum ada data agama yang tercatat.</p>
                                @endforelse
                            </div>
                        </div>
                    </x-guest::ui.card>

                    <x-guest::ui.card variant="bordered" padding="lg">
                        <h3 class="text-xl font-bold text-slate-900">Fasilitas & Jejaring</h3>
                        <p class="mt-2 text-sm text-slate-500">Gambaran sarana publik dan aktivitas ekonomi warga.</p>

                        <div class="mt-6 grid grid-cols-2 gap-3">
                            @foreach ($facilityStats as $item)
                                <div class="rounded-2xl bg-background-light p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-sm font-medium text-slate-500">{{ $item['label'] }}</p>
                                        <x-guest::ui.icon :name="$item['icon']" size="sm" color="text-primary" />
                                    </div>
                                    <p class="mt-3 text-2xl font-extrabold text-slate-900">{{ $item['value'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </x-guest::ui.card>
                </div>
            </div>
        </div>
    </section>

    <section class="pb-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-guest::ui.kelurahan-map
                :endpoint="route('guest.api.peta-kelurahan')"
                :title="'Peta Wilayah ' . ($kelurahan?->nama ?? 'Kelurahan')"
                subtitle="Eksplorasi batas kelurahan, wilayah RW, dan seluruh layer aktif melalui peta interaktif yang diambil langsung dari endpoint data peta publik." />
        </div>
    </section>


</x-guest::layout.app>
