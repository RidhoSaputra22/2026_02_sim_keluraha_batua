<x-layouts.app :title="'Surat Pengantar'">

    <x-slot:header>
        <x-layouts.page-header
            title="Surat Pengantar"
            description="Daftar surat dari warga di wilayah Anda"
        />
    </x-slot:header>

    {{-- Filter --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('rtrw.pengantar.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <x-ui.input label="Cari" name="search" placeholder="Cari nomor surat, perihal, atau nama pemohon..." value="{{ request('search') }}" />
            </div>
            <div class="w-40">
                <x-ui.select label="Status" name="status" :options="['' => 'Semua', 'draft' => 'Draft', 'proses' => 'Proses', 'signed' => 'Selesai', 'reject' => 'Ditolak']" selected="{{ request('status') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="sm" isSubmit>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    Cari
                </x-ui.button>
                <x-ui.button type="ghost" size="sm" href="{{ route('rtrw.pengantar.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Table --}}
    <x-ui.card title="Daftar Surat ({{ $surats->total() }})">
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Nomor Surat</th>
                        <th>Jenis</th>
                        <th>Pemohon</th>
                        <th>Perihal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($surats as $idx => $surat)
                    @php
                        $statusMap = [
                            'draft' => 'warning',
                            'proses' => 'info',
                            'signed' => 'success',
                            'reject' => 'error',
                        ];
                        $badgeType = $statusMap[$surat->status_esign] ?? 'warning';
                    @endphp
                    <tr class="hover">
                        <td class="text-xs text-base-content/50">{{ $surats->firstItem() + $idx }}</td>
                        <td class="text-sm">{{ $surat->tanggal_surat?->format('d/m/Y') ?? '-' }}</td>
                        <td class="font-mono text-xs">{{ $surat->nomor_surat ?? '-' }}</td>
                        <td class="text-sm">{{ $surat->jenis?->nama ?? '-' }}</td>
                        <td class="font-medium">{{ $surat->pemohon?->nama ?? $surat->nama_dalam_surat ?? '-' }}</td>
                        <td class="text-sm max-w-[200px] truncate">{{ $surat->perihal ?? '-' }}</td>
                        <td>
                            <x-ui.badge type="{{ $badgeType }}" size="xs">{{ ucfirst($surat->status_esign ?? 'Draft') }}</x-ui.badge>
                        </td>
                        <td>
                            <x-ui.button type="ghost" size="xs" href="{{ route('rtrw.pengantar.show', $surat) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                Detail
                            </x-ui.button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <div class="text-base-content/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <p>Belum ada data surat untuk wilayah Anda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($surats->hasPages())
        <div class="mt-4">
            {{ $surats->links() }}
        </div>
        @endif
    </x-ui.card>

</x-layouts.app>
