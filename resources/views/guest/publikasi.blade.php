<x-guest::layout.app title="Publikasi">

    {{-- HERO SECTION --}}
    <x-guest::ui.hero size="lg" background="white">
        <x-slot:title>
            Pusat Publikasi &amp; Informasi Kelurahan
        </x-slot:title>

        <x-slot:subtitle>
            Akses berita terbaru, pengumuman resmi, dan dokumentasi kegiatan warga dalam satu platform yang transparan
            dan akuntabel.
        </x-slot:subtitle>

        <x-slot:actions>
            <x-guest::ui.search-bar placeholder="Cari berita, pengumuman, atau artikel kegiatan..." button-text="CARI"
                action="#" />
        </x-slot:actions>
    </x-guest::ui.hero>

    {{-- MAIN CONTENT --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8">
                @php
                $categories = [
                ['icon' => 'newspaper', 'icon_type' => 'outlined', 'color' => 'red', 'title' => 'Berita Terkini',
                'desc' => 'Update terbaru mengenai perkembangan pembangunan, kebijakan lokal, dan peristiwa penting di
                lingkungan kelurahan.'],
                ['icon' => 'campaign', 'icon_type' => 'outlined', 'color' => 'red', 'title' => 'Pengumuman', 'desc'
                => 'Informasi resmi dari pemerintah kelurahan terkait administrasi, jadwal layanan, dan pemberitahuan
                mendesak.'],
                ['icon' => 'calendar_month', 'icon_type' => 'outlined', 'color' => 'red', 'title' => 'Agenda
                Kegiatan', 'desc' => 'Jadwal acara kemasyarakatan, rapat warga, kegiatan posyandu, hingga perayaan hari
                besar di wilayah kita.'],
                ['icon' => 'photo_library', 'icon_type' => 'outlined', 'color' => 'red', 'title' => 'Galeri Foto',
                'desc' => 'Dokumentasi visual momen-momen kebersamaan dan kemajuan infrastruktur fisik di Kelurahan Maju
                Jaya.'],
                ['icon' => 'analytics', 'icon_type' => 'outlined', 'color' => 'red', 'title' => 'Laporan
                Transparansi', 'desc' => 'Publikasi data statistik kependudukan, realisasi anggaran, dan laporan
                akuntabilitas kinerja pemerintah.'],
                ['icon' => 'folder_zip', 'icon_type' => 'outlined', 'color' => 'red', 'title' => 'Pusat Unduhan',
                'desc' => 'Unduh dokumen publik, formulir layanan, Peraturan Kelurahan, hingga infografis warta
                resmi.'],
                ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($categories as $cat)
                    <x-guest::ui.card variant="bordered" padding="lg"
                        class="group flex flex-col h-full hover:shadow-md transition-all">
                        <div
                            class="w-14 h-14 bg-{{ $cat['color'] }}-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-{{ $cat['color'] }}-600 transition-colors">
                            <x-guest::ui.icon :name="$cat['icon']" size="lg" color="text-{{ $cat['color'] }}-600 " />
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $cat['title'] }}</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6 flex-grow">{{ $cat['desc'] }}</p>
                        <x-guest::ui.button size="sm">Lihat Selengkapnya</x-guest::ui.button>
                    </x-guest::ui.card>
                    @endforeach
                </div>
            </div>

            {{-- SIDEBAR --}}
            <div class="lg:col-span-4">
                {{-- Agenda Widget --}}
                <x-guest::ui.card variant="bordered" padding="none" class="overflow-hidden mb-8">
                    <div class="bg-slate-50/50 p-6 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                            <x-guest::ui.icon name="event_upcoming" color="text-primary" />
                            Agenda Mendatang
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">Status dan jadwal kegiatan terkini</p>
                    </div>
                    <div class="p-6 space-y-6">
                        @php
                        $events = [
                        ['time' => 'Besok • 09:00 WIB', 'title' => 'Posyandu Balita RW 04', 'location' => 'Lokasi: Balai
                        RW 04, Jl. Kenanga', 'active' => true],
                        ['time' => '28 Okt • 07:30 WIB', 'title' => 'Kerja Bakti Lingkungan', 'location' => 'Lokasi:
                        Seluruh Wilayah Kelurahan', 'active' => false],
                        ['time' => '30 Okt • 19:30 WIB', 'title' => 'Rapat Persiapan HUT', 'location' => 'Lokasi: Aula
                        Kantor Kelurahan', 'active' => false],
                        ];
                        @endphp

                        @foreach($events as $i => $event)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div
                                    class="w-3 h-3 rounded-full {{ $event['active'] ? 'bg-primary ring-4 ring-primary/10' : 'bg-slate-200' }} mt-1.5">
                                </div>
                                @if(!$loop->last)
                                <div class="w-0.5 h-full bg-slate-100 my-1"></div>
                                @endif
                            </div>
                            <div class="pb-2">
                                <span
                                    class="text-[10px] font-bold {{ $event['active'] ? 'text-primary' : 'text-slate-400' }} uppercase tracking-widest block mb-1">{{ $event['time'] }}</span>
                                <h4 class="text-sm font-bold text-slate-900 leading-snug">{{ $event['title'] }}</h4>
                                <p class="text-xs text-slate-500 mt-1">{{ $event['location'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="p-6 pt-0">
                        <x-guest::ui.button variant="outline" class="w-full" icon="calendar_today" icon-position="left">
                            Lihat Kalender Lengkap
                        </x-guest::ui.button>
                    </div>
                </x-guest::ui.card>

                {{-- Newsletter --}}
                <div class="bg-primary rounded-2xl p-8 text-white relative overflow-hidden shadow-sm">
                    <div class="relative z-10">
                        <h3 class="text-xl font-bold mb-2">Langganan Warta</h3>
                        <p class="text-white/70 text-sm mb-6">Dapatkan berita dan pengumuman terbaru langsung di email
                            Anda.</p>
                        <input
                            class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-sm placeholder-white/50 text-white mb-3 focus:ring-white outline-none"
                            placeholder="Alamat Email" type="email" />
                        <x-guest::ui.button variant="secondary"
                            class="w-full bg-white text-primary hover:bg-white/90 font-bold" icon="mail_outline"
                            icon-position="left">
                            BERLANGGANAN
                        </x-guest::ui.button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- DOCUMENT TABLE SECTION --}}
    <section class="bg-white py-20 border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-slate-900 flex items-center gap-3">
                        <x-guest::ui.icon name="article" color="text-primary" />
                        Rilis Dokumen Terbaru
                    </h3>
                    <p class="text-slate-500 mt-2">Daftar publikasi dan dokumen resmi yang baru dirilis.</p>
                </div>
                <a class="text-primary font-bold text-sm uppercase tracking-wider hover:underline" href="#">Lihat Semua
                    Dokumen</a>
            </div>

            <x-guest::ui.card variant="bordered" padding="none" class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Nama
                                    Dokumen</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">
                                    Kategori</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Tgl
                                    Terbit</th>
                                <th
                                    class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                                            <x-guest::ui.icon name="picture_as_pdf" color="text-red-500" />
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900">Laporan Akuntabilitas Kinerja 2023.pdf
                                            </p>
                                            <p class="text-xs text-slate-400">Ukuran: 3.4 MB</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <x-guest::ui.badge variant="success" size="sm">Transparansi</x-guest::ui.badge>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-500">22 Okt 2023</td>
                                <td class="px-6 py-5 text-right">
                                    <x-guest::ui.button variant="ghost" size="sm">Unduh</x-guest::ui.button>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                            <x-guest::ui.icon name="description" color="text-blue-500" />
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900">Surat Edaran Keamanan Lingkungan.pdf</p>
                                            <p class="text-xs text-slate-400">Ukuran: 850 KB</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <x-guest::ui.badge variant="warning" size="sm">Pengumuman</x-guest::ui.badge>
                                </td>
                                <td class="px-6 py-5 text-sm text-slate-500">18 Okt 2023</td>
                                <td class="px-6 py-5 text-right">
                                    <x-guest::ui.button variant="ghost" size="sm">Unduh</x-guest::ui.button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-guest::ui.card>
        </div>
    </section>

</x-guest::layout.app>
