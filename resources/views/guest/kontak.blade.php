<x-guest::layout.app title="Kontak">
    <x-guest::ui.hero size="lg" background="white">
        <x-slot:title>
            Hubungi <span class="text-primary">{{ $kelurahan?->nama ?? 'Kelurahan' }}</span>
        </x-slot:title>

        <x-slot:subtitle>
            Gunakan halaman ini untuk melihat informasi kontak resmi, jam layanan, dan jalur cepat menuju layanan surat, publikasi, maupun pengaduan warga.
        </x-slot:subtitle>
    </x-guest::ui.hero>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            <section class="lg:col-span-5 space-y-6">
                <x-guest::ui.card variant="bordered" padding="lg">
                    <h3 class="text-xl font-bold text-slate-900 mb-6">Informasi Kontak</h3>

                    <div class="space-y-5">
                        <x-guest::ui.contact-info icon="place" title="Alamat"
                            content="{{ $kelurahan?->alamat_kantor ?: 'Alamat kantor kelurahan belum diisi.' }}" />
                        <x-guest::ui.contact-info icon="call" title="Telepon"
                            content="{{ $kelurahan?->no_telp ?: 'Nomor telepon belum diisi.' }}"
                            link="{{ $kelurahan?->no_telp ? 'tel:' . preg_replace('/\D+/', '', $kelurahan->no_telp) : null }}" />
                        <x-guest::ui.contact-info icon="email" title="Email"
                            content="{{ $kelurahan?->email ?: 'Email belum diisi.' }}"
                            link="{{ $kelurahan?->email ? 'mailto:' . $kelurahan->email : null }}" />
                        <x-guest::ui.contact-info icon="language" title="Website"
                            content="{{ $kelurahan?->website ?: 'Website belum diisi.' }}"
                            link="{{ $kelurahan?->website ?: null }}" />
                        <x-guest::ui.contact-info icon="schedule" title="Jam Layanan"
                            content="Senin - Jumat, pukul 08.00 - 16.00 WITA" />
                    </div>
                </x-guest::ui.card>

                <x-guest::ui.card variant="bordered" padding="lg">
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Butuh Lokasi Kantor?</h3>
                    <p class="text-sm text-slate-500 leading-6">
                        Gunakan alamat resmi di atas saat membuka peta atau aplikasi navigasi favorit Anda.
                    </p>
                </x-guest::ui.card>
            </section>

            <section class="lg:col-span-7 space-y-6">
                <x-guest::ui.card variant="bordered" padding="lg">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6">Akses Layanan Cepat</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('guest.surat-online') }}" class="rounded-2xl border border-slate-200 p-5 hover:border-primary/30 hover:bg-primary/5 transition-colors">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                    <x-guest::ui.icon name="description" />
                                </div>
                                <h4 class="font-bold text-slate-900">Persyaratan Surat</h4>
                            </div>
                            <p class="text-sm text-slate-500">Cek daftar berkas untuk setiap layanan surat sebelum datang ke kantor kelurahan.</p>
                        </a>

                        <a href="{{ route('guest.publikasi') }}" class="rounded-2xl border border-slate-200 p-5 hover:border-primary/30 hover:bg-primary/5 transition-colors">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                    <x-guest::ui.icon name="article" />
                                </div>
                                <h4 class="font-bold text-slate-900">Publikasi</h4>
                            </div>
                            <p class="text-sm text-slate-500">Lihat berita, pengumuman, dan dokumen publik terbaru dari kelurahan.</p>
                        </a>

                        <a href="{{ route('guest.umkm') }}" class="rounded-2xl border border-slate-200 p-5 hover:border-primary/30 hover:bg-primary/5 transition-colors">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                    <x-guest::ui.icon name="storefront" />
                                </div>
                                <h4 class="font-bold text-slate-900">Direktori UMKM</h4>
                            </div>
                            <p class="text-sm text-slate-500">Temukan usaha lokal yang sudah terdata di sistem kelurahan.</p>
                        </a>

                        <a href="{{ route('guest.pengaduan') }}" class="rounded-2xl border border-slate-200 p-5 hover:border-primary/30 hover:bg-primary/5 transition-colors">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                    <x-guest::ui.icon name="campaign" />
                                </div>
                                <h4 class="font-bold text-slate-900">Pengaduan Warga</h4>
                            </div>
                            <p class="text-sm text-slate-500">Kirim laporan atau aspirasi warga melalui formulir online yang tersimpan di sistem.</p>
                        </a>
                    </div>
                </x-guest::ui.card>

                <x-guest::ui.card padding="lg" class="bg-primary ">
                    <h3 class="text-2xl font-bold mb-3">Masih Perlu Bantuan?</h3>
                    <p class=" leading-7 mb-6">
                        Jika Anda belum menemukan informasi yang dicari, silakan hubungi petugas melalui kontak resmi
                        di samping atau gunakan formulir pengaduan agar permintaan Anda masuk ke sistem.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        @if ($kelurahan?->no_telp)
                            <x-guest::ui.button href="tel:{{ preg_replace('/\D+/', '', $kelurahan->no_telp) }}" variant="secondary"
                                class="bg-white text-primary hover:bg-slate-100">
                                Telepon Sekarang
                            </x-guest::ui.button>
                        @endif
                        @if ($kelurahan?->email)
                            <x-guest::ui.button href="mailto:{{ $kelurahan->email }}" variant="outline"
                                class="border-white/30 text-white hover:bg-white/10">
                                Kirim Email
                            </x-guest::ui.button>
                        @endif
                    </div>
                </x-guest::ui.card>
            </section>
        </div>
    </main>
</x-guest::layout.app>
