<x-layouts.app :title="'Permohonan Surat'">
    <x-slot:header>
        <x-layouts.page-header title="Permohonan Surat" description="Kelola permohonan dan registrasi surat">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('persuratan.permohonan.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Permohonan
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
    <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif
    @if(session('error'))
    <x-ui.alert type="error" class="mb-4">{{ session('error') }}</x-ui.alert>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
        $totalDraft = \App\Models\Surat::where('status_esign', 'draft')->count();
        $totalProses = \App\Models\Surat::where('status_esign', 'proses')->count();
        $totalSigned = \App\Models\Surat::where('status_esign', 'signed')->count();
        $totalReject = \App\Models\Surat::where('status_esign', 'reject')->count();
        @endphp
        <x-ui.stat title="Draft" :value="$totalDraft" description="Menunggu verifikasi" />
        <x-ui.stat title="Proses" :value="$totalProses" description="Menunggu tanda tangan" />
        <x-ui.stat title="Selesai" :value="$totalSigned" description="Sudah ditandatangani" />
        <x-ui.stat title="Ditolak" :value="$totalReject" description="Perlu perbaikan" />
    </div>

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('persuratan.permohonan.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nomor surat, perihal, pemohon, NIK..."
                    value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-48">
                <x-ui.select name="status" placeholder="Semua Status"
                    :options="['draft' => 'Draft', 'proses' => 'Proses', 'signed' => 'Selesai', 'reject' => 'Ditolak']"
                    selected="{{ request('status') }}" />
            </div>
            <div class="w-full md:w-48">
                <x-ui.select name="jenis_id" placeholder="Semua Jenis"
                    :options="$jenisList->pluck('nama', 'id')->toArray()" selected="{{ request('jenis_id') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('persuratan.permohonan.index') }}">Reset
                </x-ui.button>
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
                        <th>Tanggal</th>
                        <th>Sifat</th>
                        <th class="text-center">Status</th>
                        <th class="w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($surats as $s)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $surats->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="font-mono text-sm font-medium">{{ $s->nomor_surat }}</div>
                            <div class="text-xs text-base-content/50">
                                {{ $s->arah === 'masuk' ? 'Surat Masuk' : 'Surat Keluar' }}</div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $s->jenis->nama ?? '-' }}</div>
                            <div class="text-xs text-base-content/60">{{ Str::limit($s->perihal, 40) }}</div>
                        </td>
                        <td>
                            <div class="font-medium">{{ $s->pemohon->nama ?? '-' }}</div>
                            <div class="text-xs text-base-content/60">{{ $s->pemohon->penduduk->nik ?? '-' }}</div>
                        </td>
                        <td class="text-sm">{{ $s->tanggal_surat?->format('d/m/Y') ?? '-' }}</td>
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
                        <td class="text-center">
                            @php
                            $statusClass = match($s->status_esign) {
                            'draft' => 'badge-warning',
                            'proses' => 'badge-info',
                            'signed' => 'badge-success',
                            'reject' => 'badge-error',
                            default => 'badge-ghost',
                            };
                            $statusLabel = match($s->status_esign) {
                            'draft' => 'Draft',
                            'proses' => 'Proses',
                            'signed' => 'Selesai',
                            'reject' => 'Ditolak',
                            default => '-',
                            };
                            @endphp
                            <span class="badge {{ $statusClass }} badge-sm">{{ $statusLabel }}</span>
                        </td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs"
                                    href="{{ route('persuratan.permohonan.edit', $s) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </x-ui.button>
                                @if($s->status_esign === 'draft')
                                <form method="POST" action="{{ route('persuratan.permohonan.destroy', $s) }}"
                                    onsubmit="return confirm('Hapus permohonan surat ini?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="error" size="xs" :outline="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </x-ui.button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-base-content/50">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p>Belum ada permohonan surat.</p>
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
