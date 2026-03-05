<x-layouts.app :title="'Data RT'">
    <x-slot:header>
        <x-layouts.page-header title="Data RT" description="Kelola data Rukun Tetangga">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('master.rt.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah RT
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    {{-- Filter --}}
    @php
        $rwOptions = $rwFilter->mapWithKeys(fn($r) => [
            $r->id => 'RW ' . str_pad($r->nomor, 3, '0', STR_PAD_LEFT) . ' — ' . ($r->kelurahan->nama ?? ''),
        ])->toArray();
    @endphp
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('master.rt.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nomor RT, deskripsi, alamat..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-48">
                <x-ui.select name="rw_id" placeholder="Semua RW" :options="$rwOptions" selected="{{ request('rw_id') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md" :isSubmit="true">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('master.rt.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Summary --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <x-ui.stat title="Total RT" :value="$totalRt"
            icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>'
            type="primary" />
    </div>

    {{-- Daftar RT --}}
    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>RT / RW</th>
                        <th>Kelurahan</th>
                        <th>Luas Area</th>
                        <th>Alamat Pos</th>
                        <th>Telepon</th>
                        <th>Foto</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rtList as $rt)
                        <tr class="hover">
                            <td class="font-semibold">
                                RT {{ str_pad($rt->nomor, 3, '0', STR_PAD_LEFT) }} / RW
                                {{ str_pad($rt->rw->nomor ?? 0, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="text-sm">{{ $rt->rw->kelurahan->nama ?? '-' }}</td>
                            <td>{{ $rt->luas_area ? $rt->luas_area . ' km²' : '-' }}</td>
                            <td class="text-sm">{{ $rt->alamat_pos ?? '-' }}</td>
                            <td>{{ $rt->no_telp ?? '-' }}</td>
                            <td>
                                @if ($rt->foto)
                                    <div class="avatar">
                                        <div class="w-10 h-10 rounded">
                                            <img src="{{ asset('storage/' . $rt->foto) }}" alt="">
                                        </div>
                                    </div>
                                @else
                                    <span class="text-base-content/30 text-xs">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <x-ui.button type="ghost" size="xs" href="{{ route('master.rt.show', $rt) }}">Detail</x-ui.button>
                                    <x-ui.button type="primary" size="xs" :outline="true" href="{{ route('master.rt.edit', $rt) }}">Edit</x-ui.button>
                                    <x-ui.button type="error" size="xs" :outline="true"
                                        @click="$dispatch('confirm-delete', { action: '{{ route('master.rt.destroy', $rt) }}', message: 'Hapus RT {{ str_pad($rt->nomor, 3, '0', STR_PAD_LEFT) }}? Pastikan tidak ada data penduduk terkait.' })">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </x-ui.button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-base-content/60">
                                Tidak ada data RT. <a href="{{ route('master.rt.create') }}" class="link link-primary">Tambah RT baru</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rtList->hasPages())
            <div class="mt-4">{{ $rtList->links() }}</div>
        @endif
    </x-ui.card>

    <x-ui.confirm-delete />
</x-layouts.app>
