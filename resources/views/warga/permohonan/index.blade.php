<x-layouts.app :title="'Permohonan Saya'">

    <x-slot:header>
        <x-layouts.page-header title="Permohonan Saya" description="Daftar semua permohonan surat Anda">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('warga.permohonan.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Ajukan Baru
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

    {{-- Filter --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('warga.permohonan.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.select name="status" placeholder="Semua Status" :options="[
                    'draft' => 'Menunggu Verifikasi',
                    'proses' => 'Menunggu TTD',
                    'signed' => 'Selesai',
                    'reject' => 'Ditolak',
                ]" selected="{{ request('status') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Filter</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('warga.permohonan.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Table --}}
    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor Surat</th>
                        <th>Jenis Surat</th>
                        <th>Perihal</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($surats as $i => $surat)
                        <tr class="hover">
                            <td>{{ $surats->firstItem() + $i }}</td>
                            <td class="font-mono text-sm">{{ $surat->nomor_surat }}</td>
                            <td class="font-medium">{{ $surat->jenis->nama ?? '-' }}</td>
                            <td class="text-sm max-w-48 truncate">{{ $surat->perihal }}</td>
                            <td class="text-sm">{{ $surat->tgl_input?->format('d/m/Y') }}</td>
                            <td>
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
                            </td>
                            <td>
                                <x-ui.button type="ghost" size="xs" href="{{ route('warga.permohonan.show', $surat) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    Detail
                                </x-ui.button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-base-content/60 py-8">
                                Belum ada permohonan. <a href="{{ route('warga.permohonan.create') }}" class="link link-primary">Ajukan sekarang</a>
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
