{{-- SECTION 2: PETA WILAYAH + MUTASI CARD --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Peta Wilayah Kelurahan --}}
   <div class="lg:col-span-2">
        <x-ui.card title="Peta Wilayah " compact>
            <div class="relative">
                <div id="dashboard-map" class="w-full rounded-lg " style="height: 500px; z-index: 0;"></div>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3 text-xs text-base-content/50">
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-primary inline-block"></span>
                    RW Anda di-highlight
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded-full bg-base-300 inline-block"></span>
                    RW lain tetap ditampilkan
                </span>
                <span>Klik area untuk melihat info RW</span>
            </div>
            <div class="absolute top-0 left-3 right-0  border-t border-base-200 pt-3">

                <div id="dashboard-custom-layer-toggles" class="flex flex-wrap gap-2">
                    <span class="text-xs text-base-content/50">Memuat layer...</span>
                </div>
            </div>
            </div>
        </x-ui.card>
    </div>


    {{-- Mutasi Penduduk (Bulan Ini) + Quick Actions --}}
    <div class="space-y-6">
        <x-ui.card title="Mutasi Penduduk" compact>
            <p class="text-xs text-base-content/50 mb-3">{{ now()->translatedFormat('F Y') }}</p>
            <div class="space-y-2">
                @php
                $mutasiItems = [
                    ['label' => 'Kelahiran',  'value' => $mutasiLahir,     'color' => 'text-success', 'bg' => 'bg-success/10', 'icon' => 'M12 4v16m8-8H4'],
                    ['label' => 'Kematian',   'value' => $mutasiMeninggal, 'color' => 'text-error',   'bg' => 'bg-error/10',   'icon' => 'M20 12H4'],
                    ['label' => 'Datang',     'value' => $mutasiDatang,    'color' => 'text-info',    'bg' => 'bg-info/10',    'icon' => 'M11 16l-4-4m0 0l4-4m-4 4h14'],
                    ['label' => 'Pindah',     'value' => $mutasiPindah,    'color' => 'text-warning', 'bg' => 'bg-warning/10', 'icon' => 'M13 7l5 5m0 0l-5 5m5-5H6'],
                ];
                @endphp
                @foreach($mutasiItems as $item)
                <div class="flex items-center justify-between rounded-lg px-3 py-2 {{ $item['bg'] }}">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 {{ $item['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span class="text-sm font-medium">{{ $item['label'] }}</span>
                    </div>
                    <span class="text-lg font-bold {{ $item['color'] }}">{{ $item['value'] }}</span>
                </div>
                @endforeach
                <div class="pt-2 border-t border-base-300">
                    <div class="flex items-center justify-between text-xs text-base-content/50 px-1">
                        <span>Total</span>
                        <span class="font-bold text-base-content text-base">{{ $mutasiLahir + $mutasiMeninggal + $mutasiDatang + $mutasiPindah }}</span>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- Aksi Cepat --}}
        <x-ui.card title="Aksi Cepat" compact>
            <div class="space-y-2">
                <x-ui.button type="primary" size="sm" class="w-full justify-start gap-2" href="{{ route('kependudukan.penduduk.create') }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Tambah Penduduk
                </x-ui.button>
                <x-ui.button type="secondary" size="sm" class="w-full justify-start gap-2" href="{{ route('kependudukan.keluarga.create') }}" :outline="true">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Kartu Keluarga
                </x-ui.button>
                <x-ui.button type="accent" size="sm" class="w-full justify-start gap-2" href="{{ route('usaha.create') }}" :outline="true">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Daftar Usaha Baru
                </x-ui.button>
            </div>
        </x-ui.card>
    </div>

</div>
