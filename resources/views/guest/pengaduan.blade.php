<x-guest::layout.app title="Pengaduan">

    {{-- HERO SECTION --}}
    <x-guest::ui.hero size="xl" background="gradient">
        <x-slot:badge>
            <x-guest::ui.badge variant="primary">Layanan Aspirasi Masyarakat</x-guest::ui.badge>
        </x-slot:badge>

        <x-slot:title>
            Sampaikan Aspirasi &amp; <span class="text-primary">Keluhan Anda</span>
        </x-slot:title>

        <x-slot:subtitle>
            Mewujudkan transparansi dan pelayanan publik yang lebih baik. Laporan Anda akan ditindaklanjuti secara profesional oleh petugas Kelurahan kami.
        </x-slot:subtitle>

        <x-slot:actions>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <x-guest::ui.button size="lg" icon="edit_note" icon-position="left" href="#lapor" class="shadow-xl shadow-primary/25">
                    Ajukan Pengaduan Sekarang
                </x-guest::ui.button>
                <x-guest::ui.button variant="outline" size="lg" href="#panduan">
                    Lihat Panduan
                </x-guest::ui.button>
            </div>
        </x-slot:actions>
    </x-guest::ui.hero>

    {{-- INSTRUCTION SECTION (Panduan) --}}
    <section class="py-20 bg-white" id="panduan">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-guest::ui.section-header
                title="Panduan Pengaduan"
                size="lg"
            />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @php
                    $steps = [
                        ['icon' => 'assignment', 'title' => 'Tulis Laporan', 'desc' => 'Laporkan keluhan atau aspirasi anda dengan jelas, lengkap dengan judul dan deskripsi detail.'],
                        ['icon' => 'add_a_photo', 'title' => 'Lampirkan Bukti', 'desc' => 'Sertakan foto atau dokumen pendukung untuk mempercepat proses verifikasi di lapangan.'],
                        ['icon' => 'query_stats', 'title' => 'Pantau Progress', 'desc' => 'Cek status tindak lanjut laporan secara real-time melalui kode unik yang Anda dapatkan.'],
                    ];
                @endphp

                @foreach($steps as $step)
                <x-guest::ui.card padding="lg" class="group bg-background-light border-transparent hover:border-primary/20 transition-all">
                    <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6 group-hover:bg-primary group-hover:text-white transition-all">
                        <x-guest::ui.icon name="{{ $step['icon'] }}" size="lg" />
                    </div>
                    <h3 class="text-xl font-bold mb-3">{{ $step['title'] }}</h3>
                    <p class="text-slate-600">{{ $step['desc'] }}</p>
                </x-guest::ui.card>
                @endforeach
            </div>
        </div>
    </section>

    {{-- COMPLAINT FORM SECTION --}}
    <section class="py-20" id="lapor">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-guest::ui.card variant="elevated" padding="none" class="overflow-hidden">
                <div class="bg-primary p-8 text-white">
                    <h2 class="text-2xl font-bold mb-2">Formulir Pengaduan</h2>
                    <p class="text-white/80">Silakan isi formulir di bawah ini dengan informasi yang akurat.</p>
                </div>

                <form class="p-8 lg:p-12 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-guest::ui.input
                            name="subjek"
                            label="Subjek Laporan"
                            placeholder="Contoh: Lampu Jalan Padam"
                            :required="true"
                        />
                        <x-guest::ui.select
                            name="kategori"
                            label="Kategori"
                            :options="[
                                ['value' => 'infrastruktur', 'label' => 'Infrastruktur'],
                                ['value' => 'keamanan', 'label' => 'Keamanan & Ketertiban'],
                                ['value' => 'kebersihan', 'label' => 'Kebersihan Lingkungan'],
                                ['value' => 'layanan', 'label' => 'Layanan Administrasi'],
                                ['value' => 'sosial', 'label' => 'Masalah Sosial'],
                            ]"
                            placeholder="Pilih Kategori"
                            :required="true"
                        />
                    </div>

                    <x-guest::ui.textarea
                        name="isi_laporan"
                        label="Isi Laporan"
                        placeholder="Jelaskan detail keluhan, lokasi kejadian, dan informasi penting lainnya..."
                        rows="5"
                        :required="true"
                    />

                    <x-guest::ui.file-upload
                        name="lampiran"
                        label="Lampiran Bukti (Foto/File)"
                        accept=".jpg,.png,.pdf"
                        max-size="5MB"
                        description="Format: JPG, PNG, PDF (Maks. 5MB)"
                    />

                    <div class="flex items-start gap-3 p-4 bg-primary/5 rounded-xl border border-primary/10">
                        <x-guest::ui.icon name="info" color="text-primary" />
                        <p class="text-xs text-slate-600">Dengan mengirimkan laporan ini, Anda setuju bahwa data yang diberikan adalah benar dan bersedia untuk dihubungi oleh petugas untuk verifikasi lebih lanjut.</p>
                    </div>

                    <div class="pt-4">
                        <x-guest::ui.button type="submit" size="lg" class="w-full shadow-xl shadow-primary/20">
                            Kirim Laporan
                        </x-guest::ui.button>
                    </div>
                </form>
            </x-guest::ui.card>
        </div>
    </section>

</x-guest::layout.app>
