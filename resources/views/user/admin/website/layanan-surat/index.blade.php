<x-layouts.app title="Layanan Surat">
    <x-layouts.page-header title="Layanan Surat" description="Kelola daftar persyaratan surat online yang tampil ke warga">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.website.layanan-surat.create') }}" type="primary">Tambah Layanan</x-ui.button>
        </x-slot:actions>
    </x-layouts.page-header>

    <x-ui.card class="mt-6 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <x-ui.input name="search" label="Cari" placeholder="Nama layanan atau deskripsi" value="{{ request('search') }}"
                class="w-72" />
            <x-ui.select name="status" label="Status" :options="['' => 'Semua', 'active' => 'Aktif', 'inactive' => 'Nonaktif']"
                selected="{{ request('status') }}" />
            <x-ui.button type="primary" size="sm">Filter</x-ui.button>
            <a href="{{ route('admin.website.layanan-surat.index') }}" class="btn btn-ghost btn-sm">Reset</a>
        </form>
    </x-ui.card>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Layanan</th>
                        <th>Persyaratan</th>
                        <th>Biaya</th>
                        <th>Status</th>
                        <th>Urutan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($layananSurat as $item)
                        <tr>
                            <td>
                                <div class="font-semibold">{{ $item->nama }}</div>
                                <div class="text-xs text-base-content/60">{{ $item->estimasi_layanan ?: 'Tanpa estimasi' }}</div>
                            </td>
                            <td>{{ $item->persyaratans_count }}</td>
                            <td>{{ $item->biaya ?: '-' }}</td>
                            <td>
                                <x-ui.badge :type="$item->is_active ? 'success' : 'ghost'" size="sm">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </x-ui.badge>
                            </td>
                            <td>{{ $item->sort_order ?? '-' }}</td>
                            <td class="flex gap-1">
                                <x-ui.button href="{{ route('admin.website.layanan-surat.edit', $item) }}" size="xs" type="warning">Edit</x-ui.button>
                                <x-ui.confirm-delete :action="route('admin.website.layanan-surat.destroy', $item)" size="xs" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-base-content/60">Belum ada layanan surat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $layananSurat->links() }}</div>
    </x-ui.card>
</x-layouts.app>
