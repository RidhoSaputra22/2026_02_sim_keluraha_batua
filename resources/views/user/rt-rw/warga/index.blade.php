<x-layouts.app :title="'Data Warga Wilayah'">
    <x-slot:header>
        <x-layouts.page-header title="Data Warga"
            description="Daftar warga yang berada di wilayah RT/RW Anda" />
    </x-slot:header>

    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('rtrw.warga.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nama atau NIK..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.select name="jenis_kelamin" placeholder="Semua JK"
                    :options="['L' => 'Laki-laki', 'P' => 'Perempuan']"
                    selected="{{ request('jenis_kelamin') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" :isSubmit="true">Cari</x-ui.button>
                <x-ui.button type="ghost" href="{{ route('rtrw.warga.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>JK</th>
                        <th>KK</th>
                        <th>RT/RW</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penduduk as $warga)
                        <tr class="hover">
                            <td class="font-mono text-sm">{{ $warga->nik }}</td>
                            <td>
                                <div class="font-medium">{{ $warga->nama }}</div>
                                <div class="text-xs text-base-content/60">{{ $warga->alamat ?? '-' }}</div>
                            </td>
                            <td>
                                <x-ui.badge type="{{ $warga->jenis_kelamin === 'L' ? 'info' : 'secondary' }}" size="xs">
                                    {{ $warga->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </x-ui.badge>
                            </td>
                            <td class="font-mono text-sm">{{ $warga->keluarga->no_kk ?? '-' }}</td>
                            <td class="text-sm">
                                RT {{ str_pad((string) ($warga->rt->nomor ?? '-'), 2, '0', STR_PAD_LEFT) }}
                                / RW {{ str_pad((string) ($warga->rt->rw->nomor ?? '-'), 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    <x-ui.button type="info" size="xs" :outline="true"
                                        href="{{ route('rtrw.warga.show', $warga) }}">
                                        Detail
                                    </x-ui.button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-base-content/60">Tidak ada data warga.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($penduduk->hasPages())
            <div class="mt-4">{{ $penduduk->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
