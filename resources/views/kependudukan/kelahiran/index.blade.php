<x-layouts.app :title="'Data Kelahiran'">
    <x-slot:header>
        <x-layouts.page-header title="Data Kelahiran" description="Kelola pencatatan kelahiran penduduk">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('kependudukan.kelahiran.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Kelahiran
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('kependudukan.kelahiran.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nama bayi, nama orang tua, no akte..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.select name="jenis_kelamin" placeholder="Semua JK" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" selected="{{ request('jenis_kelamin') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('kependudukan.kelahiran.index') }}">Reset</x-ui.button>
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
                        <th>Nama Bayi</th>
                        <th>JK</th>
                        <th>Tempat / Tanggal Lahir</th>
                        <th>Nama Ibu</th>
                        <th>Nama Ayah</th>
                        <th>No. Akte</th>
                        <th class="w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelahiran as $k)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $kelahiran->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="font-medium">{{ $k->nama_bayi }}</div>
                            @if($k->jam_lahir)
                                <div class="text-xs text-base-content/60">Jam: {{ $k->jam_lahir }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $k->jenis_kelamin === 'L' ? 'info' : 'secondary' }} badge-sm">
                                {{ $k->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </td>
                        <td class="text-sm">
                            <div>{{ $k->tempat_lahir ?? '-' }}</div>
                            <div class="text-base-content/60">{{ $k->tanggal_lahir->format('d/m/Y') }}</div>
                        </td>
                        <td class="text-sm">{{ $k->ibu->nama ?? '-' }}</td>
                        <td class="text-sm">{{ $k->ayah->nama ?? '-' }}</td>
                        <td class="text-sm">{{ $k->no_akte ?? '-' }}</td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs" href="{{ route('kependudukan.kelahiran.edit', $k) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <form method="POST" action="{{ route('kependudukan.kelahiran.destroy', $k) }}" onsubmit="return confirm('Hapus data kelahiran &quot;{{ $k->nama_bayi }}&quot;?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="error" size="xs" :outline="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-8 text-base-content/60">Tidak ada data kelahiran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($kelahiran->hasPages())
            <div class="mt-4">{{ $kelahiran->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
