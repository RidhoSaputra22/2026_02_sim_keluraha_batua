<x-layouts.app title="Kelola Pegawai">
    <x-layouts.page-header title="Kelola Pegawai" subtitle="Data pegawai dan staff kelurahan">
        <x-ui.button href="{{ route('master.pegawai.create') }}" icon="plus" color="primary">
            Tambah Pegawai
        </x-ui.button>
    </x-layouts.page-header>

    {{-- Filter & Search --}}
    <x-ui.card class="mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <x-ui.input name="search" label="Cari" placeholder="Nama atau NIP..."
                value="{{ request('search') }}" class="w-64" />
            <x-ui.select name="status_pegawai" label="Status"
                :options="['' => 'Semua'] + \App\Enums\StatusAktifEnum::options()"
                selected="{{ request('status_pegawai') }}" />
            <x-ui.button type="submit" color="primary" size="sm">Filter</x-ui.button>
            <a href="{{ route('master.pegawai.index') }}" class="btn btn-ghost btn-sm">Reset</a>
        </form>
    </x-ui.card>

    {{-- Table --}}
    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Gol/Pangkat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawai as $i => $p)
                        <tr>
                            <td>{{ $p->no_urut ?? ($pegawai->firstItem() + $i) }}</td>
                            <td class="font-mono text-sm">{{ $p->nip }}</td>
                            <td>{{ $p->nama }}</td>
                            <td>{{ $p->jabatan }}</td>
                            <td>{{ $p->gol }} {{ $p->pangkat ? "/ $p->pangkat" : '' }}</td>
                            <td>
                                <x-ui.badge color="{{ strtolower((string) $p->status_pegawai) === \App\Enums\StatusAktifEnum::AKTIF->value ? 'success' : 'error' }}">
                                    {{ \App\Enums\StatusAktifEnum::labelOf(strtolower((string) $p->status_pegawai)) }}
                                </x-ui.badge>
                            </td>
                            <td class="flex gap-1">
                                <x-ui.button href="{{ route('master.pegawai.edit', $p) }}" size="xs" color="warning">Edit</x-ui.button>
                                <x-ui.confirm-delete :action="route('master.pegawai.destroy', $p)" size="xs" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-base-content/50">Belum ada data pegawai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $pegawai->links() }}</div>
    </x-ui.card>
</x-layouts.app>
