<x-guest::layout.app title="Surat Online">

    {{-- HERO SECTION --}}
    <x-guest::ui.hero size="xl" background="gradient">
        <x-slot:title>
            Layanan Surat <span class="text-primary">Mandiri Online</span>
        </x-slot:title>

        <x-slot:subtitle>
            Urus administrasi kependudukan lebih cepat, mudah, dan transparan dari mana saja tanpa perlu mengantre di
            kantor kelurahan.
        </x-slot:subtitle>

        <x-slot:actions>
            <x-guest::ui.search-bar placeholder="Cari layanan surat (cth: Surat Domisili...)" button-text="Cari" action="#">
                <div class="flex flex-wrap justify-center gap-2 mt-4">
                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Populer:</span>
                    <a class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full hover:bg-primary/10 hover:text-primary transition-colors"
                        href="#">Domisili</a>
                    <a class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full hover:bg-primary/10 hover:text-primary transition-colors"
                        href="#">SKU</a>
                    <a class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full hover:bg-primary/10 hover:text-primary transition-colors"
                        href="#">SKTM</a>
                </div>
            </x-guest::ui.search-bar>
        </x-slot:actions>
    </x-guest::ui.hero>

    {{-- MAIN CONTENT --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Services Grid --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Pilih Jenis Layanan</h2>
                        <p class="text-slate-500">Pilih dokumen yang ingin Anda ajukan hari ini.</p>
                    </div>
                    <a class="text-sm font-semibold text-primary hover:underline flex items-center gap-1" href="#">
                        Lihat Semua
                        <x-guest::ui.icon name="arrow_forward" size="sm" />
                    </a>
                </div>

                @php
                $services = [
                ['icon' => 'home', 'title' => 'Surat Keterangan Domisili', 'desc' => 'Pernyataan resmi tempat tinggal
                penduduk di wilayah kelurahan.'],
                ['icon' => 'storefront', 'title' => 'SKU (Keterangan Usaha)', 'desc' => 'Surat bukti kepemilikan usaha
                untuk keperluan perbankan/izin.'],
                ['icon' => 'personal_injury', 'title' => 'Surat Kematian', 'desc' => 'Pelaporan peristiwa kematian untuk
                pemutakhiran data kependudukan.'],
                ['icon' => 'local_shipping', 'title' => 'Pindah Datang', 'desc' => 'Pengurusan administrasi perpindahan
                penduduk antar wilayah.'],
                ['icon' => 'receipt_long', 'title' => 'SKTM (Keterangan Tidak Mampu)', 'desc' => 'Dokumen pendukung
                untuk bantuan sosial atau biaya pendidikan.'],
                ['icon' => 'favorite', 'title' => 'Pengantar Nikah', 'desc' => 'Syarat awal administrasi pendaftaran
                pernikahan di KUA/Catatan Sipil.'],
                ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($services as $svc)
                    <x-guest::ui.card variant="bordered" padding="md"
                        class="group hover:border-primary/50 hover:shadow-md transition-all">
                        <div
                            class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4 group-hover:bg-primary group-hover:text-white transition-colors text-primary">
                            <x-guest::ui.icon name="{{ $svc['icon'] }}" />
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $svc['title'] }}</h3>
                        <p class="text-sm text-slate-500 mb-6">{{ $svc['desc'] }}</p>
                        <x-guest::ui.button variant="outline"
                            class="w-full group-hover:bg-primary group-hover:text-white group-hover:border-primary">
                            Buat Sekarang
                        </x-guest::ui.button>
                    </x-guest::ui.card>
                    @endforeach
                </div>
            </div>

            {{-- Sidebar Widgets --}}
            <div class="space-y-6 ">
                {{-- Tracking Widget --}}
                <x-guest::ui.card variant="bordered" padding="lg" class=" top-24">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-primary/10 text-primary rounded-lg flex items-center justify-center">
                            <x-guest::ui.icon name="track_changes" />
                        </div>
                        <h3 class="font-bold text-slate-900">Lacak Status</h3>
                    </div>
                    <p class="text-sm text-slate-500 mb-4">Masukkan ID pengajuan untuk melihat progress dokumen Anda.
                    </p>
                    <div class="space-y-3">
                        <x-guest::ui.input name="tracking_id" label="ID Pengajuan" placeholder="CONTOH: REQ-2023-XXXX" />
                        <x-guest::ui.button class="w-full shadow-lg shadow-primary/20">Cek Status Pengajuan</x-guest::ui.button>
                    </div>

                    <div class="mt-8 pt-8 border-t border-slate-100">
                        <h4 class="text-sm font-bold text-slate-900 mb-4">Mengapa Menggunakan Online?</h4>
                        <ul class="space-y-3">
                            @foreach(['Proses verifikasi lebih cepat', 'Pantau status secara real-time', 'Dokumen
                            digital ber-QR Code'] as $benefit)
                            <li class="flex items-start gap-2 text-sm text-slate-600">
                                <x-guest::ui.icon name="check_circle" size="sm" color="text-primary" class="mt-0.5" />
                                {{ $benefit }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </x-guest::ui.card>

                {{-- Help Banner --}}
                <div
                    class="bg-gradient-to-br from-primary to-blue-700 rounded-xl p-6 text-white overflow-hidden relative group">
                    <div
                        class="absolute -right-4 -bottom-4 opacity-20 transform group-hover:scale-110 transition-transform">
                        <x-guest::ui.icon name="support_agent" class="!text-[8rem]" />
                    </div>
                    <h4 class="text-lg font-bold mb-2 relative z-10">Butuh Bantuan?</h4>
                    <p class="text-sm text-white/80 mb-4 relative z-10">Hubungi petugas pelayanan kami melalui WhatsApp
                        untuk panduan lebih lanjut.</p>
                    <x-guest::ui.button variant="secondary" class="bg-white text-primary hover:bg-slate-100 relative z-10">
                        Chat Sekarang
                    </x-guest::ui.button>
                </div>
            </div>
        </div>

        {{-- HOW-TO GUIDE --}}
        <section class="mt-24">
            <x-guest::ui.section-header title="3 Langkah Mudah" subtitle="Panduan pengajuan surat online tanpa ribet."
                size="lg" />

            @php
            $steps = [
            ['icon' => 'edit_note', 'num' => '1', 'title' => 'Isi Formulir', 'desc' => 'Lengkapi data diri dan unggah
            dokumen pendukung yang diperlukan.'],
            ['icon' => 'verified_user', 'num' => '2', 'title' => 'Verifikasi Data', 'desc' => 'Petugas kelurahan akan
            meninjau dan memvalidasi pengajuan Anda.'],
            ['icon' => 'cloud_download', 'num' => '3', 'title' => 'Unduh Dokumen', 'desc' => 'Setelah disetujui, surat
            dapat langsung diunduh dalam format PDF atau diambil.'],
            ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
                <div class="hidden md:block absolute top-12 left-0 w-full h-0.5 bg-slate-200 z-0"></div>
                @foreach($steps as $step)
                <div class="relative z-10 text-center">
                    <div
                        class="w-24 h-24 bg-white border-4 border-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl ring-8 ring-background-light">
                        <x-guest::ui.icon name="{{ $step['icon'] }}" size="xl" color="text-primary" />
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-2">{{ $step['num'] }}. {{ $step['title'] }}</h4>
                    <p class="text-sm text-slate-500 max-w-xs mx-auto">{{ $step['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </section>
    </main>

</x-guest::layout.app>
