{{-- SECTION 5: FASILITAS + WARGA TERBARU --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Fasilitas Wilayah --}}
    <x-ui.card title="Fasilitas di Wilayah" compact>
        <div class="space-y-3">
            <div class="flex items-center gap-3 rounded-lg bg-error/10 p-3">
                <div class="w-10 h-10 rounded-xl bg-error/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-error">{{ $totalFaskes }}</div>
                    <div class="text-xs text-error/70 font-medium">Fasilitas Kesehatan</div>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-lg bg-warning/10 p-3">
                <div class="w-10 h-10 rounded-xl bg-warning/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-warning">{{ $totalTempatIbadah }}</div>
                    <div class="text-xs text-warning/70 font-medium">Tempat Ibadah</div>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-lg bg-info/10 p-3">
                <div class="w-10 h-10 rounded-xl bg-info/20 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-info">{{ $totalUmkm }}</div>
                    <div class="text-xs text-info/70 font-medium">UMKM ({{ $umkmAktif }} aktif)</div>
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- Warga Terbaru --}}
    <div class="lg:col-span-2">
        <x-ui.card title="Warga Terbaru" compact>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>RT/RW</th>
                            <th>JK</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentWarga as $warga)
                        <tr class="hover">
                            <td class="font-mono text-xs">{{ $warga->nik }}</td>
                            <td class="font-medium">{{ $warga->nama }}</td>
                            <td class="text-xs text-base-content/60">
                                {{ str_pad($warga->rt?->nomor ?? '-', 2, '0', STR_PAD_LEFT) }}/{{ str_pad($warga->rt?->rw?->nomor ?? '-', 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td>
                                <span class="badge badge-sm {{ $warga->jenis_kelamin === 'L' ? 'badge-info' : 'badge-accent' }}">
                                    {{ $warga->jenis_kelamin }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('kependudukan.penduduk.show', $warga) }}" class="btn btn-ghost btn-xs">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-base-content/40">Belum ada data warga.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('rtrw.warga.index') }}">Lihat Semua &rarr;</x-ui.button>
            </x-slot:actions>
        </x-ui.card>
    </div>

</div>
