<x-layouts.app :title="'Verifikasi Surat'">
    <x-slot:header>
        <x-layouts.page-header title="Verifikasi Surat" description="Verifikasi dan validasi permohonan surat">
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif
    @if(session('error'))
        <x-ui.alert type="error" class="mb-4">{{ session('error') }}</x-ui.alert>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        @php
            $pendingDraft = \App\Models\Surat::where('status_esign', 'draft')->count();
            $pendingProses = \App\Models\Surat::where('status_esign', 'proses')->count();
            $totalReject = \App\Models\Surat::where('status_esign', 'reject')->count();
        @endphp
        <x-ui.stat title="Menunggu Verifikasi" :value="$pendingDraft" description="Status: Draft" />
        <x-ui.stat title="Sudah Diverifikasi" :value="$pendingProses" description="Menunggu TTD" />
        <x-ui.stat title="Ditolak" :value="$totalReject" description="Perlu perbaikan" />
    </div>

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('persuratan.verifikasi.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nomor surat, perihal, pemohon..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-48">
                <x-ui.select name="status" placeholder="Semua Status" :options="['draft' => 'Menunggu Verifikasi', 'proses' => 'Sudah Diverifikasi']" selected="{{ request('status') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('persuratan.verifikasi.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Table --}}
    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th class="w-12">No</th>
                        <th>Nomor Surat</th>
                        <th>Jenis / Perihal</th>
                        <th>Pemohon</th>
                        <th>Petugas Input</th>
                        <th>Tanggal Input</th>
                        <th class="text-center">Status</th>
                        <th class="w-48 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($surats as $s)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $surats->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="font-mono text-sm font-medium">{{ $s->nomor_surat }}</div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $s->jenis->nama ?? '-' }}</div>
                            <div class="text-xs text-base-content/60">{{ Str::limit($s->perihal, 40) }}</div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $s->pemohon->nama ?? '-' }}</div>
                            <div class="text-xs text-base-content/60">{{ $s->pemohon->penduduk->nik ?? '-' }}</div>
                        </td>
                        <td class="text-sm">{{ $s->petugasInput->name ?? '-' }}</td>
                        <td class="text-sm">{{ $s->tgl_input?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="text-center">
                            @php
                                $statusClass = match($s->status_esign) {
                                    'draft' => 'badge-warning',
                                    'proses' => 'badge-info',
                                    default => 'badge-ghost',
                                };
                                $statusLabel = match($s->status_esign) {
                                    'draft' => 'Menunggu',
                                    'proses' => 'Terverifikasi',
                                    default => '-',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }} badge-sm">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            <div class="flex justify-end gap-1">
                                @if($s->status_esign === 'draft')
                                    {{-- Approve --}}
                                    <form method="POST" action="{{ route('persuratan.verifikasi.approve', $s) }}" onsubmit="return confirm('Verifikasi surat ini?')">
                                        @csrf
                                        <x-ui.button type="success" size="xs">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            Verifikasi
                                        </x-ui.button>
                                    </form>
                                    {{-- Reject --}}
                                    <button class="btn btn-error btn-xs btn-outline" onclick="document.getElementById('reject-modal-{{ $s->id }}').showModal()">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        Tolak
                                    </button>

                                    {{-- Reject Modal --}}
                                    <dialog id="reject-modal-{{ $s->id }}" class="modal">
                                        <div class="modal-box">
                                            <h3 class="font-bold text-lg">Tolak Surat #{{ $s->nomor_surat }}</h3>
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
                                @else
                                    <span class="text-xs text-base-content/40">Sudah diverifikasi</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-base-content/50">
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
        <div class="mt-4">
            {{ $surats->links() }}
        </div>
    </x-ui.card>
</x-layouts.app>
