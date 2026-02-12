<x-layouts.app :title="'Dashboard Verifikator'">

    <x-slot:header>
        <x-layouts.page-header
            title="Dashboard Verifikator"
            description="Verifikasi dan validasi permohonan surat"
        />
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif
    @if(session('error'))
        <x-ui.alert type="error" class="mb-4">{{ session('error') }}</x-ui.alert>
    @endif

    {{-- Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.card class="bg-warning/5">
            <x-ui.stat title="Menunggu Verifikasi" value="{{ $suratMenungguVerifikasi }}" description="Perlu ditinjau">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-success/5">
            <x-ui.stat title="Disetujui Hari Ini" value="{{ $suratDisetujuiHariIni }}" description="Approved">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-error/5">
            <x-ui.stat title="Ditolak Hari Ini" value="{{ $suratDitolakHariIni }}" description="Rejected">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>

        <x-ui.card class="bg-info/5">
            <x-ui.stat title="Perlu Perbaikan" value="{{ $suratPerluPerbaikan }}" description="Dikembalikan ke operator">
                <x-slot:icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </x-slot:icon>
            </x-ui.stat>
        </x-ui.card>
    </div>

    {{-- Antrian Verifikasi + Sidebar --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-ui.card title="Antrian Verifikasi">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Surat</th>
                                <th>Pemohon</th>
                                <th>Operator</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($antrian as $s)
                            <tr class="hover">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $s->jenis->nama ?? '-' }}</td>
                                <td>
                                    <div class="font-medium">{{ $s->pemohon->nama ?? '-' }}</div>
                                    <div class="text-xs text-base-content/60">{{ $s->pemohon->penduduk->nik ?? '-' }}</div>
                                </td>
                                <td class="text-xs">{{ $s->petugasInput->name ?? '-' }}</td>
                                <td class="text-sm">{{ $s->tgl_input?->format('d M Y') ?? '-' }}</td>
                                <td>
                                    <div class="flex gap-1">
                                        {{-- Approve --}}
                                        <form method="POST" action="{{ route('persuratan.verifikasi.approve', $s) }}" onsubmit="return confirm('Verifikasi surat ini?')">
                                            @csrf
                                            <x-ui.button type="success" size="xs">Setujui</x-ui.button>
                                        </form>
                                        {{-- Reject --}}
                                        <button class="btn btn-error btn-xs btn-outline" onclick="document.getElementById('reject-modal-{{ $s->id }}').showModal()">Tolak</button>

                                        {{-- Reject Modal --}}
                                        <dialog id="reject-modal-{{ $s->id }}" class="modal">
                                            <div class="modal-box">
                                                <h3 class="font-bold text-lg">Tolak Surat — {{ $s->jenis->nama ?? '' }}</h3>
                                                <p class="text-sm text-base-content/60 mt-1">Pemohon: {{ $s->pemohon->nama ?? '-' }}</p>
                                                <form method="POST" action="{{ route('persuratan.verifikasi.reject', $s) }}">
                                                    @csrf
                                                    <div class="mt-4">
                                                        <x-ui.textarea label="Alasan Penolakan" name="catatan_verifikasi" placeholder="Jelaskan alasan penolakan..." rows="3" required />
                                                    </div>
                                                    <div class="modal-action">
                                                        <button type="button" class="btn btn-ghost" onclick="this.closest('dialog').close()">Batal</button>
                                                        <button type="submit" class="btn btn-error">Tolak Surat</button>
                                                    </div>
                                                </form>
                                            </div>
                                            <form method="dialog" class="modal-backdrop"><button>close</button></form>
                                        </dialog>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-base-content/50">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <p>Tidak ada surat yang perlu diverifikasi.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($antrian->count() > 0)
                <x-slot:actions>
                    <x-ui.button type="ghost" size="sm" href="{{ route('persuratan.verifikasi.index') }}">Lihat Semua →</x-ui.button>
                </x-slot:actions>
                @endif
            </x-ui.card>
        </div>

        {{-- Sidebar: Kinerja + Riwayat --}}
        <div class="space-y-6">
            <x-ui.card title="Kinerja Bulan Ini">
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>Total Diverifikasi</span>
                            <span class="font-bold">{{ $totalVerifikasiBulanIni }}</span>
                        </div>
                        <progress class="progress progress-primary w-full" value="{{ min($totalVerifikasiBulanIni, 100) }}" max="100"></progress>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-center">
                        <div class="bg-success/10 rounded-lg p-2">
                            <div class="text-lg font-bold text-success">{{ $disetujuiBulanIni }}</div>
                            <div class="text-xs">Disetujui</div>
                        </div>
                        <div class="bg-error/10 rounded-lg p-2">
                            <div class="text-lg font-bold text-error">{{ $ditolakBulanIni }}</div>
                            <div class="text-xs">Ditolak</div>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Riwayat Verifikasi Terbaru">
                <div class="space-y-3">
                    @forelse($riwayat as $r)
                    <div class="flex items-center gap-3">
                        @if($r->status_esign === 'proses' || $r->status_esign === 'signed')
                            <x-ui.badge type="success" size="xs">✓</x-ui.badge>
                        @else
                            <x-ui.badge type="error" size="xs">✕</x-ui.badge>
                        @endif
                        <div>
                            <p class="text-sm font-medium">{{ $r->jenis->nama ?? '-' }} — {{ Str::limit($r->pemohon->nama ?? '-', 15) }}</p>
                            <p class="text-xs text-base-content/60">
                                {{ $r->status_esign === 'reject' ? 'Ditolak' : 'Disetujui' }}, {{ $r->tgl_verifikasi?->format('H:i') }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-base-content/50 text-center py-4">Belum ada riwayat verifikasi.</p>
                    @endforelse
                </div>
            </x-ui.card>

            {{-- Quick Link --}}
            <x-ui.card>
                <div class="space-y-2">
                    <x-ui.button type="primary" size="sm" class="w-full" href="{{ route('persuratan.verifikasi.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                        Halaman Verifikasi Lengkap
                    </x-ui.button>
                    <x-ui.button type="ghost" size="sm" class="w-full" href="{{ route('persuratan.tracking.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        Tracking Layanan
                    </x-ui.button>
                </div>
            </x-ui.card>
        </div>
    </div>

</x-layouts.app>
