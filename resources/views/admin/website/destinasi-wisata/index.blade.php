<x-layouts.app title="Destinasi Wisata">
    <x-layouts.page-header title="Destinasi Wisata" description="Kelola tempat, kuliner, dan rekomendasi yang tampil di halaman wisata">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.website.destinasi-wisata.create') }}" type="primary">Tambah Destinasi</x-ui.button>
        </x-slot:actions>
    </x-layouts.page-header>

    <x-ui.card class="mt-6 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <x-ui.input name="search" label="Cari" placeholder="Nama atau alamat destinasi" value="{{ request('search') }}"
                class="w-72" />
            <x-ui.select name="kategori" label="Kategori" :options="['' => 'Semua'] + \App\Models\DestinasiWisata::kategoriOptions()"
                selected="{{ request('kategori') }}" />
            <x-ui.select name="status" label="Status" :options="['' => 'Semua', 'published' => 'Tayang', 'draft' => 'Draft']"
                selected="{{ request('status') }}" />
            <x-ui.button type="primary" size="sm">Filter</x-ui.button>
            <a href="{{ route('admin.website.destinasi-wisata.index') }}" class="btn btn-ghost btn-sm">Reset</a>
        </form>
    </x-ui.card>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Unggulan</th>
                        <th>Urutan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($destinasiWisata as $item)
                        <tr>
                            <td>
                                <div class="font-semibold">{{ $item->nama }}</div>
                                <div class="text-xs text-base-content/60">{{ $item->alamat ?: 'Alamat belum diisi' }}</div>
                            </td>
                            <td>
                                <x-ui.badge type="info" size="sm">{{ \App\Models\DestinasiWisata::kategoriOptions()[$item->kategori] ?? ucfirst($item->kategori) }}</x-ui.badge>
                            </td>
                            <td>
                                <x-ui.badge :type="$item->is_published ? 'success' : 'ghost'" size="sm">
                                    {{ $item->is_published ? 'Tayang' : 'Draft' }}
                                </x-ui.badge>
                            </td>
                            <td>{{ $item->is_featured ? 'Ya' : '-' }}</td>
                            <td>{{ $item->sort_order ?? '-' }}</td>
                            <td class="flex gap-1">
                                <x-ui.button href="{{ route('admin.website.destinasi-wisata.edit', $item) }}" size="xs" type="warning">Edit</x-ui.button>
                                <x-ui.confirm-delete :action="route('admin.website.destinasi-wisata.destroy', $item)" size="xs" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-base-content/60">Belum ada destinasi wisata.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $destinasiWisata->links() }}</div>
    </x-ui.card>
</x-layouts.app>
