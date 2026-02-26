<x-layouts.app :title="'Detail Keluarga - ' . $keluarga->no_kk">
    <x-slot:header>
        <x-layouts.page-header title="Detail Kartu Keluarga" description="No. KK: {{ $keluarga->no_kk }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('kependudukan.keluarga.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
                <x-ui.button type="primary" size="sm" href="{{ route('kependudukan.keluarga.edit', $keluarga) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Info KK --}}
        <div class="lg:col-span-1 space-y-6">
            <x-ui.card>
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <h3 class="font-bold text-lg">{{ $keluarga->kepalaKeluarga->nama ?? '-' }}</h3>
                    <p class="text-sm text-base-content/60">Kepala Keluarga</p>
                </div>
                <div class="divide-y">
                    <div class="py-2 flex justify-between">
                        <span class="text-sm text-base-content/60">No. KK</span>
                        <span class="font-mono text-sm font-medium">{{ $keluarga->no_kk }}</span>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-sm text-base-content/60">NIK KK</span>
                        <span class="font-mono text-sm">{{ $keluarga->kepalaKeluarga->nik ?? '-' }}</span>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-sm text-base-content/60">Anggota</span>
                        <x-ui.badge type="primary" size="sm">{{ $keluarga->jumlah_anggota_keluarga ?? 0 }} orang</x-ui.badge>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-sm text-base-content/60">RT/RW</span>
                        <span class="text-sm">{{ $keluarga->rt->nomor ?? '-' }}/{{ $keluarga->rt->rw->nomor ?? '-' }}</span>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Anggota Keluarga --}}
        <div class="lg:col-span-2">
            <x-ui.card title="Anggota Keluarga">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>L/P</th>
                                <th>Agama</th>
                                <th>Status Kawin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($keluarga->anggota as $a)
                            <tr class="hover">
                                <td class="font-mono text-xs">{{ $a->nik }}</td>
                                <td>
                                    <span class="font-medium">{{ $a->nama }}</span>
                                    @if($a->id === $keluarga->kepala_keluarga_id)
                                        <x-ui.badge type="primary" size="xs" class="ml-1">KK</x-ui.badge>
                                    @endif
                                </td>
                                <td>{{ $a->jenis_kelamin ?? '-' }}</td>
                                <td class="text-sm">{{ $a->agama ?? '-' }}</td>
                                <td class="text-sm">{{ ucfirst($a->status_kawin ?? '-') }}</td>
                                <td>
                                    <x-ui.button type="info" size="xs" :outline="true" href="{{ route('kependudukan.penduduk.show', $a) }}">Detail</x-ui.button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-8 text-base-content/60">Belum ada data anggota keluarga terdaftar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
