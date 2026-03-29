<x-guest::layout.app title="Pengaduan">
    <x-guest::ui.hero size="xl" background="gradient">


        <x-slot:title>
            Sampaikan Aspirasi & <span class="text-primary">Keluhan Warga</span>
        </x-slot:title>

        <x-slot:subtitle>
            Formulir ini tersimpan ke sistem agar admin bisa menindaklanjuti laporan warga langsung dari panel website publik.
        </x-slot:subtitle>

        <x-slot:actions>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <x-guest::ui.button href="#lapor" size="lg" icon="edit_note" icon-position="left">
                    Kirim Pengaduan
                </x-guest::ui.button>
                <x-guest::ui.button href="#panduan" variant="outline" size="lg">
                    Lihat Panduan
                </x-guest::ui.button>
            </div>
        </x-slot:actions>
    </x-guest::ui.hero>

    <section class="py-20 bg-white" id="panduan">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-guest::ui.section-header title="Panduan Pengaduan"
                subtitle="Isi laporan dengan detail yang jelas agar petugas lebih mudah melakukan verifikasi." size="lg" />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ([
                    ['icon' => 'assignment', 'title' => 'Tulis Laporan', 'desc' => 'Isi subjek, kategori, lokasi, dan kronologi secara ringkas namun jelas.'],
                    ['icon' => 'add_a_photo', 'title' => 'Lampirkan Bukti', 'desc' => 'Unggah foto atau dokumen pendukung bila tersedia untuk mempercepat tindak lanjut.'],
                    ['icon' => 'query_stats', 'title' => 'Simpan Kode Aduan', 'desc' => 'Setelah berhasil dikirim, simpan kode pengaduan yang tampil di layar.'],
                ] as $step)
                    <x-guest::ui.card padding="lg" class="bg-background-light border border-primary/10">
                        <div class="w-14 h-14 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-5">
                            <x-guest::ui.icon name="{{ $step['icon'] }}" size="lg" />
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $step['title'] }}</h3>
                        <p class="text-slate-600">{{ $step['desc'] }}</p>
                    </x-guest::ui.card>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-20" id="lapor">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-5">
                    <p class="font-semibold text-green-800">{{ session('success') }}</p>
                    @if (session('kode_pengaduan'))
                        <p class="text-sm text-green-700 mt-2">
                            Kode pengaduan Anda:
                            <span class="font-mono font-bold">{{ session('kode_pengaduan') }}</span>
                        </p>
                    @endif
                </div>
            @endif

            <x-guest::ui.card variant="elevated" padding="none" class="overflow-hidden">
                <div class="bg-primary p-8 text-white">
                    <h2 class="text-2xl font-bold mb-2">Formulir Pengaduan</h2>
                    <p class="text-white/80">Masukkan data dengan akurat agar laporan bisa diverifikasi lebih cepat.</p>
                </div>

                <form method="POST" action="{{ route('guest.pengaduan.store') }}" enctype="multipart/form-data"
                    class="p-8 lg:p-10 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-guest::ui.input name="nama" label="Nama Lengkap" placeholder="Nama pelapor"
                            value="{{ old('nama') }}" :required="true" :error="$errors->first('nama')" />
                        <x-guest::ui.input name="no_hp" label="Nomor HP / WhatsApp" placeholder="08xxxxxxxxxx"
                            value="{{ old('no_hp') }}" :required="true" :error="$errors->first('no_hp')" />
                        <x-guest::ui.input name="email" type="email" label="Email (Opsional)" placeholder="email@contoh.com"
                            value="{{ old('email') }}" :error="$errors->first('email')" />
                        <x-guest::ui.select name="kategori" label="Kategori"
                            :options="collect(\App\Models\PengaduanWarga::kategoriOptions())->map(fn($label, $value) => ['value' => $value, 'label' => $label])->values()->all()"
                            selected="{{ old('kategori') }}" placeholder="Pilih kategori" :required="true"
                            :error="$errors->first('kategori')" />
                    </div>

                    <x-guest::ui.input name="subjek" label="Subjek Laporan" placeholder="Contoh: Lampu jalan padam di gang 3"
                        value="{{ old('subjek') }}" :required="true" :error="$errors->first('subjek')" />

                    <x-guest::ui.input name="lokasi" label="Lokasi Kejadian" placeholder="Alamat atau patokan lokasi"
                        value="{{ old('lokasi') }}" :error="$errors->first('lokasi')" />

                    <x-guest::ui.textarea name="isi_laporan" label="Isi Laporan"
                        placeholder="Tuliskan kronologi, dampak, dan detail penting lainnya..." rows="6"
                        value="{{ old('isi_laporan') }}" :required="true" :error="$errors->first('isi_laporan')" />

                    <div>
                        <label class="text-sm font-semibold text-slate-700 block mb-2">Lampiran Bukti (Opsional)</label>
                        <input type="file" name="lampiran" accept=".jpg,.jpeg,.png,.pdf"
                            class="w-full p-3 rounded-lg border border-slate-200">
                        <p class="text-xs text-slate-500 mt-2">Format JPG, PNG, atau PDF. Maksimal 5MB.</p>
                        @error('lampiran')
                            <p class="text-sm text-red-500 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-2xl bg-primary/5 border border-primary/10 p-4 text-sm text-slate-600 leading-6">
                        Dengan mengirimkan laporan ini, Anda menyatakan bahwa data yang diberikan benar dan bersedia dihubungi
                        oleh petugas jika dibutuhkan klarifikasi tambahan.
                    </div>

                    <x-guest::ui.button type="submit" size="lg" class="w-full">
                        Kirim Pengaduan
                    </x-guest::ui.button>
                </form>
            </x-guest::ui.card>
        </div>
    </section>
</x-guest::layout.app>
