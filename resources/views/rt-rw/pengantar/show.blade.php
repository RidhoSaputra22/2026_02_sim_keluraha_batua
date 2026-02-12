<x-layouts.app :title="'Detail Surat'">

    <x-slot:header>
        <x-layouts.page-header title="Detail Surat"
            description="{{ $surat->jenis?->nama ?? 'Surat' }} — {{ $surat->pemohon?->nama ?? $surat->nama_dalam_surat ?? '' }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('rtrw.pengantar.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @php
    $statusMap = [
    'draft' => 'warning',
    'proses' => 'info',
    'signed' => 'success',
    'reject' => 'error',
    ];
    $badgeType = $statusMap[$surat->status_esign] ?? 'warning';
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Detail Surat --}}
        <div class="lg:col-span-2">
            <x-ui.card title="Informasi Surat">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Nomor Surat</label>
                        <p class="font-mono font-semibold">{{ $surat->nomor_surat ?? 'Belum diisi' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Tanggal Surat</label>
                        <p>{{ $surat->tanggal_surat?->format('d F Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Jenis Surat</label>
                        <p>{{ $surat->jenis?->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Sifat Surat</label>
                        <p>{{ $surat->sifat?->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Status</label>
                        <p>
                            <x-ui.badge type="{{ $badgeType }}" size="sm">{{ ucfirst($surat->status_esign ?? 'Draft') }}
                            </x-ui.badge>
                        </p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Arah Surat</label>
                        <p>{{ ucfirst($surat->arah ?? '-') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-base-content/50 uppercase">Perihal</label>
                        <p>{{ $surat->perihal ?? '-' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-base-content/50 uppercase">Uraian</label>
                        <p class="whitespace-pre-line">{{ $surat->uraian ?? '-' }}</p>
                    </div>
                </div>
            </x-ui.card>

            {{-- Timeline / Proses --}}
            <x-ui.card title="Riwayat Proses" class="mt-6">
                <ul class="timeline timeline-vertical timeline-compact">
                    <li>
                        <div class="timeline-start timeline-box text-xs">
                            {{ $surat->tgl_input?->format('d/m/Y H:i') ?? '-' }}
                        </div>
                        <div class="timeline-middle">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="h-5 w-5 text-success">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="timeline-end timeline-box">
                            <span class="font-medium">Diinput</span>
                            <span class="text-xs text-base-content/60 block">oleh
                                {{ $surat->petugasInput?->name ?? '-' }}</span>
                        </div>
                        <hr />
                    </li>

                    @if($surat->tgl_verifikasi)
                    <li>
                        <hr />
                        <div class="timeline-start timeline-box text-xs">
                            {{ $surat->tgl_verifikasi->format('d/m/Y H:i') }}
                        </div>
                        <div class="timeline-middle">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="h-5 w-5 text-success">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="timeline-end timeline-box">
                            <span class="font-medium">Diverifikasi</span>
                            <span class="text-xs text-base-content/60 block">oleh
                                {{ $surat->verifikator?->name ?? '-' }}</span>
                            @if($surat->catatan_verifikasi)
                            <span class="text-xs italic">{{ $surat->catatan_verifikasi }}</span>
                            @endif
                        </div>
                        <hr />
                    </li>
                    @endif

                    @if($surat->tgl_ttd)
                    <li>
                        <hr />
                        <div class="timeline-start timeline-box text-xs">
                            {{ $surat->tgl_ttd->format('d/m/Y H:i') }}
                        </div>
                        <div class="timeline-middle">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                class="h-5 w-5 text-success">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="timeline-end timeline-box">
                            <span class="font-medium">Ditandatangani</span>
                            <span class="text-xs text-base-content/60 block">oleh
                                {{ $surat->penandatanganPejabat?->nama ?? '-' }}</span>
                            @if($surat->catatan_penandatangan)
                            <span class="text-xs italic">{{ $surat->catatan_penandatangan }}</span>
                            @endif
                        </div>
                    </li>
                    @endif
                </ul>
            </x-ui.card>
        </div>

        {{-- Pemohon --}}
        <div>
            <x-ui.card title="Data Pemohon">
                @if($surat->pemohon)
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Nama</label>
                        <p class="font-semibold">{{ $surat->pemohon->nama }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">No. HP / WA</label>
                        <p>{{ $surat->pemohon->no_hp_wa ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Email</label>
                        <p>{{ $surat->pemohon->email ?? '-' }}</p>
                    </div>
                    @if($surat->pemohon->penduduk)
                    <div class="divider my-1"></div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">NIK</label>
                        <p class="font-mono">{{ $surat->pemohon->penduduk->nik }}</p>
                    </div>
                    <div>
                        <label class="text-xs text-base-content/50 uppercase">Alamat</label>
                        <p class="text-sm">{{ $surat->pemohon->penduduk->alamat ?? '-' }}</p>
                    </div>
                    <x-ui.button type="ghost" size="sm" class="w-full"
                        href="{{ route('rtrw.warga.show', $surat->pemohon->penduduk) }}">
                        Lihat Detail Warga
                    </x-ui.button>
                    @endif
                </div>
                @else
                <div class="text-center py-4 text-base-content/50">
                    <p>Data pemohon tidak tersedia.</p>
                </div>
                @endif
            </x-ui.card>
        </div>
    </div>

</x-layouts.app>
