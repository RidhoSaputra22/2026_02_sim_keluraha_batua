<x-layouts.app :title="'Detail Warga'">
    <x-slot:header>
        <x-layouts.page-header title="Detail Warga" description="{{ $penduduk->nama }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('rtrw.warga.index') }}">
                    Kembali ke Daftar
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card title="Identitas Warga">
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
                        <span class="text-sm text-base-content/60">Agama</span>
                        <p>{{ $penduduk->agama ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">Status Kawin</span>
                        <p>{{ $penduduk->status_kawin ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-base-content/60">Pendidikan</span>
                        <p>{{ $penduduk->pendidikan ?? '-' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-sm text-base-content/60">Alamat</span>
                        <p>{{ $penduduk->alamat ?? '-' }}</p>
                    </div>
                </div>
            </x-ui.card>

            @if ($penduduk->keluarga)
                <x-ui.card title="Anggota Keluarga">
                    <div class="mb-4 text-sm text-base-content/70">
                        No. KK: <span class="font-mono">{{ $penduduk->keluarga->no_kk }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NIK</th>
                                    <th>Hubungan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($penduduk->keluarga->anggota as $anggota)
                                    <tr>
                                        <td>{{ $anggota->nama }}</td>
                                        <td class="font-mono text-xs">{{ $anggota->nik }}</td>
                                        <td>
                                            @if ((int) $penduduk->keluarga->kepala_keluarga_id === (int) $anggota->id)
                                                <x-ui.badge type="primary" size="xs">Kepala Keluarga</x-ui.badge>
                                            @elseif ((int) $penduduk->id === (int) $anggota->id)
                                                <x-ui.badge type="info" size="xs">Data Dipilih</x-ui.badge>
                                            @else
                                                <span class="text-sm text-base-content/70">Anggota</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-base-content/60">Tidak ada anggota keluarga.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>
            @endif
        </div>

        <div class="space-y-6">
            <x-ui.card title="Wilayah">
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-base-content/60">RT</span>
                        <span>{{ $penduduk->rt->nomor ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-base-content/60">RW</span>
                        <span>{{ $penduduk->rt->rw->nomor ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-base-content/60">No. KK</span>
                        <span class="font-mono text-xs">{{ $penduduk->keluarga->no_kk ?? '-' }}</span>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Status Data">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-base-content/60">Status</span>
                    <x-ui.badge
                        type="{{ strtolower((string) ($penduduk->status_data ?? 'aktif')) === 'aktif' ? 'success' : 'warning' }}"
                        size="sm">
                        {{ ucfirst(strtolower((string) ($penduduk->status_data ?? 'aktif'))) }}
                    </x-ui.badge>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="space-y-2">
                    <x-ui.button type="primary" class="w-full" href="{{ route('rtrw.keluarga.index') }}">
                        Lihat Semua Keluarga
                    </x-ui.button>
                    <x-ui.button type="ghost" class="w-full" href="{{ route('rtrw.warga.index') }}">
                        Kembali ke Daftar Warga
                    </x-ui.button>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
