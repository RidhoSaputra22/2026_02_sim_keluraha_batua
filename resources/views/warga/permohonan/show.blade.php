<x-layouts.app :title="'Detail Permohonan'">

    <x-slot:header>
        <x-layouts.page-header title="Detail Permohonan" description="Tracking dan detail permohonan surat Anda">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('warga.permohonan.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Detail Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Header with status --}}
            <x-ui.card>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold font-mono">{{ $permohonan->nomor_surat }}</h3>
                        <p class="text-sm text-base-content/60 mt-1">Diajukan pada {{ $permohonan->tgl_input?->format('d F Y, H:i') }}</p>
                    </div>
                    @php
                        $badgeType = match($permohonan->status_esign) {
                            'draft' => 'warning',
                            'proses' => 'info',
                            'signed' => 'success',
                            'reject' => 'error',
                            default => 'ghost',
                        };
                        $statusLabel = match($permohonan->status_esign) {
                            'draft' => 'Menunggu Verifikasi',
                            'proses' => 'Menunggu Tanda Tangan',
                            'signed' => 'Selesai / Ditandatangani',
                            'reject' => 'Ditolak',
                            default => '-',
                        };
                    @endphp
                    <x-ui.badge :type="$badgeType" size="lg">{{ $statusLabel }}</x-ui.badge>
                </div>
            </x-ui.card>

            {{-- Detail Surat --}}
            <x-ui.card title="Detail Surat">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                    <div>
                        <span class="text-base-content/60">Jenis Surat</span>
                        <p class="font-medium">{{ $permohonan->jenis->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-base-content/60">Sifat Surat</span>
                        <p class="font-medium">{{ $permohonan->sifat->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-base-content/60">Perihal</span>
                        <p class="font-medium">{{ $permohonan->perihal }}</p>
                    </div>
                    <div>
                        <span class="text-base-content/60">Tanggal Surat</span>
                        <p class="font-medium">{{ $permohonan->tanggal_surat?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                    @if($permohonan->uraian)
                        <div class="md:col-span-2">
                            <span class="text-base-content/60">Keterangan</span>
                            <p class="font-medium">{{ $permohonan->uraian }}</p>
                        </div>
                    @endif
                </div>
            </x-ui.card>

            {{-- Pemohon --}}
            <x-ui.card title="Data Pemohon">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                    <div>
                        <span class="text-base-content/60">Nama</span>
                        <p class="font-medium">{{ $permohonan->pemohon->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-base-content/60">NIK</span>
                        <p class="font-mono">{{ $permohonan->pemohon->penduduk->nik ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-base-content/60">No. HP</span>
                        <p class="font-medium">{{ $permohonan->pemohon->no_hp_wa ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-base-content/60">Email</span>
                        <p class="font-medium">{{ $permohonan->pemohon->email ?? '-' }}</p>
                    </div>
                </div>
            </x-ui.card>

            {{-- Catatan Penolakan --}}
            @if($permohonan->status_esign === 'reject')
                <x-ui.alert type="error">
                    <div>
                        <p class="font-semibold">Permohonan Ditolak</p>
                        <p class="text-sm mt-1">{{ $permohonan->catatan_verifikasi ?: $permohonan->catatan_penandatangan ?: 'Tidak ada catatan.' }}</p>
                    </div>
                </x-ui.alert>
            @endif
        </div>

        {{-- Right: Timeline --}}
        <div class="lg:col-span-1">
            <x-ui.card title="Tracking Status">
                @php
                    $steps = [
                        ['label' => 'Permohonan Diajukan', 'date' => $permohonan->tgl_input, 'done' => true],
                        ['label' => 'Verifikasi', 'date' => $permohonan->tgl_verifikasi, 'done' => in_array($permohonan->status_esign, ['proses', 'signed'])],
                        ['label' => 'Tanda Tangan', 'date' => $permohonan->tgl_ttd, 'done' => $permohonan->status_esign === 'signed'],
                        ['label' => 'Selesai', 'date' => $permohonan->status_esign === 'signed' ? $permohonan->tgl_ttd : null, 'done' => $permohonan->status_esign === 'signed'],
                    ];

                    $rejected = $permohonan->status_esign === 'reject';
                @endphp

                <ul class="steps steps-vertical w-full">
                    @foreach($steps as $step)
                        @php
                            $stepClass = $step['done'] ? 'step-success' : ($rejected && $loop->index === 1 ? 'step-error' : '');
                            $icon = $step['done'] ? '✓' : ($rejected && $loop->index === 1 ? '✗' : '○');
                        @endphp
                        <li class="step {{ $stepClass }}" data-content="{{ $icon }}">
                            <div class="text-left ml-2 py-2">
                                <p class="text-sm font-medium {{ !$step['done'] && !($rejected && $loop->index === 1) ? 'text-base-content/40' : '' }}">
                                    {{ $step['label'] }}
                                </p>
                                @if($step['date'])
                                    <p class="text-xs text-base-content/60">{{ $step['date']->format('d M Y, H:i') }}</p>
                                @elseif($step['done'])
                                    <p class="text-xs text-base-content/60">Selesai</p>
                                @elseif($rejected && $loop->index === 1)
                                    <p class="text-xs text-error">Ditolak</p>
                                @else
                                    <p class="text-xs text-base-content/40">Menunggu...</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>

                @if($permohonan->status_esign === 'signed' && $permohonan->arsip_path)
                    <div class="mt-6 pt-4 border-t border-base-300">
                        <x-ui.button type="success" class="w-full" href="{{ asset('storage/' . $permohonan->arsip_path) }}" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Unduh Dokumen
                        </x-ui.button>
                    </div>
                @endif
            </x-ui.card>

            @if($permohonan->verifikator)
                <x-ui.card title="Petugas" class="mt-6">
                    <div class="text-sm space-y-2">
                        @if($permohonan->verifikator)
                            <div>
                                <span class="text-base-content/60">Verifikator:</span>
                                <span class="font-medium ml-1">{{ $permohonan->verifikator->name }}</span>
                            </div>
                        @endif
                        @if($permohonan->penandatanganPejabat)
                            <div>
                                <span class="text-base-content/60">Penandatangan:</span>
                                <span class="font-medium ml-1">{{ $permohonan->penandatanganPejabat->nama_pejabat ?? '-' }}</span>
                            </div>
                        @endif
                    </div>
                </x-ui.card>
            @endif
        </div>
    </div>

</x-layouts.app>
