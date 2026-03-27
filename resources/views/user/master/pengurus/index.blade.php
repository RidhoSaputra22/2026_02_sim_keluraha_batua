<x-layouts.app :title="'Pengurus RT/RW'">
    <x-slot:header>
        <x-layouts.page-header title="Pengurus RT/RW" description="Kelola data pengurus RT dan RW">
            <x-slot:actions>
                <x-ui.import-export-buttons module="pengurus" />
                <x-ui.button type="primary" size="sm" href="{{ route('master.pengurus.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Pengurus
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if (session('new_user_credential'))
        <div class="alert alert-success mb-4 text-sm font-mono">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('new_user_credential') }}</span>
        </div>
    @endif

    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('master.pengurus.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nama, NIK, no. telp..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-36">
                <x-ui.select name="jabatan" placeholder="Semua Jabatan" :options="$jabatanList->mapWithKeys(fn($j) => [$j->id => $j->nama])->toArray()" selected="{{ request('jabatan') }}" />
            </div>
            <div class="w-full md:w-32">
                <x-ui.select name="rw" placeholder="Semua RW" :options="$rwList->mapWithKeys(fn($r) => [$r->id => 'RW ' . $r->nomor])->toArray()" selected="{{ request('rw') }}" />
            </div>
            <div class="w-full md:w-36">
                <x-ui.select name="status" placeholder="Semua Status" :options="\App\Enums\StatusAktifEnum::options()" selected="{{ request('status') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md" :isSubmit="true">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('master.pengurus.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <x-ui.stat title="Total RT" :value="$totalRT" icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>' type="primary" />
        <x-ui.stat title="Total RW" :value="$totalRW" icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>' type="secondary" />
        <x-ui.stat title="Aktif" :value="$totalAktif" icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' type="success" />
        <x-ui.stat title="Nonaktif" :value="$totalNonaktif" icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' type="error" />
    </div>

    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Jabatan</th>
                        <th>RT/RW</th>
                        <th>Tgl Mulai</th>
                        <th>Kontak</th>
                        <th>Akun</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengurus as $p)
                    <tr class="hover">
                        <td class="font-medium">{{ $p->penduduk->nama ?? '-' }}</td>
                        <td class="font-mono text-xs">{{ $p->penduduk->nik ?? '-' }}</td>
                        <td>
                            <x-ui.badge type="primary" size="sm">{{ $p->jabatan->nama ?? '-' }}</x-ui.badge>
                        </td>
                        <td class="text-sm">RT {{ $p->rt->nomor ?? '-' }} / RW {{ $p->rw->nomor ?? '-' }}</td>
                        <td class="text-sm">
                            {{ $p->tgl_mulai ? \Carbon\Carbon::parse($p->tgl_mulai)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-sm">{{ $p->no_telp ?? '-' }}</td>
                        <td>
                            @if($p->user)
                                <x-ui.badge type="info" size="sm">{{ $p->user->email }}</x-ui.badge>
                            @else
                                <span class="text-xs text-base-content/40">—</span>
                            @endif
                        </td>
                        <td>
                            <x-ui.badge :type="strtolower((string) ($p->status ?? '')) === \App\Enums\StatusAktifEnum::AKTIF->value ? 'success' : 'error'" size="sm">
                                {{ \App\Enums\StatusAktifEnum::labelOf(strtolower((string) ($p->status ?? \App\Enums\StatusAktifEnum::AKTIF->value))) }}
                            </x-ui.badge>
                        </td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs" href="{{ route('master.pengurus.edit', $p) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <x-ui.button type="error" size="xs" :outline="true" @click="$dispatch('confirm-delete', { action: '{{ route('master.pengurus.destroy', $p) }}', message: 'Hapus data pengurus ini?' })">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </x-ui.button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-8 text-base-content/60">Tidak ada data pengurus.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pengurus->hasPages())
            <div class="mt-4">{{ $pengurus->links() }}</div>
        @endif
    </x-ui.card>

    <x-ui.confirm-delete />
</x-layouts.app>
