<x-layouts.app :title="'Data Sekolah'">
    <x-slot:header>
        <x-layouts.page-header title="Data Sekolah" description="Kelola data sekolah di wilayah kelurahan">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('data-umum.sekolah.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Sekolah
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('data-umum.sekolah.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nama sekolah, NPSN, alamat..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.select name="jenjang" placeholder="Semua Jenjang" :options="$jenjangList->mapWithKeys(fn($j) => [$j => $j])->toArray()" selected="{{ request('jenjang') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.select name="status" placeholder="Semua Status" :options="['Negeri' => 'Negeri', 'Swasta' => 'Swasta']" selected="{{ request('status') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('data-umum.sekolah.index') }}">Reset</x-ui.button>
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
                        <th>Nama Sekolah</th>
                        <th>NPSN</th>
                        <th>Jenjang</th>
                        <th>Status</th>
                        <th>Siswa</th>
                        <th>Guru</th>
                        <th>R. Kelas</th>
                        <th class="w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sekolahList as $item)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $sekolahList->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="font-medium">{{ $item->nama_sekolah }}</div>
                            <div class="text-xs text-base-content/60">{{ Str::limit($item->alamat, 35) }}</div>
                        </td>
                        <td class="text-sm">{{ $item->npsn ?? '-' }}</td>
                        <td><span class="badge badge-outline badge-sm">{{ $item->jenjang ?? '-' }}</span></td>
                        <td class="text-sm">{{ $item->status ?? '-' }}</td>
                        <td class="text-sm text-center">{{ number_format($item->jumlah_siswa ?? 0) }}</td>
                        <td class="text-sm text-center">{{ $item->jumlah_guru ?? 0 }}</td>
                        <td class="text-sm text-center">{{ $item->ruang_kelas ?? 0 }}</td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs" href="{{ route('data-umum.sekolah.edit', $item) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <form method="POST" action="{{ route('data-umum.sekolah.destroy', $item) }}" onsubmit="return confirm('Hapus data sekolah ini?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="error" size="xs" :outline="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-base-content/50">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                <p>Belum ada data sekolah</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sekolahList->hasPages())
            <div class="mt-4">{{ $sekolahList->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
