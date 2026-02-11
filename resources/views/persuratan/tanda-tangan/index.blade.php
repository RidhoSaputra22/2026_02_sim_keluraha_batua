<x-layouts.app :title="'Tanda Tangan Surat'">
    <x-slot:header>
        <x-layouts.page-header title="Tanda Tangan Surat" description="Tanda tangani surat yang telah diverifikasi">
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif
    @if(session('error'))
        <x-ui.alert type="error" class="mb-4">{{ session('error') }}</x-ui.alert>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        @php
            $pendingTtd = \App\Models\Surat::where('status_esign', 'proses')->count();
            $totalSigned = \App\Models\Surat::where('status_esign', 'signed')->count();
        @endphp
        <x-ui.stat title="Menunggu Tanda Tangan" :value="$pendingTtd" description="Surat terverifikasi" />
        <x-ui.stat title="Sudah Ditandatangani" :value="$totalSigned" description="Total selesai" />
    </div>

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('persuratan.tanda-tangan.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nomor surat, perihal, pemohon..." value="{{ request('search') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('persuratan.tanda-tangan.index') }}">Reset</x-ui.button>
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
                        <th>Sifat</th>
                        <th>Diverifikasi Oleh</th>
                        <th>Tgl Verifikasi</th>
                        <th class="w-56 text-right">Aksi</th>
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
                        <td>
                            @if($s->sifat)
                                @php
                                    $sifatClass = match($s->sifat->nama) {
                                        'Sangat Segera' => 'badge-error',
                                        'Segera' => 'badge-warning',
                                        'Rahasia' => 'badge-secondary',
                                        default => 'badge-ghost',
                                    };
                                @endphp
                                <span class="badge {{ $sifatClass }} badge-sm">{{ $s->sifat->nama }}</span>
                            @else
                                <span class="text-base-content/40">-</span>
                            @endif
                        </td>
                        <td class="text-sm">{{ $s->verifikator->name ?? '-' }}</td>
                        <td class="text-sm">{{ $s->tgl_verifikasi?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>
                            <div class="flex justify-end gap-1">
                                {{-- Sign --}}
                                <button class="btn btn-success btn-xs" onclick="document.getElementById('sign-modal-{{ $s->id }}').showModal()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    Tanda Tangan
                                </button>
                                {{-- Reject --}}
                                <button class="btn btn-error btn-xs btn-outline" onclick="document.getElementById('reject-ttd-modal-{{ $s->id }}').showModal()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>

                                {{-- Sign Modal --}}
                                <dialog id="sign-modal-{{ $s->id }}" class="modal">
                                    <div class="modal-box">
                                        <h3 class="font-bold text-lg">Tanda Tangani Surat</h3>
                                        <p class="text-sm text-base-content/60 mt-1">{{ $s->nomor_surat }} — {{ $s->perihal }}</p>
                                        <form method="POST" action="{{ route('persuratan.tanda-tangan.sign', $s) }}">
                                            @csrf
                                            <div class="mt-4">
                                                <label class="label"><span class="label-text font-medium">Penandatangan</span></label>
                                                <select name="penandatangan_pejabat_id" class="select select-bordered w-full" required>
                                                    <option value="">Pilih penandatangan...</option>
                                                    @foreach($penandatanganList as $p)
                                                        <option value="{{ $p->id }}">{{ $p->pegawai->nama ?? 'Pejabat #'.$p->id }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mt-4">
                                                <x-ui.textarea label="Catatan (opsional)" name="catatan_penandatangan" placeholder="Catatan penandatangan..." rows="2" />
                                            </div>
                                            <div class="modal-action">
                                                <button type="button" class="btn btn-ghost" onclick="this.closest('dialog').close()">Batal</button>
                                                <button type="submit" class="btn btn-success">Tanda Tangani</button>
                                            </div>
                                        </form>
                                    </div>
                                    <form method="dialog" class="modal-backdrop"><button>close</button></form>
                                </dialog>

                                {{-- Reject Modal --}}
                                <dialog id="reject-ttd-modal-{{ $s->id }}" class="modal">
                                    <div class="modal-box">
                                        <h3 class="font-bold text-lg">Tolak Surat #{{ $s->nomor_surat }}</h3>
                                        <form method="POST" action="{{ route('persuratan.tanda-tangan.reject', $s) }}">
                                            @csrf
                                            <div class="mt-4">
                                                <x-ui.textarea label="Alasan Penolakan" name="catatan_penandatangan" placeholder="Jelaskan alasan penolakan..." rows="3" required />
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
                        <td colspan="8" class="text-center py-8 text-base-content/50">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                <p>Tidak ada surat yang menunggu tanda tangan.</p>
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
