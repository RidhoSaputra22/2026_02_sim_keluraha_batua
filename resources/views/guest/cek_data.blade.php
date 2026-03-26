<x-guest::layout.app title="Cek Data">

    {{-- HERO SECTION --}}
    <x-guest::ui.hero size="xl" background="white">
        <x-slot:title>Layanan Data Kependudukan Digital</x-slot:title>

        <x-slot:subtitle>
            Cek dan perbarui data kependudukan Anda dengan mudah melalui portal layanan digital kami.
        </x-slot:subtitle>
    </x-guest::ui.hero>

    <main class="flex-grow">

        {{-- CEK DATA SECTION --}}
        <section class="py-12 px-4">
            <div class="max-w-7xl mx-auto">
                <x-guest::ui.card variant="bordered" padding="lg">
                    <x-slot:icon>person_search</x-slot:icon>
                    <x-slot:title>Cek Data Warga</x-slot:title>

                    <p class="text-slate-500 mb-6">
                        Masukkan NIK Anda untuk melihat data kependudukan
                    </p>

                    @if (session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                            <x-guest::ui.icon name="error" size="sm" class="inline mr-1" />
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('result'))
                        <div class="mb-6 p-6 bg-green-50 border border-green-200 rounded-lg">
                            <h4 class="font-bold text-green-800 mb-4 flex items-center gap-2">
                                <x-guest::ui.icon name="check_circle" size="sm" color="text-green-600" />
                                Data Ditemukan
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                @php $result = session('result'); @endphp
                                <div><span class="font-semibold text-slate-600">NIK:</span> {{ $result['nik'] }}</div>
                                <div><span class="font-semibold text-slate-600">Nama:</span> {{ $result['nama'] }}</div>
                                <div><span class="font-semibold text-slate-600">Jenis Kelamin:</span> {{ $result['jenis_kelamin'] }}</div>
                                <div><span class="font-semibold text-slate-600">Agama:</span> {{ $result['agama'] ?? '-' }}</div>
                                <div><span class="font-semibold text-slate-600">Status Kawin:</span> {{ $result['status_kawin'] ?? '-' }}</div>
                                <div><span class="font-semibold text-slate-600">RT/RW:</span> {{ $result['rt'] ?? '-' }}/{{ $result['rw'] ?? '-' }}</div>
                                <div class="md:col-span-2"><span class="font-semibold text-slate-600">Alamat:</span> {{ $result['alamat'] ?? '-' }}</div>
                            </div>
                        </div>
                    @endif

                    <form class="space-y-6" method="POST" action="{{ route('guest.cek-data.search') }}">
                        @csrf

                        <x-guest::ui.input name="nik" label="NIK (Nomor Induk Kependudukan)"
                            placeholder="Masukkan 16 Digit NIK Anda" icon="badge" :required="true"
                            :value="old('nik')" :error="$errors->first('nik')" />

                        <p class="text-sm text-slate-400">
                            *Pastikan NIK yang Anda masukkan sesuai dengan KTP atau Kartu Keluarga.
                        </p>

                        <x-slot:footer>
                            <x-guest::ui.button type="submit" size="lg" icon="search" class="w-full md:w-auto">
                                Cek Data
                            </x-guest::ui.button>
                        </x-slot:footer>
                    </form>
                </x-guest::ui.card>
            </div>
        </section>

        {{-- DIVIDER --}}
        <x-guest::ui.divider text="Atau" class="max-w-7xl mx-auto px-4" />

        {{-- FORM INPUT DATA SECTION --}}
        <section class="pb-12 px-4">
            <div class="max-w-7xl mx-auto">
                <x-guest::ui.card variant="bordered" padding="lg">
                    <x-slot:icon>app_registration</x-slot:icon>
                    <x-slot:title>Formulir Input Data Mandiri</x-slot:title>

                    <p class="text-slate-500 mb-8">
                        Lengkapi formulir di bawah ini untuk mengajukan pembaruan data.
                    </p>

                    <form class="space-y-6" method="POST" action="#">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-guest::ui.input name="name" label="Nama Lengkap" placeholder="Sesuai KTP" icon="person"
                                :required="true" />

                            <x-guest::ui.input name="phone" type="tel" label="Nomor HP (WhatsApp)"
                                placeholder="Contoh: 081234567890" icon="phone" :required="true" />
                        </div>

                        <x-guest::ui.select name="category" label="Kategori Data" :options="[
                                ['value' => 'pindah', 'label' => 'Pindah Datang / Domisili'],
                                ['value' => 'lahir', 'label' => 'Laporan Kelahiran'],
                                ['value' => 'mati', 'label' => 'Laporan Kematian'],
                                ['value' => 'alamat', 'label' => 'Perubahan Alamat'],
                                ['value' => 'lainnya', 'label' => 'Lain-lain'],
                            ]" placeholder="Pilih Kategori Pembaruan" :required="true" />

                        <x-guest::ui.textarea name="address" label="Alamat Lengkap"
                            placeholder="Tuliskan alamat domisili saat ini" rows="4" :required="true" />

                        <x-guest::ui.file-upload name="document" label="Unggah Scan KTP / KK" accept=".jpg,.jpeg,.png,.pdf"
                            max-size="2MB" description="Format: JPG, PNG, atau PDF" :required="true" />

                        <x-slot:footer>
                            <div class="flex justify-end gap-4">
                                <x-guest::ui.button variant="outline" type="button">
                                    Reset
                                </x-guest::ui.button>
                                <x-guest::ui.button type="submit" size="lg" icon="send">
                                    Kirim Data Mandiri
                                </x-guest::ui.button>
                            </div>
                        </x-slot:footer>
                    </form>
                </x-guest::ui.card>
            </div>
        </section>

        {{-- INFO SECTION --}}
        <section class="pb-20 px-4">
            <div class="max-w-7xl mx-auto">
                <x-guest::ui.section-header title="Informasi Penting" subtitle="Hal yang perlu Anda ketahui" size="md" />

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <x-guest::ui.card padding="md">
                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-guest::ui.icon name="security" size="lg" color="text-primary" />
                            </div>
                            <h3 class="font-bold mb-2">Keamanan Data</h3>
                            <p class="text-sm text-slate-600">
                                Data Anda aman dan terenkripsi
                            </p>
                        </div>
                    </x-guest::ui.card>

                    <x-guest::ui.card padding="md">
                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-guest::ui.icon name="schedule" size="lg" color="text-primary" />
                            </div>
                            <h3 class="font-bold mb-2">Proses Cepat</h3>
                            <p class="text-sm text-slate-600">
                                Maksimal 3 hari kerja
                            </p>
                        </div>
                    </x-guest::ui.card>

                    <x-guest::ui.card padding="md">
                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-guest::ui.icon name="support_agent" size="lg" color="text-primary" />
                            </div>
                            <h3 class="font-bold mb-2">Dukungan 24/7</h3>
                            <p class="text-sm text-slate-600">
                                Kami siap membantu Anda
                            </p>
                        </div>
                    </x-guest::ui.card>
                </div>
            </div>
        </section>

    </main>

</x-guest::layout.app>
