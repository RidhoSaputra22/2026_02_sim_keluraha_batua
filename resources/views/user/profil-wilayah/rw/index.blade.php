<x-layouts.app :title="'Profil RW'">
    <x-slot:header>
        <x-layouts.page-header title="Profil Wilayah RW" description="Daftar profil dan biodata RW">
            <x-slot:actions>
                @php $kelurahan = \App\Models\Kelurahan::first(); @endphp
                @if($kelurahan)
                <x-ui.button type="ghost" size="sm" href="{{ route('master.profil-wilayah.kelurahan.show', $kelurahan) }}">
                    Profil Kelurahan
                </x-ui.button>
                @endif
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    {{-- Filter --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('master.profil-wilayah.rw.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nomor RW, deskripsi, alamat..." value="{{ request('search') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md" :isSubmit="true">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('master.profil-wilayah.rw.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Daftar RW --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($rwList as $rw)
        <x-ui.card>
            {{-- Foto --}}
            <div class="w-full aspect-video rounded-lg overflow-hidden bg-base-200 mb-4">
                @if($rw->foto)
                    <img src="{{ asset('storage/' . $rw->foto) }}" alt="Foto RW {{ $rw->nomor }}" class="w-full h-full object-cover">
                @else
                    <div class="flex items-center justify-center h-full text-base-content/30">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                    </div>
                @endif
            </div>

            <h3 class="text-lg font-bold mb-2">RW {{ str_pad($rw->nomor, 3, '0', STR_PAD_LEFT) }}</h3>

            <div class="space-y-1 text-sm text-base-content/70">
                <div class="flex justify-between">
                    <span>Jumlah RT</span>
                    <span class="font-semibold text-base-content">{{ $rw->rts->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Luas Area</span>
                    <span class="font-semibold text-base-content">{{ $rw->luas_area ? $rw->luas_area . ' km²' : '-' }}</span>
                </div>
                @if($rw->no_telp)
                <div class="flex justify-between">
                    <span>Telepon</span>
                    <span class="font-semibold text-base-content">{{ $rw->no_telp }}</span>
                </div>
                @endif
            </div>

            @if($rw->deskripsi)
                <p class="text-xs text-base-content/50 mt-2 line-clamp-2">{{ $rw->deskripsi }}</p>
            @endif

            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('master.profil-wilayah.rw.show', $rw) }}">Detail</x-ui.button>
                <x-ui.button type="primary" size="sm" :outline="true" href="{{ route('master.profil-wilayah.rw.edit', $rw) }}">Edit</x-ui.button>
            </x-slot:actions>
        </x-ui.card>
        @empty
        <div class="col-span-full text-center py-12 text-base-content/60">
            Tidak ada data RW.
        </div>
        @endforelse
    </div>

    @if($rwList->hasPages())
        <div class="mt-6">{{ $rwList->links() }}</div>
    @endif
</x-layouts.app>
