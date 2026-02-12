<x-layouts.app :title="'Detail Warga — ' . $penduduk->nama">

    <x-slot:header>
        <x-layouts.page-header
            title="Detail Warga"
            description="{{ $penduduk->nama }}"
        >
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('rtrw.warga.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Biodata --}}
        <div class="lg:col-span-2">
            <x-ui.card title="Biodata Penduduk">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">NIK</label>
                        <p class="font-mono font-semibold">{{ $penduduk->nik }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Nama Lengkap</label>
                        <p class="font-semibold">{{ $penduduk->nama }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Jenis Kelamin</label>
                        <p>
                            @if($penduduk->jenis_kelamin === 'L')
                                <x-ui.badge type="info" size="sm">Laki-laki</x-ui.badge>
                            @elseif($penduduk->jenis_kelamin === 'P')
                                <x-ui.badge type="secondary" size="sm">Perempuan</x-ui.badge>
                            @else
                                <span class="text-base-content/50">-</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Golongan Darah</label>
                        <p>{{ $penduduk->gol_darah ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Agama</label>
                        <p>{{ $penduduk->agama ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Status Perkawinan</label>
                        <p>{{ $penduduk->status_kawin ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Pendidikan</label>
                        <p>{{ $penduduk->pendidikan ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Status Data</label>
                        <p>
                            @if($penduduk->status_data === 'aktif' || !$penduduk->status_data)
                                <x-ui.badge type="success" size="sm">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge type="warning" size="sm">{{ ucfirst($penduduk->status_data) }}</x-ui.badge>
                            @endif
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-base-content/50 uppercase">Alamat</label>
                        <p>{{ $penduduk->alamat ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">RT / RW</label>
                        <p>RT {{ str_pad($penduduk->rt?->nomor ?? '-', 3, '0', STR_PAD_LEFT) }} / RW {{ str_pad($penduduk->rt?->rw?->nomor ?? '-', 3, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Keluarga --}}
        <div>
            <x-ui.card title="Data Keluarga">
                @if($penduduk->keluarga)
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs text-base-content/50 uppercase">No. KK</label>
                            <p class="font-mono font-semibold">{{ $penduduk->keluarga->no_kk }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-base-content/50 uppercase">Kepala Keluarga</label>
                            <p class="font-semibold">{{ $penduduk->keluarga->kepalaKeluarga?->nama ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-base-content/50 uppercase">Jumlah Anggota</label>
                            <p>{{ $penduduk->keluarga->jumlah_anggota_keluarga }} orang</p>
                        </div>

                        @if($penduduk->keluarga->anggota && $penduduk->keluarga->anggota->count() > 0)
                        <div class="divider my-1"></div>
                        <label class="text-xs text-base-content/50 uppercase">Anggota Keluarga</label>
                        <div class="space-y-2">
                            @foreach($penduduk->keluarga->anggota as $anggota)
                            <div class="flex items-center justify-between text-sm bg-base-200/30 rounded px-3 py-2">
                                <div>
                                    <span class="font-medium">{{ $anggota->nama }}</span>
                                    @if($anggota->id === $penduduk->keluarga->kepala_keluarga_id)
                                        <x-ui.badge type="primary" size="xs">KK</x-ui.badge>
                                    @endif
                                </div>
                                <span class="text-xs text-base-content/50">
                                    {{ $anggota->jenis_kelamin === 'L' ? 'L' : 'P' }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-4 text-base-content/50">
                        <p>Belum terhubung ke data keluarga.</p>
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>

</x-layouts.app>
