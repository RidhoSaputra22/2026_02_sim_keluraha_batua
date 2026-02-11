<x-layouts.app :title="'Data Pegawai & Staff'">
    <x-slot:header>
        <x-layouts.page-header title="Data Pegawai & Staff" description="Kelola data pegawai dan staf kelurahan">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('admin.pegawai.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Pegawai
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('admin.pegawai.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nama, NIP, NIK, jabatan..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.select name="status_pegawai" placeholder="Semua Status" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'pensiun' => 'Pensiun', 'mutasi' => 'Mutasi']" selected="{{ request('status_pegawai') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('admin.pegawai.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama / NIP</th>
                        <th>Jabatan</th>
                        <th>Pangkat / Gol.</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawai as $p)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $p->no_urut ?? $loop->iteration }}</td>
                        <td>
                            <div class="font-medium">{{ $p->nama }}</div>
                            <div class="text-xs text-base-content/60">NIP: {{ $p->nip ?? '-' }}</div>
                        </td>
                        <td class="text-sm">{{ $p->jabatan ?? '-' }}</td>
                        <td class="text-sm">
                            {{ $p->pangkat ?? '-' }}
                            @if($p->gol)
                                <span class="text-base-content/60">({{ $p->gol }})</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusClass = match($p->status_pegawai) {
                                    'aktif' => 'badge-success',
                                    'nonaktif' => 'badge-error',
                                    'pensiun' => 'badge-warning',
                                    'mutasi' => 'badge-info',
                                    default => 'badge-ghost',
                                };
                            @endphp
                            <span class="badge {{ $statusClass }} badge-sm">{{ ucfirst($p->status_pegawai ?? '-') }}</span>
                        </td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs" href="{{ route('admin.pegawai.edit', $p) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <form method="POST" action="{{ route('admin.pegawai.destroy', $p) }}" onsubmit="return confirm('Hapus data pegawai ini?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="error" size="xs" :outline="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-8 text-base-content/60">Tidak ada data pegawai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pegawai->hasPages())
            <div class="mt-4">{{ $pegawai->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
