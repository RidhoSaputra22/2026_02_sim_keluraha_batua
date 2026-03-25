<x-guest::layout.app title="Beranda">

    {{-- HERO SECTION --}}
    <section class="relative h-[600px] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0">
            <img alt="Kantor Kelurahan Modern" class="w-full h-full object-cover"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBUC9Hwtv_EXSKYSAlBT4bHA7FQMlbje-rVBN6IoULW4hXdJtYK4wRVoGDkxXvj0EqHiuMEryN7T73OHY51E6Ohpos84Sb6PKodwGkqDigsKsRIJlb3jpiNKh3EWK7MANm5wN-dRDQuhwip0xvSAN7HX8i5QC34rVmFRc0QLlk0ct07U4oKQvd2TwctczmS9aRP1aMYOecH0BF9BXwZ8esnksvNjv1cE5-y3HjaDCJjvoej0k93Ezz1Wa4wivGeFgdwrWdIB-S6bhp-" />
            <div class="absolute inset-0 bg-gradient-to-r from-primary/90 to-primary/40"></div>
        </div>
        <div class="relative z-10 max-w-4xl px-4 text-center text-white">
            <h1 class="text-5xl font-extrabold mb-6 leading-tight">
                Selamat Datang di Website Resmi <br />
                <span class="text-yellow-300">Kelurahan Batua Raya</span>
            </h1>
            <p class="text-xl mb-10 opacity-90 font-light max-w-2xl mx-auto">
                Akses informasi publik, layanan administrasi, dan kabar terkini lingkungan Kelurahan secara cepat,
                transparan, dan akuntabel.
            </p>

            <x-guest::ui.search-bar placeholder="Cari layanan, berita, atau informasi publik..." button-text="CARI" action="#">
                <div class="mt-4 flex flex-wrap justify-center gap-2 text-sm">
                    <span class="opacity-80">Populer:</span>
                    <a class="bg-white/20 hover:bg-white/30 backdrop-blur-md px-3 py-1 rounded-full" href="#">Cek
                        KTP</a>
                    <a class="bg-white/20 hover:bg-white/30 backdrop-blur-md px-3 py-1 rounded-full" href="#">Izin
                        Usaha</a>
                    <a class="bg-white/20 hover:bg-white/30 backdrop-blur-md px-3 py-1 rounded-full" href="#">Bantuan
                        Sosial</a>
                </div>
            </x-guest::ui.search-bar>
        </div>
    </section>

    {{-- LAYANAN UNGGULAN --}}
    <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-guest::ui.section-header title="Layanan Unggulan"
            subtitle="Pilih jenis layanan masyarakat yang Anda butuhkan di bawah ini." size="md" />

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <x-guest::ui.card variant="bordered" padding="lg"
                class="group hover:shadow-xl hover:-translate-y-2 transition-all cursor-pointer">
                <div
                    class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                    <x-guest::ui.icon name="badge" size="lg" color="text-primary group-hover:text-white" />
                </div>
                <h3 class="text-xl font-bold mb-3 text-slate-900">Adm. Kependudukan</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-6">Pengurusan KTP, KK, Akta Kelahiran, dan Surat
                    Pindah domisili.</p>
                <div class="flex items-center text-primary font-bold text-sm">
                    LIHAT DETAIL
                    <x-guest::ui.icon name="arrow_forward" size="sm" class="ml-2" />
                </div>
            </x-guest::ui.card>

            <x-guest::ui.card variant="bordered" padding="lg"
                class="group hover:shadow-xl hover:-translate-y-2 transition-all cursor-pointer">
                <div
                    class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                    <x-guest::ui.icon name="store" size="lg" color="text-primary group-hover:text-white" />
                </div>
                <h3 class="text-xl font-bold mb-3 text-slate-900">Perizinan Usaha</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-6">Surat Izin Usaha Mikro (IUMK) dan rekomendasi
                    usaha lainnya.</p>
                <div class="flex items-center text-primary font-bold text-sm">
                    LIHAT DETAIL
                    <x-guest::ui.icon name="arrow_forward" size="sm" class="ml-2" />
                </div>
            </x-guest::ui.card>

            <x-guest::ui.card variant="bordered" padding="lg"
                class="group hover:shadow-xl hover:-translate-y-2 transition-all cursor-pointer">
                <div
                    class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                    <x-guest::ui.icon name="volunteer_activism" size="lg" color="text-primary group-hover:text-white" />
                </div>
                <h3 class="text-xl font-bold mb-3 text-slate-900">Bantuan Sosial</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-6">Informasi dan pendaftaran bantuan sosial, BPJS
                    PBI, dan BLT.</p>
                <div class="flex items-center text-primary font-bold text-sm">
                    LIHAT DETAIL
                    <x-guest::ui.icon name="arrow_forward" size="sm" class="ml-2" />
                </div>
            </x-guest::ui.card>

            <x-guest::ui.card variant="bordered" padding="lg"
                class="group hover:shadow-xl hover:-translate-y-2 transition-all cursor-pointer">
                <div
                    class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary transition-colors">
                    <x-guest::ui.icon name="report_problem" size="lg" color="text-primary group-hover:text-white" />
                </div>
                <h3 class="text-xl font-bold mb-3 text-slate-900">Pengaduan</h3>
                <p class="text-slate-500 text-sm leading-relaxed mb-6">Sampaikan keluhan dan laporan warga terkait
                    fasilitas umum atau keamanan.</p>
                <div class="flex items-center text-primary font-bold text-sm">
                    LIHAT DETAIL
                    <x-guest::ui.icon name="arrow_forward" size="sm" class="ml-2" />
                </div>
            </x-guest::ui.card>
        </div>
    </section>

    {{-- BERITA & KEGIATAN --}}
    <section class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-2">Berita &amp; Kegiatan</h2>
                    <p class="text-slate-600">Update terkini informasi dan aktivitas di lingkungan kelurahan.</p>
                </div>
                <a class="text-primary font-bold flex items-center hover:underline" href="#">
                    LIHAT SEMUA BERITA
                    <x-guest::ui.icon name="chevron_right" class="ml-1" />
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- News 1 --}}
                <x-guest::ui.card padding="sm" class="overflow-hidden hover:shadow-md transition-shadow">
                    <div class="relative h-48 overflow-hidden -mx-4 -mt-4 mb-4">
                        <img alt="Kegiatan Kerja Bakti"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuA-NUVdgS_8zMu_zJMe_E3NoeYBacdzhI8S0HwhsFC4cUGe6_YlReuP2SJZ9Jb5BlCJE6Xx48q-SBPKvn23-cuo38jEjeNEKGZFB3pglpMWQjgjyx_B7hKTLuE3N0N3BxP4OdEtpiYo5NxTs0Hj_PcGljtS1_VUD3Hn6YIg5C_H03im0O9iyOPInVPNwgfUMycxxwp8BTzww9yIfRU_FuoFV1gzWkrqrm9cBcGlDCYAoSY2J5NWDaVbcTq9iIzyYtnLhcpbNVCFNMgZ" />
                        <div class="absolute top-4 left-4">
                            <x-guest::ui.badge variant="primary" size="sm">Kegiatan</x-guest::ui.badge>
                        </div>
                    </div>
                    <div class="text-xs text-slate-400 mb-2 flex items-center">
                        <x-guest::ui.icon name="calendar_today" size="sm" class="mr-1" /> 15 Oktober 2023
                    </div>
                    <h4 class="text-lg font-bold mb-3 line-clamp-2 hover:text-primary transition-colors cursor-pointer">
                        Warga Kompak Melaksanakan Kerja Bakti Rutin di RW 04
                    </h4>
                    <p class="text-slate-500 text-sm line-clamp-3 mb-4">Dalam rangka menjaga kebersihan lingkungan dan
                        mencegah banjir di musim hujan, warga RW 04 bersama petugas PPSU mengadakan pembersihan saluran
                        air...</p>
                    <a class="text-primary font-semibold text-sm" href="#">Baca Selengkapnya</a>
                </x-guest::ui.card>

                {{-- News 2 --}}
                <x-guest::ui.card padding="sm" class="overflow-hidden hover:shadow-md transition-shadow">
                    <div class="relative h-48 overflow-hidden -mx-4 -mt-4 mb-4">
                        <img alt="Penyuluhan Kesehatan"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCxskVVt7SRXf3PMk4RBfatV8z64o2xNKV2zUoFVIDxq-547LDhg0MIc_CCfoZZMm4c9P-Ws9IS0rBvfhGJOMr6gIJznFVBQC8VmlAd7oiMu0GKkqYpdKebgUbUcorfyHdSdMClH7W-J8CZuOLPFGEs77609xcV_p_M7aV6sd55CwjHB0URupCTMfl1MalwdrZpXSq-Q2Vy_98oc7wCyqBYYN-dVPCNVSad3fUF1Z1e6hd_OU1WVbVcegDvU2_7iBacOE2Or3d7ev59" />
                        <div class="absolute top-4 left-4">
                            <x-guest::ui.badge variant="success" size="sm">Kesehatan</x-guest::ui.badge>
                        </div>
                    </div>
                    <div class="text-xs text-slate-400 mb-2 flex items-center">
                        <x-guest::ui.icon name="calendar_today" size="sm" class="mr-1" /> 12 Oktober 2023
                    </div>
                    <h4 class="text-lg font-bold mb-3 line-clamp-2 hover:text-primary transition-colors cursor-pointer">
                        Penyuluhan Pencegahan Stunting Bagi Ibu Hamil di Posyandu Melati
                    </h4>
                    <p class="text-slate-500 text-sm line-clamp-3 mb-4">Puskesmas setempat berkolaborasi dengan pihak
                        kelurahan mengadakan penyuluhan gizi seimbang bagi ibu hamil sebagai upaya menekan angka
                        stunting...</p>
                    <a class="text-primary font-semibold text-sm" href="#">Baca Selengkapnya</a>
                </x-guest::ui.card>

                {{-- News 3 --}}
                <x-guest::ui.card padding="sm" class="overflow-hidden hover:shadow-md transition-shadow">
                    <div class="relative h-48 overflow-hidden -mx-4 -mt-4 mb-4">
                        <img alt="Rapat Desa"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDjKd9JhndPM2mtHfy84OjkN1y30q36GGARbCtor4w0o8apDpcEZU7IXrTRka07lsTF3WxU92uw7cdlqlU9zmWb73ijlo21Ue3GU7jACZkOhX5kFjlvwCsUhn9h8X9IiDDyllQwqJW3cq0xgptmbfbgWHgnSKmMZuNugFOT6uW3dWKQqF0t54xfS44A0-5XBzuntQdjkeVTajjQ2akjRu1wWZ-dvgf_Q-NJtzttIjxLhpFNDi-_O9gq6sck5InHL2NwaFiaCKJgUkIS" />
                        <div class="absolute top-4 left-4">
                            <x-guest::ui.badge variant="warning" size="sm">Pengumuman</x-guest::ui.badge>
                        </div>
                    </div>
                    <div class="text-xs text-slate-400 mb-2 flex items-center">
                        <x-guest::ui.icon name="calendar_today" size="sm" class="mr-1" /> 10 Oktober 2023
                    </div>
                    <h4 class="text-lg font-bold mb-3 line-clamp-2 hover:text-primary transition-colors cursor-pointer">
                        Penyaluran Bantuan Langsung Tunai (BLT) Tahap II Telah Selesai
                    </h4>
                    <p class="text-slate-500 text-sm line-clamp-3 mb-4">Bantuan tunai dari pemerintah pusat telah
                        berhasil disalurkan kepada 450 Keluarga Penerima Manfaat (KPM) yang terdata di sistem
                        kependudukan...</p>
                    <a class="text-primary font-semibold text-sm" href="#">Baca Selengkapnya</a>
                </x-guest::ui.card>
            </div>
        </div>
    </section>

    {{-- SAMBUTAN LURAH --}}
    <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-primary rounded-3xl overflow-hidden flex flex-col lg:flex-row items-stretch">
            <div class="lg:w-1/3 relative min-h-[400px]">
                <img alt="Foto Lurah" class="absolute inset-0 w-full h-full object-cover object-top"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDR0YJK7Jp0NyKOuae75fIbgc0pVz3fVmLmzSg6BBLe1YwvDkqqjEPsFrXgetRBKfz44xac94CzQuUBX7Tuhhg5k0_sYZiUClBwo6t1oShbt93_qf-g5xYviuW08YjIeKO8sYBjRYZN5RisdmC6COHKkPuFDK3gEWK-D1IrqZY9rLdpgT5lfnqtCtg_fej3_Fd8ja-HWZ_RvMKSZo_sv8Zoe_TqZQBz3ODPCWVn2O90UbZgHlifQrajeykOgldlDZBIEyFIasSmUYcq" />
                <div
                    class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-primary/80 to-transparent p-8 lg:hidden">
                    <h3 class="text-white text-2xl font-bold">Drs. H. Ahmad Fauzi, M.Si</h3>
                    <p class="text-yellow-300 font-medium">Lurah Terpilih</p>
                </div>
            </div>
            <div class="lg:w-2/3 p-8 lg:p-16 flex flex-col justify-center">
                <div class="hidden lg:block mb-8">
                    <h3 class="text-white text-4xl font-extrabold mb-2">Sambutan Lurah</h3>
                    <div class="h-1.5 w-20 bg-yellow-300 rounded-full"></div>
                </div>
                <blockquote class="text-white/90 text-xl italic leading-relaxed mb-8">
                    "{{ $kelurahan?->visi ?? 'Visi kami adalah mewujudkan Kelurahan yang mandiri, sejahtera, dan melayani dengan sepenuh hati melalui digitalisasi birokrasi dan pemberdayaan masyarakat yang inklusif.' }}"
                </blockquote>
                <div class="text-white mb-10">
                    <h4 class="text-2xl font-bold">{{ $kelurahan?->nama_lurah ?? 'Kepala Kelurahan' }}</h4>
                    <p class="text-yellow-300 font-medium uppercase tracking-wider text-sm">Kepala Kelurahan {{ $kelurahan?->nip_lurah ? '- NIP. ' . $kelurahan->nip_lurah : '' }}</p>
                </div>
                <div class="flex flex-wrap gap-4">
                    <x-guest::ui.button variant="secondary" size="lg" icon="person_search"
                        class="bg-white text-primary hover:bg-slate-100 shadow-none">
                        PROFIL LENGKAP
                    </x-guest::ui.button>
                    <x-guest::ui.button variant="outline" size="lg" class="border-white/30 text-white hover:bg-white/10">
                        VISI &amp; MISI
                    </x-guest::ui.button>
                </div>
            </div>
        </div>
    </section>
</x-guest::layout.app>
