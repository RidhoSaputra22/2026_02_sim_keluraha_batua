<x-guest::layout.app title="Administrasi Kependudukan">
    <x-guest::ui.hero size="xl" background="white">
        <x-slot:title>
            Layanan <span class="text-primary">Administrasi Kependudukan</span>
        </x-slot:title>

        <x-slot:subtitle>
            Pilih layanan persuratan yang Anda butuhkan untuk melihat estimasi, biaya, persyaratan, dan catatan
            administrasi secara lebih rinci pada halaman detail tersendiri.
        </x-slot:subtitle>

        <x-slot:actions>
            <x-guest::ui.search-bar action="{{ route('guest.administrasi') }}"
                placeholder="Cari layanan administrasi, persuratan, atau kata kunci persyaratan..." button-text="Cari"
                value="{{ $search }}" />
        </x-slot:actions>
    </x-guest::ui.hero>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">

                @if ($search !== '')
                    <div class="rounded-2xl border border-primary/15 bg-primary/5 px-5 py-4 text-sm text-slate-600">
                        Menampilkan
                        <span class="font-semibold text-slate-900">{{ number_format($layananSurat->total()) }}</span>
                        hasil untuk pencarian
                        <span class="font-semibold text-primary">"{{ $search }}"</span>.
                    </div>
                @endif

                <div class="space-y-5">
                    @forelse ($layananSurat as $layanan)
                        <div class="rounded-2xl border border-slate-100 p-5 transition-colors hover:border-primary/20">
                            <div class="flex items-start gap-4">
                                <div
                                    class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                    <x-guest::ui.icon :name="$layanan->icon ?: 'description'" size="lg" color="text-primary" />
                                </div>

                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg font-bold text-slate-900">
                                        {{ $layanan->nama }}
                                    </h3>

                                    @if (!empty($layanan->deskripsi))
                                        <p class="mt-1 text-sm text-slate-500">{{ $layanan->deskripsi }}
                                        </p>
                                    @endif
                                    <div class="mt-4 flex flex-wrap gap-3">
                                        <x-guest::ui.button href="{{ route('guest.administrasi.show', $layanan) }}"
                                            variant="outline" size="sm">
                                            Lihat Persyaratan
                                        </x-guest::ui.button>

                                    </div>
                                </div>
                                <div class="space-x-1">

                                    @if (!empty($layanan->biaya))
                                        <x-guest::ui.badge variant="primary" size="sm">
                                            {{ $layanan->biaya }}
                                        </x-guest::ui.badge>
                                    @endif
                                    @if (!empty($layanan->estimasi_layanan))
                                        <x-guest::ui.badge variant="info" size="sm">
                                            Estimasi: {{ $layanan->estimasi_layanan }}
                                        </x-guest::ui.badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <x-guest::ui.card variant="bordered" padding="lg" class="text-center">
                            <x-guest::ui.icon name="description" size="xl" color="text-slate-300" class="mb-4" />
                            <p class="text-lg font-semibold text-slate-900">
                                Belum ada layanan administrasi yang sesuai.
                            </p>
                            <p class="mt-2 text-sm text-slate-500">
                                Coba gunakan kata kunci yang lebih umum atau hubungi kelurahan untuk informasi
                                layanan yang belum dipublikasikan.
                            </p>
                        </x-guest::ui.card>
                    @endforelse
                </div>

                @if ($layananSurat->hasPages())
                    <div class="rounded-2xl border border-slate-100 bg-white p-4">
                        {{ $layananSurat->links('vendor.pagination.guest-light') }}
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <x-guest::ui.card variant="bordered" padding="lg">
                    <h3 class="mb-4 text-xl font-bold text-slate-900">Cara Menggunakan Halaman Ini</h3>
                    <div class="space-y-4 text-sm text-slate-600">
                        <div class="flex items-start gap-3">
                            <x-guest::ui.icon name="search" size="sm" color="text-primary" class="mt-1" />
                            <p>Cari nama layanan administrasi atau kata kunci dokumen yang ingin Anda urus.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <x-guest::ui.icon name="open_in_new" size="sm" color="text-primary" class="mt-1" />
                            <p>Buka halaman detail layanan untuk melihat persyaratan lengkap dalam tampilan khusus.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <x-guest::ui.icon name="fact_check" size="sm" color="text-primary" class="mt-1" />
                            <p>Pastikan semua berkas lengkap sebelum datang ke kantor kelurahan.</p>
                        </div>
                    </div>
                </x-guest::ui.card>

                <x-guest::ui.card padding="lg" class="bg-primary">
                    <h3 class="mb-2 text-xl font-bold">Butuh Bantuan?</h3>
                    <p class="mb-5 text-sm leading-6">
                        Jika persyaratan belum jelas, silakan datang ke kantor kelurahan atau buka halaman kontak
                        untuk informasi layanan lebih lanjut.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <x-guest::ui.button href="{{ route('guest.kontak') }}" variant="secondary"
                            class="bg-white text-primary hover:bg-slate-100">
                            Hubungi Kelurahan
                        </x-guest::ui.button>

                    </div>
                </x-guest::ui.card>
            </div>
        </div>
    </main>
</x-guest::layout.app>
