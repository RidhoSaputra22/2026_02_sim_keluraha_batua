<x-guest::layout.app title="Cek Data">
    <x-guest::ui.hero size="xl" background="white">
        <x-slot:title>
            Cek <span class="text-primary">Data Kependudukan</span>
        </x-slot:title>

        <x-slot:subtitle>
            Masukkan NIK untuk melihat ringkasan data warga yang terdaftar di sistem kelurahan secara cepat, aman, dan mudah dipahami.
        </x-slot:subtitle>
    </x-guest::ui.hero>

    <section class="bg-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <x-guest::ui.stat-card title="Total Penduduk" :value="(string) $totalPenduduk" icon="groups"
                    description="Data warga terdaftar" color="primary" />
                <x-guest::ui.stat-card title="Kartu Keluarga" :value="(string) $totalKK" icon="home"
                    description="Keluarga aktif" color="info" />
                <x-guest::ui.stat-card title="Wilayah RW" :value="(string) $totalRw" icon="map"
                    description="Cakupan wilayah kelurahan" color="success" />
            </div>
        </div>
    </section>

    <section class="border-y border-slate-100 bg-slate-50 py-12" id="cek-data-result">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <div class="space-y-6 lg:col-span-7">
                    <x-guest::ui.card variant="bordered" padding="lg">
                        <div class="flex items-start gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                <x-guest::ui.icon name="person_search" size="lg" color="text-primary" />
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-slate-900">Cek Data Warga</h2>
                                <p class="mt-2 text-sm leading-6 text-slate-500">
                                    Masukkan NIK 16 digit untuk melihat ringkasan data identitas, KK, wilayah RT/RW, dan status data kependudukan.
                                </p>
                            </div>
                        </div>

                        @if (session('error'))
                            <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                                <div class="flex items-center gap-2 font-semibold">
                                    <x-guest::ui.icon name="error" size="sm" color="text-red-600" />
                                    Data tidak ditemukan
                                </div>
                                <p class="mt-2">{{ session('error') }}</p>
                            </div>
                        @endif

                        @if (session('result'))
                            <div class="mt-6 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                                <div class="flex items-center gap-2 font-semibold">
                                    <x-guest::ui.icon name="check_circle" size="sm" color="text-green-600" />
                                    Data kependudukan berhasil ditemukan
                                </div>
                                <p class="mt-2">Silakan cek ringkasan data di panel bawah. Jika ada perbedaan, lanjutkan ke layanan administrasi kependudukan.</p>
                            </div>
                        @endif

                        <form class="mt-8 space-y-6" method="POST" action="{{ route('guest.cek-data.search') }}">
                            @csrf

                            <x-guest::ui.input name="nik" label="NIK (Nomor Induk Kependudukan)"
                                placeholder="Masukkan 16 digit NIK Anda" icon="badge" :required="true"
                                :value="old('nik')" :error="$errors->first('nik')" maxlength="16"
                                inputmode="numeric" pattern="[0-9]{16}" autocomplete="off" />

                            <p class="text-sm text-slate-400">
                                Pastikan NIK sesuai dengan KTP atau Kartu Keluarga agar pencarian berhasil.
                            </p>

                            <div class="flex flex-wrap gap-3">
                                <x-guest::ui.button type="submit" size="lg" icon="search" class="w-full md:w-auto">
                                    Cek Data
                                </x-guest::ui.button>
                                <x-guest::ui.button href="{{ route('guest.administrasi') }}" variant="outline"
                                    size="lg" class="w-full md:w-auto">
                                    Layanan Administrasi
                                </x-guest::ui.button>
                            </div>
                        </form>
                    </x-guest::ui.card>

                    @if (session('result'))
                        @php
                            $result = session('result');
                            $statusVariant = match (strtolower((string) $result['status_data'])) {
                                'aktif', 'valid' => 'success',
                                'proses', 'pending' => 'warning',
                                'tidak aktif', 'tidak_aktif', 'nonaktif' => 'danger',
                                default => 'default',
                            };
                        @endphp

                        <x-guest::ui.card variant="bordered" padding="lg" >
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold text-slate-900">Ringkasan Data Penduduk</h3>
                                    <p class="mt-2 text-sm text-slate-500">Data di bawah ini merupakan ringkasan hasil pencarian berdasarkan NIK dan sudah disamarkan untuk menjaga privasi warga.</p>
                                </div>
                                <div class="min-w-32">
                                    <x-guest::ui.badge :variant="$statusVariant" size="sm">
                                    Status Data: {{ $result['status_data'] }}
                                </x-guest::ui.badge>
                                </div>
                            </div>

                            <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="rounded-2xl border border-slate-100 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Nama Lengkap</div>
                                    <div class="mt-2 text-lg font-bold text-slate-900">{{ $result['nama'] }}</div>
                                </div>
                                <div class="rounded-2xl border border-slate-100 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">NIK</div>
                                    <div class="mt-2 text-lg font-bold text-slate-900">{{ $result['nik'] }}</div>
                                </div>
                                <div class="rounded-2xl border border-slate-100 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Nomor KK</div>
                                    <div class="mt-2 text-base font-semibold text-slate-900">{{ $result['no_kk'] }}</div>
                                </div>
                                <div class="rounded-2xl border border-slate-100 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">RT / RW</div>
                                    <div class="mt-2 text-base font-semibold text-slate-900">RT {{ $result['rt'] }} / RW {{ $result['rw'] }}</div>
                                </div>
                                <div class="rounded-2xl border border-slate-100 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Jenis Kelamin</div>
                                    <div class="mt-2 text-base font-semibold text-slate-900">{{ $result['jenis_kelamin'] }}</div>
                                </div>
                                <div class="rounded-2xl border border-slate-100 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Agama</div>
                                    <div class="mt-2 text-base font-semibold text-slate-900">{{ $result['agama'] }}</div>
                                </div>
                                <div class="rounded-2xl border border-slate-100 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status Kawin</div>
                                    <div class="mt-2 text-base font-semibold text-slate-900">{{ $result['status_kawin'] }}</div>
                                </div>
                                <div class="rounded-2xl border border-slate-100 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Pendidikan</div>
                                    <div class="mt-2 text-base font-semibold text-slate-900">{{ $result['pendidikan'] }}</div>
                                </div>
                                <div class="rounded-2xl border border-slate-100 p-4 md:col-span-2">
                                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Pekerjaan</div>
                                    <div class="mt-2 text-base font-semibold text-slate-900">{{ $result['pekerjaan'] }}</div>
                                </div>
                                <div class="rounded-2xl border border-slate-100 p-4 md:col-span-2">
                                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Alamat</div>
                                    <div class="mt-2 text-base font-semibold leading-7 text-slate-900">{{ $result['alamat'] }}</div>
                                </div>
                            </div>
                        </x-guest::ui.card>
                    @endif
                </div>

                <aside class="space-y-6 lg:col-span-5">
                    <x-guest::ui.card variant="bordered" padding="lg">
                        <h3 class="text-xl font-bold text-slate-900">Yang Bisa Dicek</h3>
                        <div class="mt-5 space-y-4 text-sm text-slate-600">
                            <div class="flex items-start gap-3">
                                <x-guest::ui.icon name="badge" size="sm" color="text-primary" class="mt-1" />
                                <p>Identitas dasar warga berdasarkan NIK dan nomor KK.</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <x-guest::ui.icon name="home" size="sm" color="text-primary" class="mt-1" />
                                <p>Alamat domisili serta wilayah RT/RW tempat warga terdaftar.</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <x-guest::ui.icon name="fact_check" size="sm" color="text-primary" class="mt-1" />
                                <p>Status data, agama, pendidikan, pekerjaan, dan status perkawinan.</p>
                            </div>
                        </div>
                    </x-guest::ui.card>

                    <x-guest::ui.card padding="lg" class="bg-primary ">
                        <h3 class="text-xl font-bold">Data Tidak Sesuai?</h3>
                        <p class="mt-3 text-sm leading-6 ">
                            Jika data yang muncul berbeda dengan dokumen Anda, lanjutkan ke layanan administrasi kependudukan atau hubungi petugas kelurahan untuk verifikasi.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            <x-guest::ui.button href="{{ route('guest.administrasi') }}" variant="secondary"
                                class="bg-white text-primary hover:bg-slate-100">
                                Buka Administrasi
                            </x-guest::ui.button>
                            <x-guest::ui.button href="{{ route('guest.kontak') }}" variant="outline"
                                class="border-white/30 text-white hover:bg-white/10">
                                Hubungi Kelurahan
                            </x-guest::ui.button>
                        </div>
                    </x-guest::ui.card>
                </aside>
            </div>
        </section>

    <section class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <x-guest::ui.section-header title="Informasi Penting" subtitle="Hal yang perlu Anda ketahui" size="md" />

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <x-guest::ui.card padding="md">
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary/10">
                            <x-guest::ui.icon name="security" size="lg" color="text-primary" />
                        </div>
                        <h3 class="font-bold mb-2 text-slate-900">Akses Aman</h3>
                        <p class="text-sm text-slate-600">
                            Data yang ditampilkan berupa ringkasan untuk membantu verifikasi awal warga.
                        </p>
                    </div>
                </x-guest::ui.card>

                <x-guest::ui.card padding="md">
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary/10">
                            <x-guest::ui.icon name="schedule" size="lg" color="text-primary" />
                        </div>
                        <h3 class="font-bold mb-2 text-slate-900">Cek Cepat</h3>
                        <p class="text-sm text-slate-600">
                            Pencarian dilakukan langsung dari data kependudukan yang tersimpan di sistem kelurahan.
                        </p>
                    </div>
                </x-guest::ui.card>

                <x-guest::ui.card padding="md">
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary/10">
                            <x-guest::ui.icon name="support_agent" size="lg" color="text-primary" />
                        </div>
                        <h3 class="font-bold mb-2 text-slate-900">Bantuan Lanjutan</h3>
                        <p class="text-sm text-slate-600">
                            Jika ada perbedaan data, petugas kelurahan siap membantu melalui layanan administrasi dan kontak resmi.
                        </p>
                    </div>
                </x-guest::ui.card>
            </div>
        </div>
    </section>
</x-guest::layout.app>
