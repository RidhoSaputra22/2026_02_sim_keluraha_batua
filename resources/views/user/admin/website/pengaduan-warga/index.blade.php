<x-layouts.app title="Pengaduan Warga">
    <x-layouts.page-header title="Pengaduan Warga" description="Pantau laporan yang dikirim melalui halaman pengaduan website publik" />

    <x-ui.card class="mt-6 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <x-ui.input name="search" label="Cari" placeholder="Kode, nama, atau subjek" value="{{ request('search') }}"
                class="w-72" />
            <x-ui.select name="kategori" label="Kategori" :options="['' => 'Semua'] + \App\Models\PengaduanWarga::kategoriOptions()"
                selected="{{ request('kategori') }}" />
            <x-ui.select name="status" label="Status" :options="['' => 'Semua'] + \App\Models\PengaduanWarga::statusOptions()"
                selected="{{ request('status') }}" />
            <x-ui.button type="primary" size="sm">Filter</x-ui.button>
            <a href="{{ route('admin.website.pengaduan-warga.index') }}" class="btn btn-ghost btn-sm">Reset</a>
        </form>
    </x-ui.card>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pelapor</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengaduanWarga as $item)
                        <tr>
                            <td class="font-mono text-sm">{{ $item->kode_pengaduan }}</td>
                            <td>
                                <div class="font-semibold">{{ $item->nama }}</div>
                                <div class="text-xs text-base-content/60">{{ $item->subjek }}</div>
                            </td>
                            <td>{{ \App\Models\PengaduanWarga::kategoriOptions()[$item->kategori] ?? ucfirst($item->kategori) }}</td>
                            <td>
                                <x-ui.badge :type="match($item->status) {
                                    'baru' => 'warning',
                                    'ditinjau' => 'info',
                                    'diproses' => 'primary',
                                    'selesai' => 'success',
                                    default => 'ghost',
                                }" size="sm">
                                    {{ \App\Models\PengaduanWarga::statusOptions()[$item->status] ?? ucfirst($item->status) }}
                                </x-ui.badge>
                            </td>
                            <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                            <td class="flex gap-1">
                                <x-ui.button href="{{ route('admin.website.pengaduan-warga.show', $item) }}" size="xs" type="info">Detail</x-ui.button>
                                <x-ui.confirm-delete :action="route('admin.website.pengaduan-warga.destroy', $item)" size="xs" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-base-content/60">Belum ada pengaduan warga.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $pengaduanWarga->links() }}</div>
    </x-ui.card>
</x-layouts.app>
