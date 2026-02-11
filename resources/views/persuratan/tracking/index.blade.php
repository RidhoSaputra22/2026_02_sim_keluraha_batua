<x-layouts.app :title="'Tracking Layanan'">
    <x-slot:header>
        <x-layouts.page-header title="Tracking Layanan" description="Lacak status permohonan surat berdasarkan nomor surat, nama pemohon, atau NIK">
        </x-layouts.page-header>
    </x-slot:header>

    {{-- Search --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('persuratan.tracking.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Masukkan nomor surat, nama pemohon, atau NIK..." value="{{ $search ?? '' }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    Lacak
                </x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('persuratan.tracking.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    @if($search && $surat && $surat->count() > 0)
        {{-- Results --}}
        @foreach($surat as $s)
        <x-ui.card class="mb-4">
            <div class="flex flex-col lg:flex-row gap-6">
                {{-- Left: Info --}}
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <h3 class="text-lg font-bold font-mono">{{ $s->nomor_surat }}</h3>
                        @php
                            $statusClass = match($s->status_esign) {
                                'draft' => 'badge-warning',
                                'proses' => 'badge-info',
                                'signed' => 'badge-success',
                                'reject' => 'badge-error',
                                default => 'badge-ghost',
                            };
                            $statusLabel = match($s->status_esign) {
                                'draft' => 'Draft / Menunggu Verifikasi',
                                'proses' => 'Diverifikasi / Menunggu TTD',
                                'signed' => 'Selesai / Ditandatangani',
                                'reject' => 'Ditolak',
                                default => '-',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2 text-sm">
                        <div>
                            <span class="text-base-content/60">Jenis Surat:</span>
                            <span class="font-medium ml-1">{{ $s->jenis->nama ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-base-content/60">Perihal:</span>
                            <span class="font-medium ml-1">{{ $s->perihal ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-base-content/60">Pemohon:</span>
                            <span class="font-medium ml-1">{{ $s->pemohon->nama ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-base-content/60">NIK:</span>
                            <span class="font-mono ml-1">{{ $s->pemohon->penduduk->nik ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-base-content/60">Tanggal Surat:</span>
                            <span class="ml-1">{{ $s->tanggal_surat?->format('d/m/Y') ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-base-content/60">Sifat:</span>
                            <span class="ml-1">{{ $s->sifat->nama ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Right: Timeline --}}
                <div class="lg:w-80 border-l border-base-300 pl-6">
                    <h4 class="font-semibold mb-3">Timeline Proses</h4>
                    <ul class="steps steps-vertical text-sm">
                        {{-- Step 1: Draft --}}
                        <li class="step {{ in_array($s->status_esign, ['draft','proses','signed']) ? 'step-primary' : ($s->status_esign === 'reject' ? 'step-error' : '') }}">
                            <div class="text-left">
                                <div class="font-medium">Registrasi</div>
                                <div class="text-xs text-base-content/60">
                                    {{ $s->tgl_input?->format('d/m/Y H:i') ?? '-' }}
                                    @if($s->petugasInput)
                                        — {{ $s->petugasInput->name }}
                                    @endif
                                </div>
                            </div>
                        </li>
                        {{-- Step 2: Verifikasi --}}
                        <li class="step {{ in_array($s->status_esign, ['proses','signed']) ? 'step-primary' : ($s->status_esign === 'reject' && $s->tgl_verifikasi ? 'step-error' : '') }}">
                            <div class="text-left">
                                <div class="font-medium">Verifikasi</div>
                                <div class="text-xs text-base-content/60">
                                    @if($s->tgl_verifikasi)
                                        {{ $s->tgl_verifikasi->format('d/m/Y H:i') }}
                                        @if($s->verifikator)
                                            — {{ $s->verifikator->name }}
                                        @endif
                                    @else
                                        Menunggu...
                                    @endif
                                </div>
                                @if($s->catatan_verifikasi)
                                    <div class="text-xs italic mt-1 text-base-content/50">"{{ $s->catatan_verifikasi }}"</div>
                                @endif
                            </div>
                        </li>
                        {{-- Step 3: TTD --}}
                        <li class="step {{ $s->status_esign === 'signed' ? 'step-primary' : '' }}">
                            <div class="text-left">
                                <div class="font-medium">Tanda Tangan</div>
                                <div class="text-xs text-base-content/60">
                                    @if($s->tgl_ttd)
                                        {{ $s->tgl_ttd->format('d/m/Y H:i') }}
                                        @if($s->penandatanganPejabat?->pegawai)
                                            — {{ $s->penandatanganPejabat->pegawai->nama }}
                                        @endif
                                    @else
                                        Menunggu...
                                    @endif
                                </div>
                                @if($s->catatan_penandatangan)
                                    <div class="text-xs italic mt-1 text-base-content/50">"{{ $s->catatan_penandatangan }}"</div>
                                @endif
                            </div>
                        </li>
                        {{-- Step 4: Selesai --}}
                        <li class="step {{ $s->status_esign === 'signed' ? 'step-success' : '' }}">
                            <div class="text-left">
                                <div class="font-medium">Selesai</div>
                                <div class="text-xs text-base-content/60">
                                    @if($s->status_esign === 'signed')
                                        Surat siap diambil / diunduh
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </x-ui.card>
        @endforeach

        <div class="mt-4">
            {{ $surat->links() }}
        </div>
    @elseif($search)
        <x-ui.card>
            <div class="text-center py-12 text-base-content/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <p class="text-lg">Surat tidak ditemukan</p>
                <p class="text-sm mt-1">Pastikan nomor surat, nama pemohon, atau NIK yang Anda masukkan benar.</p>
            </div>
        </x-ui.card>
    @else
        <x-ui.card>
            <div class="text-center py-12 text-base-content/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                <p class="text-lg">Lacak Status Surat</p>
                <p class="text-sm mt-1">Masukkan nomor surat, nama pemohon, atau NIK pada kolom pencarian di atas.</p>
            </div>
        </x-ui.card>
    @endif
</x-layouts.app>
