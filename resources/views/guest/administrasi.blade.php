<x-guest::layout.app title="Administrasi Kependudukan">
    <x-guest::ui.hero size="lg" background="white">
        <x-slot:title>
            Persyaratan <span class="text-primary">Administrasi Kependudukan</span>
        </x-slot:title>

        <x-slot:subtitle>
            Cari tahu persyaratan resmi untuk layanan administrasi kependudukan dan persuratan warga seperti domisili, usaha, SKTM, pengantar nikah, hingga surat keterangan lainnya.
        </x-slot:subtitle>

        <x-slot:actions>
            <x-guest::ui.search-bar action="{{ route('guest.administrasi') }}"
                placeholder="Cari layanan administrasi, persuratan, atau kata kunci persyaratan..." button-text="Cari"
                value="{{ request('q') }}" />
        </x-slot:actions>
    </x-guest::ui.hero>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <x-guest::ui.section-header title="Daftar Layanan Administrasi"
                    subtitle="Klik layanan untuk melihat rincian persyaratan, estimasi, biaya, dan catatan tambahan."
                    size="md" />

                @forelse ($layananSurat as $layanan)
                    <details class="rounded-2xl border border-slate-200 bg-white overflow-hidden"
                        {{ $activeLayananSlug === $layanan->slug || (!$activeLayananSlug && $loop->first) ? 'open' : '' }}>
                        <summary class="list-none cursor-pointer p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                                    <x-guest::ui.icon name="{{ $layanan->icon ?: 'description' }}" />
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-900">{{ $layanan->nama }}</h3>
                                    <p class="text-sm text-slate-500 mt-2">{{ $layanan->deskripsi ?: 'Persyaratan layanan dapat dicek pada panel berikut.' }}</p>
                                </div>
                            </div>
                            <div class="text-sm text-slate-500">
                                {{ $layanan->persyaratans->count() }} persyaratan
                            </div>
                        </summary>

                        <div class="px-6 pb-6 border-t border-slate-100">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                                <x-guest::ui.card variant="bordered" padding="md">
                                    <div class="text-sm text-slate-500">Estimasi</div>
                                    <div class="text-lg font-bold text-slate-900 mt-1">{{ $layanan->estimasi_layanan ?: 'Menyesuaikan verifikasi petugas' }}</div>
                                </x-guest::ui.card>
                                <x-guest::ui.card variant="bordered" padding="md">
                                    <div class="text-sm text-slate-500">Biaya</div>
                                    <div class="text-lg font-bold text-slate-900 mt-1">{{ $layanan->biaya ?: 'Belum diinformasikan' }}</div>
                                </x-guest::ui.card>
                                <x-guest::ui.card variant="bordered" padding="md">
                                    <div class="text-sm text-slate-500">Kontak</div>
                                    <div class="text-lg font-bold text-slate-900 mt-1">{{ $layanan->kontak_petugas ?: 'Hubungi loket kelurahan' }}</div>
                                </x-guest::ui.card>
                            </div>

                            <div class="mt-8">
                                <h4 class="text-lg font-bold text-slate-900 mb-4">Persyaratan yang Harus Disiapkan</h4>
                                <div class="space-y-3">
                                    @forelse ($layanan->persyaratans as $syarat)
                                        <div class="rounded-2xl border border-slate-100 p-4 flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center mt-0.5">
                                                <x-guest::ui.icon name="{{ $syarat->is_required ? 'check_circle' : 'info' }}" size="sm" />
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $syarat->nama }}</p>
                                                @if ($syarat->keterangan)
                                                    <p class="text-sm text-slate-500 mt-1">{{ $syarat->keterangan }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-slate-500">Belum ada persyaratan yang dipublikasikan.</p>
                                    @endforelse
                                </div>
                            </div>

                            @if ($layanan->catatan)
                                <div class="mt-8 rounded-2xl bg-amber-50 border border-amber-100 p-5">
                                    <h5 class="font-bold text-slate-900 mb-2">Catatan Tambahan</h5>
                                    <p class="text-sm text-slate-600 leading-6">{{ $layanan->catatan }}</p>
                                </div>
                            @endif
                        </div>
                    </details>
                @empty
                    <x-guest::ui.card variant="bordered" padding="lg" class="text-center">
                        <x-guest::ui.icon name="description" size="xl" color="text-slate-300" class="mb-4" />
                        <p class="text-lg font-semibold text-slate-900">Belum ada layanan administrasi yang dipublikasikan.</p>
                        <p class="text-sm text-slate-500 mt-2">Admin dapat menambahkan layanan dan daftar persyaratannya dari panel website publik.</p>
                    </x-guest::ui.card>
                @endforelse
            </div>

            <div class="space-y-6">
                <x-guest::ui.card variant="bordered" padding="lg">
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Cara Menggunakan Halaman Ini</h3>
                    <div class="space-y-4 text-sm text-slate-600">
                        <div class="flex items-start gap-3">
                            <x-guest::ui.icon name="search" size="sm" color="text-primary" class="mt-1" />
                            <p>Cari nama layanan administrasi atau persuratan yang Anda butuhkan.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <x-guest::ui.icon name="fact_check" size="sm" color="text-primary" class="mt-1" />
                            <p>Periksa semua persyaratan dan pastikan berkas lengkap sebelum datang ke kantor.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <x-guest::ui.icon name="support_agent" size="sm" color="text-primary" class="mt-1" />
                            <p>Jika ragu, hubungi kontak petugas yang tercantum pada layanan terkait.</p>
                        </div>
                    </div>
                </x-guest::ui.card>

                <x-guest::ui.card padding="lg" class="bg-primary ">
                    <h3 class="text-xl font-bold mb-2">Butuh Bantuan?</h3>
                    <p class=" text-sm leading-6 mb-5">
                        Jika persyaratan belum jelas, silakan datang ke kantor kelurahan atau buka halaman kontak untuk
                        informasi layanan lebih lanjut.
                    </p>
                    <x-guest::ui.button href="{{ route('guest.kontak') }}" variant="secondary"
                        class="bg-white text-primary hover:bg-slate-100">
                        Hubungi Kelurahan
                    </x-guest::ui.button>
                </x-guest::ui.card>
            </div>
        </div>
    </main>
</x-guest::layout.app>
