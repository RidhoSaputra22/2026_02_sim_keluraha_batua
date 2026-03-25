<x-guest::layout.app title="Data Kelurahan">

    {{-- HERO SECTION --}}
    <x-guest::ui.hero size="xl" background="gradient">
        <x-slot:title>
            Pusat Data &amp; Informasi Kelurahan
        </x-slot:title>

        <x-slot:subtitle>
            Akses transparansi data kependudukan, wilayah, dan administrasi secara real-time untuk pelayanan publik yang
            lebih baik.
        </x-slot:subtitle>

        <x-slot:actions>
            <x-guest::ui.search-bar placeholder="Cari data kependudukan, lahan, atau statistik..." button-text="Cari Data"
              action="#">
                <div class="mt-6 flex flex-wrap justify-center gap-4 text-sm text-slate-500">
                    <span>Populer:</span>
                    <a class="hover:text-primary border-b border-slate-300" href="#">Jumlah Penduduk</a>
                    <a class="hover:text-primary border-b border-slate-300" href="#">Peta Wilayah</a>
                    <a class="hover:text-primary border-b border-slate-300" href="#">Izin Domisili</a>
                </div>
            </x-guest::ui.search-bar>
        </x-slot:actions>
    </x-guest::ui.hero>

    {{-- MAIN CATEGORIES --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Population Card --}}
                <x-guest::ui.card variant="bordered" padding="lg"
                    class="group hover:border-primary/50 hover:shadow-2xl hover:shadow-primary/5 transition-all duration-300 bg-background-light">
                    <div
                        class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                        <x-guest::ui.icon name="people" size="lg" color="text-primary group-hover:text-white" />
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Kependudukan</h3>
                    <p class="text-slate-500 mb-6">Informasi demografi, jenis kelamin, usia, dan persebaran penduduk.
                    </p>
                    <div class="p-4 bg-white rounded-lg border border-slate-200">
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Penduduk
                                </p>
                                <p class="text-2xl font-bold text-primary">{{ number_format($totalPenduduk) }}</p>
                            </div>
                            <x-guest::ui.badge variant="success" size="sm">+2.4%</x-guest::ui.badge>
                        </div>
                    </div>
                </x-guest::ui.card>

                {{-- Land & Territory Card --}}
                <x-guest::ui.card variant="bordered" padding="lg"
                    class="group hover:border-primary/50 hover:shadow-2xl hover:shadow-primary/5 transition-all duration-300 bg-background-light">
                    <div
                        class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                        <x-guest::ui.icon name="map" size="lg" color="text-primary group-hover:text-white" />
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Lahan &amp; Wilayah</h3>
                    <p class="text-slate-500 mb-6">Data batas wilayah, luas lahan, jumlah RT/RW dan zonasi penggunaan
                        lahan.</p>
                    <div class="p-4 bg-white rounded-lg border border-slate-200">
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Luas Wilayah
                                </p>
                                <p class="text-2xl font-bold text-primary text-nowrap">128.5 Ha</p>
                            </div>
                            <x-guest::ui.badge size="sm">{{ $totalRw }} RW</x-guest::ui.badge>
                        </div>
                    </div>
                </x-guest::ui.card>

                {{-- Administration Card --}}
                <x-guest::ui.card variant="bordered" padding="lg"
                    class="group hover:border-primary/50 hover:shadow-2xl hover:shadow-primary/5 transition-all duration-300 bg-background-light">
                    <div
                        class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                        <x-guest::ui.icon name="description" size="lg" color="text-primary group-hover:text-white" />
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Administrasi</h3>
                    <p class="text-slate-500 mb-6">Rekapitulasi layanan surat menyurat, sertifikasi, dan administrasi
                        kependudukan.</p>
                    <div class="p-4 bg-white rounded-lg border border-slate-200">
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Layanan Selesai
                                </p>
                                <p class="text-2xl font-bold text-primary">1,208</p>
                            </div>
                            <x-guest::ui.badge variant="success" size="sm">Bulan Ini</x-guest::ui.badge>
                        </div>
                    </div>
                </x-guest::ui.card>
            </div>
        </div>
    </section>

    {{-- STATISTICS SECTION --}}
    <section class="py-20 bg-background-light">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4">
                <div>
                    <h2 class="text-3xl font-bold mb-2">Highlight Statistik</h2>
                    <p class="text-slate-500">Pertumbuhan penduduk dan tren administrasi selama 5 tahun terakhir.</p>
                </div>
                <div class="flex gap-2">
                    <x-guest::ui.button variant="outline" size="sm">Unduh Laporan</x-guest::ui.button>
                    <x-guest::ui.button size="sm">Lihat Semua Detail</x-guest::ui.button>
                </div>
            </div>

            <x-guest::ui.card variant="bordered" padding="lg">
                <div class="mb-8">
                    <h4 class="text-lg font-bold mb-1">Pertumbuhan Populasi Tahunan</h4>
                    <p class="text-sm text-slate-500">Satuan: Ribu Jiwa</p>
                </div>
                <div class="relative h-80 flex items-end justify-between gap-4 md:gap-8 px-4 border-b border-slate-100">
                    {{-- Grid Lines --}}
                    <div
                        class="absolute inset-x-0 top-0 h-full flex flex-col justify-between -z-0 pointer-events-none opacity-50">
                        <div class="border-t border-slate-100 w-full h-0"></div>
                        <div class="border-t border-slate-100 w-full h-0"></div>
                        <div class="border-t border-slate-100 w-full h-0"></div>
                        <div class="border-t border-slate-100 w-full h-0"></div>
                    </div>
                    {{-- Bars --}}
                    @php
                    $bars = [
                    ['year' => '2020', 'height' => '40%', 'bg' => 'bg-primary/20', 'hover' =>
                    'group-hover:bg-primary/40', 'value' => '4.2k', 'active' => false],
                    ['year' => '2021', 'height' => '55%', 'bg' => 'bg-primary/30', 'hover' =>
                    'group-hover:bg-primary/50', 'value' => '4.5k', 'active' => false],
                    ['year' => '2022', 'height' => '65%', 'bg' => 'bg-primary/50', 'hover' =>
                    'group-hover:bg-primary/70', 'value' => '4.8k', 'active' => false],
                    ['year' => '2023', 'height' => '80%', 'bg' => 'bg-primary/70', 'hover' =>
                    'group-hover:bg-primary/90', 'value' => '5.1k', 'active' => false],
                    ['year' => '2024', 'height' => '95%', 'bg' => 'bg-primary', 'hover' => 'group-hover:bg-primary/90',
                    'value' => '5.4k', 'active' => true],
                    ];
                    @endphp
                    @foreach($bars as $bar)
                    <div class="flex-1 flex flex-col items-center gap-4 group cursor-pointer relative z-10">
                        <div class="w-full {{ $bar['bg'] }} {{ $bar['hover'] }} rounded-t-lg transition-all flex items-end justify-center relative"
                            style="height: {{ $bar['height'] }}">
                            <span
                                class="absolute -top-8 text-sm font-bold opacity-0 group-hover:opacity-100 transition-opacity">{{ $bar['value'] }}</span>
                        </div>
                        <span
                            class="text-sm font-medium {{ $bar['active'] ? 'text-slate-900 font-bold' : 'text-slate-500' }}">{{ $bar['year'] }}</span>
                    </div>
                    @endforeach
                </div>
            </x-guest::ui.card>
        </div>
    </section>

</x-guest::layout.app>
