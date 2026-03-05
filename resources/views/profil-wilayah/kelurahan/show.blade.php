<x-layouts.app :title="'Profil Kelurahan ' . $kelurahan->nama">
    <x-slot:header>
        <x-layouts.page-header title="Profil Kelurahan {{ $kelurahan->nama }}" description="Informasi biodata dan profil kelurahan">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('master.profil-wilayah.kelurahan.edit', $kelurahan) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit Profil
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Foto & Info Utama --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Foto Kelurahan --}}
            <x-ui.card title="Foto Kelurahan">
                @if($kelurahan->foto)
                    <div class="w-full aspect-video rounded-lg overflow-hidden bg-base-200">
                        <img src="{{ asset('storage/' . $kelurahan->foto) }}" alt="Foto {{ $kelurahan->nama }}" class="w-full h-full object-cover">
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

            {{-- Pimpinan --}}
            <x-ui.card title="Lurah">
                <div class="space-y-3">
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Nama Lurah</span>
                        <p class="font-semibold">{{ $kelurahan->nama_lurah ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">NIP</span>
                        <p class="font-mono text-sm">{{ $kelurahan->nip_lurah ?? '-' }}</p>
                    </div>
                </div>
            </x-ui.card>

            {{-- Statistik Wilayah --}}
            <x-ui.card title="Statistik Wilayah">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-primary/10 rounded-lg">
                        <p class="text-2xl font-bold text-primary">{{ $totalRw }}</p>
                        <p class="text-xs text-base-content/60">Total RW</p>
                    </div>
                    <div class="text-center p-3 bg-secondary/10 rounded-lg">
                        <p class="text-2xl font-bold text-secondary">{{ $totalRt }}</p>
                        <p class="text-xs text-base-content/60">Total RT</p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Kolom Kanan: Detail Biodata --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Umum --}}
            <x-ui.card title="Informasi Umum">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Kecamatan</span>
                        <p class="font-medium">{{ $kelurahan->kecamatan->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Kode Pos</span>
                        <p class="font-medium">{{ $kelurahan->kode_pos ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Luas Area</span>
                        <p class="font-medium">{{ $kelurahan->luas_area ? $kelurahan->luas_area . ' km²' : '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Alamat Kantor</span>
                        <p class="font-medium">{{ $kelurahan->alamat_kantor ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Telepon</span>
                        <p class="font-medium">{{ $kelurahan->no_telp ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Email</span>
                        <p class="font-medium">{{ $kelurahan->email ?? '-' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <span class="text-xs text-base-content/60 uppercase">Website</span>
                        <p class="font-medium">
                            @if($kelurahan->website)
                                <a href="{{ $kelurahan->website }}" target="_blank" class="link link-primary">{{ $kelurahan->website }}</a>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </x-ui.card>

            {{-- Batas Wilayah --}}
            <x-ui.card title="Batas Wilayah">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Batas Utara</span>
                        <p class="font-medium">{{ $kelurahan->batas_utara ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Batas Selatan</span>
                        <p class="font-medium">{{ $kelurahan->batas_selatan ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Batas Timur</span>
                        <p class="font-medium">{{ $kelurahan->batas_timur ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/60 uppercase">Batas Barat</span>
                        <p class="font-medium">{{ $kelurahan->batas_barat ?? '-' }}</p>
                    </div>
                </div>
            </x-ui.card>

            {{-- Visi & Misi --}}
            <x-ui.card title="Visi & Misi">
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-sm text-base-content/60 uppercase mb-1">Visi</h4>
                        <p class="text-base-content">{{ $kelurahan->visi ?? '-' }}</p>
                    </div>
                    <div class="divider my-1"></div>
                    <div>
                        <h4 class="font-semibold text-sm text-base-content/60 uppercase mb-1">Misi</h4>
                        @if($kelurahan->misi)
                            <div class="whitespace-pre-line text-base-content">{{ $kelurahan->misi }}</div>
                        @else
                            <p class="text-base-content">-</p>
                        @endif
                    </div>
                </div>
            </x-ui.card>

            {{-- Deskripsi --}}
            @if($kelurahan->deskripsi)
            <x-ui.card title="Deskripsi">
                <div class="whitespace-pre-line text-base-content">{{ $kelurahan->deskripsi }}</div>
            </x-ui.card>
            @endif

            {{-- Daftar RW --}}
            <x-ui.card title="Daftar RW">
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm">
                        <thead>
                            <tr>
                                <th>RW</th>
                                <th>Jumlah RT</th>
                                <th>Luas Area</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kelurahan->rws->sortBy('nomor') as $rw)
                            <tr class="hover">
                                <td class="font-semibold">RW {{ str_pad($rw->nomor, 3, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $rw->rts->count() }} RT</td>
                                <td>{{ $rw->luas_area ? $rw->luas_area . ' km²' : '-' }}</td>
                                <td class="text-right">
                                    <x-ui.button type="ghost" size="xs" href="{{ route('master.rw.show', $rw) }}">
                                        Detail
                                    </x-ui.button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
