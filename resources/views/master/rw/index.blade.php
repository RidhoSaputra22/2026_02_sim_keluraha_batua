<x-layouts.app :title="'Data RW'">
    <x-slot:header>
        <x-layouts.page-header title="Data RW" description="Kelola data Rukun Warga">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('master.rw.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah RW
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    {{-- Filter --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('master.rw.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nomor RW, deskripsi, alamat..."
                    value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-48">
                <x-ui.select name="kelurahan_id" placeholder="Semua Kelurahan" :options="$kelurahanFilter->mapWithKeys(fn($k) => [$k->id => $k->nama])->toArray()"
                    selected="{{ request('kelurahan_id') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md" :isSubmit="true">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('master.rw.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <x-ui.stat title="Total RW" :value="$totalRw"
            icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>'
            type="primary" />
        <x-ui.stat title="Total RT" :value="$totalRt"
            icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>'
            type="secondary" />
    </div>

    {{-- Daftar RW (Card Grid) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($rwList as $rw)
            <x-ui.card href="{{ route('master.rw.show', $rw) }}">
                {{-- Foto --}}
                <div class="w-full aspect-video rounded-lg overflow-hidden bg-base-200 mb-4">
                    @if ($rw->foto)
                        <img src="{{ asset('storage/' . $rw->foto) }}" alt="Foto RW {{ $rw->nomor }}"
                            class="w-full h-full object-cover">
                    @else
                        <div class="flex items-center justify-center h-full text-base-content/30">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2 mb-2">
                    <h3 class="text-lg font-bold">RW {{ str_pad($rw->nomor, 3, '0', STR_PAD_LEFT) }}</h3>
                    @if ($rw->warna)
                        <span class="w-4 h-4 rounded-full inline-block border border-base-300"
                            style="background-color: {{ $rw->warna }}"></span>
                    @endif
                </div>

                <div class="text-xs text-base-content/50 mb-2">{{ $rw->kelurahan->nama ?? '-' }}</div>

                <div class="space-y-1 text-sm text-base-content/70">
                    <div class="flex justify-between">
                        <span>Jumlah RT</span>
                        <x-ui.badge type="info" size="sm">{{ $rw->rts->count() }}</x-ui.badge>
                    </div>
                    <div class="flex justify-between">
                        <span>Luas Area</span>
                        <span
                            class="font-semibold text-base-content">{{ $rw->luas_area ? $rw->luas_area . ' km²' : '-' }}</span>
                    </div>
                    @if ($rw->no_telp)
                        <div class="flex justify-between">
                            <span>Telepon</span>
                            <span class="font-semibold text-base-content">{{ $rw->no_telp }}</span>
                        </div>
                    @endif
                </div>

                @if ($rw->deskripsi)
                    <p class="text-xs text-base-content/50 mt-2 line-clamp-2">{{ $rw->deskripsi }}</p>
                @endif

                <x-slot:actions>
                    <x-ui.button type="ghost" size="sm"
                        href="{{ route('master.rw.show', $rw) }}">Detail</x-ui.button>
                    <x-ui.button type="primary" size="sm" :outline="true"
                        href="{{ route('master.rw.edit', $rw) }}">Edit</x-ui.button>
                    <x-ui.button type="error" size="sm" :outline="true"
                        @click="$dispatch('confirm-delete', { action: '{{ route('master.rw.destroy', $rw) }}', message: 'Hapus RW {{ str_pad($rw->nomor, 3, '0', STR_PAD_LEFT) }}? Semua data RT di RW ini harus dihapus terlebih dahulu.' })">
                        Hapus
                    </x-ui.button>
                </x-slot:actions>
            </x-ui.card>
        @empty
            <div class="col-span-full text-center py-12 text-base-content/60">
                Tidak ada data RW. <a href="{{ route('master.rw.create') }}" class="link link-primary">Tambah RW
                    baru</a>.
            </div>
        @endforelse
    </div>

    @if ($rwList->hasPages())
        <div class="mt-6">{{ $rwList->links() }}</div>
    @endif

    <x-ui.confirm-delete />
</x-layouts.app>
