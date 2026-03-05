<x-layouts.app :title="'Profil RT ' . str_pad($rt->nomor, 3, '0', STR_PAD_LEFT) . ' / RW ' . str_pad($rt->rw->nomor, 3, '0', STR_PAD_LEFT)">
    <x-slot:header>
        <x-layouts.page-header title="Profil RT {{ str_pad($rt->nomor, 3, '0', STR_PAD_LEFT) }}" description="RW {{ str_pad($rt->rw->nomor, 3, '0', STR_PAD_LEFT) }} — Kel. {{ $rt->rw->kelurahan->nama ?? '-' }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('master.profil-wilayah.rt.index') }}">
                    Kembali
                </x-ui.button>
                <x-ui.button type="primary" size="sm" href="{{ route('master.profil-wilayah.rt.edit', $rt) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit Profil
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Foto --}}
            <x-ui.card title="Foto RT">
                @if($rt->foto)
                    <div class="w-full aspect-video rounded-lg overflow-hidden bg-base-200">
                        <img src="{{ asset('storage/' . $rt->foto) }}" alt="Foto RT {{ $rt->nomor }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="w-full aspect-video rounded-lg bg-base-200 flex items-center justify-center">
                        <div class="text-center text-base-content/40">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <p>Belum ada foto</p>
                        </div>
                    </div>
                @endif
            </x-ui.card>

            {{-- Info Ringkas --}}
            <x-ui.card title="Info Ringkas">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-base-content/60">RW</span>
                        <a href="{{ route('master.profil-wilayah.rw.show', $rt->rw) }}" class="font-semibold link link-primary">
                            RW {{ str_pad($rt->rw->nomor, 3, '0', STR_PAD_LEFT) }}
                        </a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-base-content/60">Kelurahan</span>
                        <span class="font-semibold">{{ $rt->rw->kelurahan->nama ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-base-content/60">Luas Area</span>
                        <span class="font-semibold">{{ $rt->luas_area ? $rt->luas_area . ' km²' : '-' }}</span>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Kolom Kanan --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Umum --}}
            <x-ui.card title="Informasi Umum">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Nomor RT</span>
                        <p class="font-medium">{{ str_pad($rt->nomor, 3, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Luas Area</span>
                        <p class="font-medium">{{ $rt->luas_area ? $rt->luas_area . ' km²' : '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Telepon</span>
                        <p class="font-medium">{{ $rt->no_telp ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Alamat Pos</span>
                        <p class="font-medium">{{ $rt->alamat_pos ?? '-' }}</p>
                    </div>
                </div>
            </x-ui.card>

            {{-- Deskripsi --}}
            @if($rt->deskripsi)
            <x-ui.card title="Deskripsi">
                <div class="whitespace-pre-line text-base-content">{{ $rt->deskripsi }}</div>
            </x-ui.card>
            @endif

            {{-- Fasilitas --}}
            @if($rt->fasilitas)
            <x-ui.card title="Fasilitas Umum">
                <div class="whitespace-pre-line text-base-content">{{ $rt->fasilitas }}</div>
            </x-ui.card>
            @endif
        </div>
    </div>
</x-layouts.app>
