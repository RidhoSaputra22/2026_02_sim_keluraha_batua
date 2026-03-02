{{-- SECTION 6: MUTASI + KELAHIRAN + KEMATIAN TERBARU --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Mutasi Terbaru --}}
    <x-ui.card title="Mutasi Warga Terbaru" compact>
        <div class="space-y-2 max-h-64 overflow-y-auto">
            @forelse($recentMutasi as $mutasi)
            <div class="flex items-start gap-2 text-xs border-b border-base-200 pb-2 last:border-0">
                <div class="mt-0.5 shrink-0">
                    @if($mutasi->jenis_mutasi === 'datang')
                        <span class="w-2 h-2 rounded-full bg-info inline-block"></span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-warning inline-block"></span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-base-content/70 truncate">
                        <span class="font-semibold">{{ $mutasi->penduduk?->nama ?? '-' }}</span>
                        <span class="badge badge-xs {{ $mutasi->jenis_mutasi === 'datang' ? 'badge-info' : 'badge-warning' }} ml-1">
                            {{ ucfirst($mutasi->jenis_mutasi) }}
                        </span>
                    </p>
                    <p class="text-base-content/40">{{ $mutasi->tanggal_mutasi?->translatedFormat('d M Y') }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-base-content/40 text-center py-4">Belum ada mutasi.</p>
            @endforelse
        </div>
    </x-ui.card>

    {{-- Kelahiran Terbaru --}}
    <x-ui.card title="Kelahiran Terbaru" compact>
        <div class="space-y-2 max-h-64 overflow-y-auto">
            @forelse($recentKelahiran as $lahir)
            <div class="flex items-start gap-2 text-xs border-b border-base-200 pb-2 last:border-0">
                <div class="mt-0.5 shrink-0">
                    <span class="w-2 h-2 rounded-full bg-success inline-block"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-base-content/70">
                        <span class="font-semibold">{{ $lahir->nama_bayi }}</span>
                        <span class="badge badge-xs {{ $lahir->jenis_kelamin === 'L' ? 'badge-info' : 'badge-accent' }} ml-1">
                            {{ $lahir->jenis_kelamin }}
                        </span>
                    </p>
                    <p class="text-base-content/40">{{ $lahir->tanggal_lahir?->translatedFormat('d M Y') }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-base-content/40 text-center py-4">Belum ada data kelahiran.</p>
            @endforelse
        </div>
    </x-ui.card>

    {{-- Kematian Terbaru --}}
    <x-ui.card title="Kematian Terbaru" compact>
        <div class="space-y-2 max-h-64 overflow-y-auto">
            @forelse($recentKematian as $mati)
            <div class="flex items-start gap-2 text-xs border-b border-base-200 pb-2 last:border-0">
                <div class="mt-0.5 shrink-0">
                    <span class="w-2 h-2 rounded-full bg-error inline-block"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-base-content/70 truncate">
                        <span class="font-semibold">{{ $mati->penduduk?->nama ?? '-' }}</span>
                    </p>
                    <p class="text-base-content/40">{{ $mati->tanggal_meninggal?->translatedFormat('d M Y') }} &mdash; {{ $mati->penyebab ?? '-' }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-base-content/40 text-center py-4">Belum ada data kematian.</p>
            @endforelse
        </div>
    </x-ui.card>

</div>
