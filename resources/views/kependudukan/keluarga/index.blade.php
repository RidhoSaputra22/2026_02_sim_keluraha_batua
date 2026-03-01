<x-layouts.app :title="'Data Keluarga'">
    <x-slot:header>
        <x-layouts.page-header title="Data Kartu Keluarga" description="Kelola data kartu keluarga">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('kependudukan.keluarga.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah KK
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('kependudukan.keluarga.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari No. KK, nama kepala keluarga, NIK..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-32">
                <x-ui.select name="rw" placeholder="Semua RW" :options="$rwList->mapWithKeys(fn($r) => [$r->id => 'RW ' . $r->nomor])->toArray()" selected="{{ request('rw') }}" />
            </div>
            <div class="w-full md:w-32">
                <x-ui.select name="rt" placeholder="Semua RT" :options="$rtList->mapWithKeys(fn($r) => [$r->id => 'RT ' . $r->nomor . '/RW ' . ($r->rw->nomor ?? '-')])->toArray()" selected="{{ request('rt') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('kependudukan.keluarga.index') }}">Reset</x-ui.button>
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
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keluarga as $kk)
                    <tr class="hover">
                        <td class="font-mono text-sm">{{ $kk->no_kk }}</td>
                        <td>
                            <div class="font-medium">{{ $kk->kepalaKeluarga->nama ?? '-' }}</div>
                            <div class="text-xs text-base-content/60">NIK: {{ $kk->kepalaKeluarga->nik ?? '-' }}</div>
                        </td>
                        <td>{{ $kk->jumlah_anggota_keluarga ?? '-' }} orang</td>
                        <td class="text-sm">{{ $kk->rt->nomor ?? '-' }}/{{ $kk->rt->rw->nomor ?? '-' }}</td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="info" size="xs" :outline="true" href="{{ route('kependudukan.keluarga.show', $kk) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </x-ui.button>
                                <x-ui.button type="ghost" size="xs" href="{{ route('kependudukan.keluarga.edit', $kk) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <form method="POST" action="{{ route('kependudukan.keluarga.destroy', $kk) }}" onsubmit="return confirm('Hapus data keluarga ini?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="error" size="xs" :outline="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-8 text-base-content/60">Tidak ada data keluarga.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($keluarga->hasPages())
            <div class="mt-4">{{ $keluarga->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
