<x-layouts.app :title="'Riwayat Permohonan'">

    <x-slot:header>
        <x-layouts.page-header title="Riwayat & Tracking" description="Lacak status semua permohonan surat Anda">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('warga.permohonan.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Ajukan Baru
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    {{-- Search & Filter --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('warga.riwayat.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nomor surat atau perihal..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-48">
                <x-ui.select name="status" placeholder="Semua Status" :options="[
                    'draft' => 'Menunggu Verifikasi',
                    'proses' => 'Menunggu TTD',
                    'signed' => 'Selesai',
                    'reject' => 'Ditolak',
                ]" selected="{{ request('status') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    Cari
                </x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('warga.riwayat.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Results --}}
    @if($surats->count() > 0)
        <div class="space-y-4">
            @foreach($surats as $surat)
                <x-ui.card>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="font-bold font-mono text-sm">{{ $surat->nomor_surat }}</h3>
                                @php
                                    $badgeType = match($surat->status_esign) {
                                        'draft' => 'warning',
                                        'proses' => 'info',
                                        'signed' => 'success',
                                        'reject' => 'error',
                                        default => 'ghost',
                                    };
                                    $statusLabel = match($surat->status_esign) {
                                        'draft' => 'Menunggu Verifikasi',
                                        'proses' => 'Menunggu TTD',
                                        'signed' => 'Selesai',
                                        'reject' => 'Ditolak',
                                        default => '-',
                                    };
                                @endphp
                                <x-ui.badge :type="$badgeType" size="sm">{{ $statusLabel }}</x-ui.badge>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm text-base-content/70">
                                <div>
                                    <span class="text-base-content/50">Jenis:</span>
                                    <span class="font-medium">{{ $surat->jenis->nama ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-base-content/50">Perihal:</span>
                                    <span class="font-medium">{{ $surat->perihal }}</span>
                                </div>
                                <div>
                                    <span class="text-base-content/50">Tanggal:</span>
                                    <span>{{ $surat->tgl_input?->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 shrink-0">
                            <x-ui.button type="ghost" size="sm" href="{{ route('warga.permohonan.show', $surat) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                Detail
                            </x-ui.button>
                            @if($surat->status_esign === 'signed' && $surat->arsip_path)
                                <x-ui.button type="success" size="sm" href="{{ asset('storage/' . $surat->arsip_path) }}" target="_blank">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    Unduh
                                </x-ui.button>
                            @endif
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        @if($surats->hasPages())
            <div class="mt-6">
                {{ $surats->links() }}
            </div>
        @endif
    @else
        <x-ui.card>
            <div class="text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-base-content/20 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-semibold text-base-content/60 mb-2">Belum Ada Riwayat</h3>
                <p class="text-sm text-base-content/40 mb-4">Anda belum memiliki permohonan surat.</p>
                <x-ui.button type="primary" href="{{ route('warga.permohonan.create') }}">Ajukan Permohonan Pertama</x-ui.button>
            </div>
        </x-ui.card>
    @endif

</x-layouts.app>
