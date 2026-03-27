<x-layouts.app :title="'RW ' . str_pad($rw->nomor, 3, '0', STR_PAD_LEFT)">
    <x-slot:header>
        <x-layouts.page-header title="RW {{ str_pad($rw->nomor, 3, '0', STR_PAD_LEFT) }}" description="Kel. {{ $rw->kelurahan->nama ?? '-' }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('master.rw.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
                <x-ui.button type="primary" size="sm" href="{{ route('master.rw.edit', $rw) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit
                </x-ui.button>
                <x-ui.button type="error" size="sm" :outline="true" @click="$dispatch('confirm-delete', { action: '{{ route('master.rw.destroy', $rw) }}', message: 'Hapus RW {{ str_pad($rw->nomor, 3, '0', STR_PAD_LEFT) }}? Pastikan semua RT sudah dihapus.' })">
                    Hapus
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Foto --}}
            <x-ui.card title="Foto RW">
                @if($rw->foto)
                    <div class="w-full aspect-video rounded-lg overflow-hidden bg-base-200">
                        <img src="{{ asset('storage/' . $rw->foto) }}" alt="Foto RW {{ $rw->nomor }}" class="w-full h-full object-cover">
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

            {{-- Statistik --}}
            <x-ui.card title="Statistik">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/60">Total RT</span>
                        <span class="font-bold text-lg">{{ $totalRt }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/60">Luas Area</span>
                        <span class="font-bold">{{ $rw->luas_area ? $rw->luas_area . ' km²' : '-' }}</span>
                    </div>
                    @if($rw->petaPolygon?->warna)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-base-content/60">Warna Peta</span>
                        <span class="w-6 h-6 rounded border border-base-300" style="background-color: {{ $rw->petaPolygon->warna }}"></span>
                    </div>
                    @endif
                </div>
            </x-ui.card>

            {{-- Quick Actions --}}
            <x-ui.card title="Aksi Cepat">
                <div class="space-y-2">
                    <x-ui.button type="primary" size="sm" class="w-full" href="{{ route('master.rt.create', ['rw_id' => $rw->id]) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Tambah RT di RW Ini
                    </x-ui.button>
                    <x-ui.button type="ghost" size="sm" class="w-full" href="{{ route('master.rt.index', ['rw_id' => $rw->id]) }}">
                        Lihat Semua RT
                    </x-ui.button>
                </div>
            </x-ui.card>
        </div>

        {{-- Kolom Kanan --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Umum --}}
            <x-ui.card title="Informasi Umum">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Kelurahan</span>
                        <p class="font-medium">{{ $rw->kelurahan->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Nomor RW</span>
                        <p class="font-medium">{{ str_pad($rw->nomor, 3, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Luas Area</span>
                        <p class="font-medium">{{ $rw->luas_area ? $rw->luas_area . ' km²' : '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Telepon</span>
                        <p class="font-medium">{{ $rw->no_telp ?? '-' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-xs text-base-content/60 uppercase">Alamat Sekretariat</span>
                        <p class="font-medium">{{ $rw->alamat_sekretariat ?? '-' }}</p>
                    </div>
                </div>
            </x-ui.card>

            {{-- Batas Wilayah --}}
            <x-ui.card title="Batas Wilayah">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Batas Utara</span>
                        <p class="font-medium">{{ $rw->batas_utara ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Batas Selatan</span>
                        <p class="font-medium">{{ $rw->batas_selatan ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Batas Timur</span>
                        <p class="font-medium">{{ $rw->batas_timur ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Batas Barat</span>
                        <p class="font-medium">{{ $rw->batas_barat ?? '-' }}</p>
                    </div>
                </div>
            </x-ui.card>

            {{-- Deskripsi --}}
            @if($rw->deskripsi)
            <x-ui.card title="Deskripsi">
                <div class="whitespace-pre-line text-base-content">{{ $rw->deskripsi }}</div>
            </x-ui.card>
            @endif

            {{-- Fasilitas --}}
            @if($rw->fasilitas)
            <x-ui.card title="Fasilitas Umum">
                <div class="whitespace-pre-line text-base-content">{{ $rw->fasilitas }}</div>
            </x-ui.card>
            @endif

            {{-- Daftar RT --}}
            <x-ui.card title="Daftar RT di RW ini">
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm">
                        <thead>
                            <tr>
                                <th>RT</th>
                                <th>Luas Area</th>
                                <th>Telepon</th>
                                <th>Foto</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rw->rts->sortBy('nomor') as $rt)
                            <tr class="hover">
                                <td class="font-semibold">RT {{ str_pad($rt->nomor, 3, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $rt->luas_area ? $rt->luas_area . ' km²' : '-' }}</td>
                                <td>{{ $rt->no_telp ?? '-' }}</td>
                                <td>
                                    @if($rt->foto)
                                        <div class="avatar"><div class="w-8 h-8 rounded"><img src="{{ asset('storage/' . $rt->foto) }}" alt=""></div></div>
                                    @else
                                        <span class="text-base-content/30 text-xs">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-end gap-1">
                                        <x-ui.button type="ghost" size="xs" href="{{ route('master.rt.show', $rt) }}">Detail</x-ui.button>
                                        <x-ui.button type="primary" size="xs" :outline="true" href="{{ route('master.rt.edit', $rt) }}">Edit</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-base-content/60">Belum ada data RT.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>
    </div>

    {{-- Pengurus Section (full width) --}}
    <div class="mt-6">
        @include('master.partials.pengurus-section', [
            'parentType' => 'rw',
            'parent' => $rw,
            'pendudukList' => $pendudukList,
            'jabatanList' => $jabatanList,
            'kelurahanList' => $kelurahanList,
            'rtRwUserList' => $rtRwUserList,
        ])
    </div>

    <x-ui.confirm-delete />
</x-layouts.app>
