<x-layouts.app :title="'Profil RT'">
    <x-slot:header>
        <x-layouts.page-header title="Profil Wilayah RT" description="Daftar profil dan biodata RT">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('master.profil-wilayah.rw.index') }}">
                    Daftar RW
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    {{-- Filter --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('master.profil-wilayah.rt.index') }}"
            class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nomor RT, deskripsi, alamat..."
                    value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.select name="rw_id" placeholder="Semua RW" :options="$rwFilter
                    ->mapWithKeys(fn($r) => [$r->id => 'RW ' . str_pad($r->nomor, 3, '0', STR_PAD_LEFT)])
                    ->toArray()"
                    selected="{{ request('rw_id') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md" :isSubmit="true">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md"
                    href="{{ route('master.profil-wilayah.rt.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Daftar RT --}}
    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>RT / RW</th>
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
                                {{ str_pad($rt->rw->nomor, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td>{{ $rt->luas_area ? $rt->luas_area . ' km²' : '-' }}</td>
                            <td class="text-sm">{{ $rt->alamat_pos ?? '-' }}</td>
                            <td>{{ $rt->no_telp ?? '-' }}</td>
                            <td>
                                @if ($rt->foto)
                                    <div class="avatar">
                                        <div class="w-10 h-10 rounded">
                                            <img src="{{ asset('storage/' . $rt->foto) }}"
                                                alt="">
                                        </div>
                                    </div>
                                @else
                                    <span class="text-base-content/30 text-xs">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <x-ui.button type="ghost" size="xs"
                                        href="{{ route('master.profil-wilayah.rt.show', $rt) }}">
                                        Detail
                                    </x-ui.button>
                                    <x-ui.button type="primary" size="xs" :outline="true"
                                        href="{{ route('master.profil-wilayah.rt.edit', $rt) }}">
                                        Edit
                                    </x-ui.button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-base-content/60">Tidak ada data RT.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rtList->hasPages())
            <div class="mt-4">{{ $rtList->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
