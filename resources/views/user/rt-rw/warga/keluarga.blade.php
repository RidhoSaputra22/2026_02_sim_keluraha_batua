<x-layouts.app :title="'Kartu Keluarga Wilayah'">
    <x-slot:header>
        <x-layouts.page-header title="Data Keluarga"
            description="Daftar kartu keluarga yang berada di wilayah RT/RW Anda" />
    </x-slot:header>

    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('rtrw.keluarga.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari No. KK atau nama kepala keluarga..."
                    value="{{ request('search') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" :isSubmit="true">Cari</x-ui.button>
                <x-ui.button type="ghost" href="{{ route('rtrw.keluarga.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>No. KK</th>
                        <th>Kepala Keluarga</th>
                        <th>Jumlah Anggota</th>
                        <th>RT/RW</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($keluarga as $kk)
                        <tr class="hover">
                            <td class="font-mono text-sm">{{ $kk->no_kk }}</td>
                            <td>
                                <div class="font-medium">{{ $kk->kepalaKeluarga->nama ?? '-' }}</div>
                                <div class="text-xs text-base-content/60">
                                    NIK: {{ $kk->kepalaKeluarga->nik ?? '-' }}
                                </div>
                            </td>
                            <td>{{ $kk->jumlah_anggota_keluarga ?? '-' }} orang</td>
                            <td class="text-sm">
                                RT {{ str_pad((string) ($kk->rt->nomor ?? '-'), 2, '0', STR_PAD_LEFT) }}
                                / RW {{ str_pad((string) ($kk->rt->rw->nomor ?? '-'), 2, '0', STR_PAD_LEFT) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-base-content/60">Tidak ada data keluarga.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($keluarga->hasPages())
            <div class="mt-4">{{ $keluarga->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
