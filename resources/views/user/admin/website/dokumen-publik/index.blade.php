<x-layouts.app title="Dokumen Publik">
    <x-layouts.page-header title="Dokumen Publik" description="Kelola dokumen, formulir, dan berkas unduhan untuk website publik">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.website.dokumen-publik.create') }}" type="primary">Tambah Dokumen</x-ui.button>
        </x-slot:actions>
    </x-layouts.page-header>

    <x-ui.card class="mt-6 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <x-ui.input name="search" label="Cari" placeholder="Judul atau deskripsi dokumen" value="{{ request('search') }}"
                class="w-72" />
            <x-ui.select name="kategori" label="Kategori" :options="['' => 'Semua'] + \App\Models\DokumenPublik::kategoriOptions()"
                selected="{{ request('kategori') }}" />
            <x-ui.select name="status" label="Status" :options="['' => 'Semua', 'published' => 'Tayang', 'draft' => 'Draft']"
                selected="{{ request('status') }}" />
            <x-ui.button type="primary" size="sm">Filter</x-ui.button>
            <a href="{{ route('admin.website.dokumen-publik.index') }}" class="btn btn-ghost btn-sm">Reset</a>
        </form>
    </x-ui.card>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Ukuran</th>
                        <th>Tanggal Publikasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dokumenPublik as $item)
                        <tr>
                            <td>
                                <div class="font-semibold">{{ $item->judul }}</div>
                                <div class="text-xs text-base-content/60">{{ $item->mime_type ?? '-' }}</div>
                            </td>
                            <td>
                                <x-ui.badge type="info" size="sm">{{ \App\Models\DokumenPublik::kategoriOptions()[$item->kategori] ?? ucfirst($item->kategori) }}</x-ui.badge>
                            </td>
                            <td>
                                <x-ui.badge :type="$item->is_published ? 'success' : 'ghost'" size="sm">
                                    {{ $item->is_published ? 'Tayang' : 'Draft' }}
                                </x-ui.badge>
                            </td>
                            <td>{{ $item->file_size ? number_format($item->file_size / 1024 / 1024, 2) . ' MB' : '-' }}</td>
                            <td>{{ $item->published_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td class="flex gap-1">
                                <x-ui.button href="{{ asset('storage/' . $item->file_path) }}" size="xs" type="info">Unduh</x-ui.button>
                                <x-ui.button href="{{ route('admin.website.dokumen-publik.edit', $item) }}" size="xs" type="warning">Edit</x-ui.button>
                                <x-ui.confirm-delete :action="route('admin.website.dokumen-publik.destroy', $item)" size="xs" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-base-content/60">Belum ada dokumen publik.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $dokumenPublik->links() }}</div>
    </x-ui.card>
</x-layouts.app>
