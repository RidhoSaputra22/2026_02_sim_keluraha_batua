<x-layouts.app :title="'Daftar Ekspedisi Surat'">
    <x-slot:header>
        <x-layouts.page-header title="Ekspedisi Surat" description="Kelola data ekspedisi surat masuk & keluar">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('ekspedisi.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Ekspedisi
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        @php
            $totalEkspedisi = \App\Models\Ekspedisi::count();
        @endphp
        <x-ui.stat title="Total Ekspedisi" :value="$totalEkspedisi" description="Semua data ekspedisi" />
    </div>

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('ekspedisi.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari ekspedisi, pemilik usaha, alamat..." value="{{ request('search') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('ekspedisi.index') }}">Reset</x-ui.button>
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
                        <th>Ekspedisi</th>
                        <th>Pemilik Usaha</th>
                        <th>Alamat</th>
                        <th>Penanggung Jawab</th>
                        <th>Telp/HP</th>
                        <th>Kegiatan</th>
                        <th class="w-32 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ekspedisiList as $item)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $ekspedisiList->firstItem() + $loop->index }}</td>
                        <td class="font-medium">{{ $item->ekspedisi }}</td>
                        <td>{{ $item->pemilik_usaha }}</td>
                        <td class="text-sm">{{ Str::limit($item->alamat, 40) }}</td>
                        <td>{{ $item->penanggung_jawab ?? '-' }}</td>
                        <td class="text-sm">{{ $item->telp_hp ?? '-' }}</td>
                        <td class="text-sm">{{ Str::limit($item->kegiatan_ekspedisi, 30) }}</td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs" href="{{ route('ekspedisi.edit', $item) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <form method="POST" action="{{ route('ekspedisi.destroy', $item) }}" onsubmit="return confirm('Hapus data ekspedisi ini?')">
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
                        <td colspan="8" class="text-center py-8 text-base-content/50">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                <p>Belum ada data ekspedisi</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ekspedisiList->hasPages())
            <div class="mt-4">{{ $ekspedisiList->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
