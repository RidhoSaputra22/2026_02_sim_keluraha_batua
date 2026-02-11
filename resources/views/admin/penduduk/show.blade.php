<x-layouts.app :title="'Detail Penduduk'">
    <x-slot:header>
        <x-layouts.page-header title="Detail Data Penduduk" description="{{ $penduduk->nama }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.penduduk.index') }}">← Kembali</x-ui.button>
                <x-ui.button type="primary" size="sm" href="{{ route('admin.penduduk.edit', $penduduk) }}">Edit</x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-layouts.breadcrumb :items="[
        ['label' => 'Data Penduduk', 'url' => route('admin.penduduk.index')],
        ['label' => $penduduk->nama],
    ]" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Info Utama --}}
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card title="Identitas">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-base-content/60">NIK</span>
                        <p class="font-mono font-medium">{{ $penduduk->nik }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">Nama Lengkap</span>
                        <p class="font-medium">{{ $penduduk->nama }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">Jenis Kelamin</span>
                        <p>{{ $penduduk->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">Golongan Darah</span>
                        <p>{{ $penduduk->gol_darah ?? '-' }}</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Status Pribadi">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-base-content/60">Agama</span>
                        <p>{{ $penduduk->agama ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">Status Perkawinan</span>
                        <p>{{ $penduduk->status_kawin ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">Pendidikan</span>
                        <p>{{ $penduduk->pendidikan ?? '-' }}</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Alamat">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <span class="text-sm text-base-content/60">Alamat Lengkap</span>
                        <p>{{ $penduduk->alamat ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">RT / RW</span>
                        <p>{{ $penduduk->rt->nomor ?? '-' }} / {{ $penduduk->rt->rw->nomor ?? '-' }}</p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <x-ui.card title="Status">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Status Data</span>
                        <x-ui.badge type="{{ ($penduduk->status_data ?? 'aktif') === 'aktif' ? 'success' : 'warning' }}" size="sm">
                            {{ $penduduk->status_data ?? 'aktif' }}
                        </x-ui.badge>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm">Tanggal Input</span>
                        <span class="text-sm">{{ $penduduk->tgl_input ? \Carbon\Carbon::parse($penduduk->tgl_input)->format('d/m/Y') : ($penduduk->created_at?->format('d/m/Y') ?? '-') }}</span>
                    </div>
                </div>
            </x-ui.card>

            @if($penduduk->keluarga)
            <x-ui.card title="Data Keluarga">
                <div class="space-y-2">
                    <div>
                        <span class="text-sm text-base-content/60">No. KK</span>
                        <p class="font-mono text-sm">{{ $penduduk->keluarga->no_kk }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">Kepala Keluarga</span>
                        <p class="font-medium">{{ $penduduk->keluarga->kepalaKeluarga->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">Jumlah Anggota</span>
                        <p>{{ $penduduk->keluarga->jumlah_anggota_keluarga ?? '-' }} orang</p>
                    </div>
                </div>
                <x-slot:actions>
                    <x-ui.button type="ghost" size="xs" href="{{ route('admin.keluarga.show', $penduduk->keluarga) }}">Detail KK →</x-ui.button>
                </x-slot:actions>
            </x-ui.card>
            @endif
        </div>
    </div>
</x-layouts.app>
