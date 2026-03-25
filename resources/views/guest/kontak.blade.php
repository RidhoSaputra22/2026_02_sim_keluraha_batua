<x-guest::layout.app title="Kontak">

    {{-- HERO SECTION --}}
    <x-guest::ui.hero size="lg" background="white">
        <x-slot:title>
            Hubungi <span class="text-primary">Kami</span>
        </x-slot:title>

        <x-slot:subtitle>
            Punya pertanyaan atau ingin memberikan saran untuk kemajuan Kelurahan kita? Kami siap mendengarkan aspirasi dan melayani kebutuhan administrasi Anda.
        </x-slot:subtitle>
    </x-guest::ui.hero>

    {{-- MAIN CONTENT --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

            {{-- LEFT COLUMN: Contact Info --}}
            <section class="lg:col-span-5 space-y-10">
                <div>
                    <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <span class="w-8 h-1 bg-primary rounded-full"></span>
                        Informasi Kontak
                    </h3>

                    <div class="bg-white p-1 rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                        <div class="aspect-video bg-slate-100 rounded-lg flex items-center justify-center">
                            <x-guest::ui.icon name="map" size="xl" color="text-slate-400" />
                        </div>
                    </div>

                    <div class="space-y-6">
                        <x-guest::ui.contact-info
                            icon="place"
                            title="Alamat"
                            content="Jl. Merdeka No. 123, Pusat Kota, Jakarta 12345"
                        />

                        <x-guest::ui.contact-info
                            icon="call"
                            title="Telepon"
                            content="(021) 1234-5678"
                            link="tel:02112345678"
                        />

                        <x-guest::ui.contact-info
                            icon="email"
                            title="Email"
                            content="info@kelurahan-digital.go.id"
                            link="mailto:info@kelurahan-digital.go.id"
                        />

                        <x-guest::ui.contact-info
                            icon="schedule"
                            title="Jam Operasional"
                            content="Senin - Jumat: 08:00 - 16:00 WIB"
                        />
                    </div>
                </div>

                {{-- Social Media --}}
                <div>
                    <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <span class="w-8 h-1 bg-primary rounded-full"></span>
                        Ikuti Kami
                    </h3>

                    <div class="flex gap-4">
                        <a href="#" class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-primary hover:text-white transition-all hover:scale-110">
                            <x-guest::ui.icon name="facebook" />
                        </a>
                        <a href="#" class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-primary hover:text-white transition-all hover:scale-110">
                            <x-guest::ui.icon name="language" />
                        </a>
                        <a href="#" class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-primary hover:text-white transition-all hover:scale-110">
                            <x-guest::ui.icon name="camera_alt" />
                        </a>
                    </div>
                </div>
            </section>

            {{-- RIGHT COLUMN: Contact Form --}}
            <section class="lg:col-span-7">
                <x-guest::ui.card variant="bordered" padding="lg">
                    <x-slot:icon>send</x-slot:icon>
                    <x-slot:title>Kirim Pesan</x-slot:title>

                    <p class="text-slate-600 mb-8">
                        Silakan isi formulir di bawah ini untuk menghubungi kami. Kami akan merespons pesan Anda secepatnya.
                    </p>

                    <form class="space-y-6" method="POST" action="#">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-guest::ui.input
                                name="name"
                                label="Nama Lengkap"
                                placeholder="Masukkan nama lengkap Anda"
                                icon="person"
                                :required="true"
                            />

                            <x-guest::ui.input
                                name="email"
                                type="email"
                                label="Email"
                                placeholder="email@example.com"
                                icon="email"
                                :required="true"
                            />
                        </div>

                        <x-guest::ui.input
                            name="phone"
                            type="tel"
                            label="Nomor Telepon (Opsional)"
                            placeholder="081234567890"
                            icon="phone"
                        />

                        <x-guest::ui.select
                            name="subject"
                            label="Subjek Pesan"
                            :options="[
                                ['value' => 'pengaduan', 'label' => 'Pengaduan'],
                                ['value' => 'saran', 'label' => 'Saran & Kritik'],
                                ['value' => 'informasi', 'label' => 'Permintaan Informasi'],
                                ['value' => 'lainnya', 'label' => 'Lainnya'],
                            ]"
                            placeholder="Pilih subjek pesan"
                            :required="true"
                        />

                        <x-guest::ui.textarea
                            name="message"
                            label="Pesan"
                            placeholder="Tuliskan pesan Anda di sini..."
                            rows="6"
                            :required="true"
                        />

                        <x-slot:footer>
                            <div class="flex justify-end">
                                <x-guest::ui.button type="submit" size="lg" icon="send">
                                    Kirim Pesan
                                </x-guest::ui.button>
                            </div>
                        </x-slot:footer>
                    </form>
                </x-guest::ui.card>
            </section>

        </div>
    </main>

</x-guest::layout.app>
