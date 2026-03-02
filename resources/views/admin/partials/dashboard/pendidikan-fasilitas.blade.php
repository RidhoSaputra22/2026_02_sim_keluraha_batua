{{-- SECTION 5: PENDIDIKAN + FASILITAS PUBLIK --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    {{-- Chart: Pendidikan --}}
    <x-ui.card title="Tingkat Pendidikan">
        <div class="h-72">
            <canvas id="chartPendidikan"></canvas>
        </div>
    </x-ui.card>

    {{-- Fasilitas Publik Summary --}}
    <x-ui.card title="Fasilitas Publik">
        <div class="grid grid-cols-3 gap-3 mb-5">
            <div class="rounded-xl bg-error/10 p-4 text-center">
                <div class="w-10 h-10 rounded-xl bg-error/20 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-error">{{ $totalFaskes }}</div>
                <div class="text-xs text-error/70 font-medium mt-1">Faskes</div>
            </div>
            <div class="rounded-xl bg-warning/10 p-4 text-center">
                <div class="w-10 h-10 rounded-xl bg-warning/20 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-warning">{{ $totalTempatIbadah }}</div>
                <div class="text-xs text-warning/70 font-medium mt-1">Tempat Ibadah</div>
            </div>
            <div class="rounded-xl bg-info/10 p-4 text-center">
                <div class="w-10 h-10 rounded-xl bg-info/20 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-5 h-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-info">{{ $totalSekolah }}</div>
                <div class="text-xs text-info/70 font-medium mt-1">Sekolah</div>
            </div>
        </div>

        {{-- UMKM Per Jenis Usaha --}}
        <h4 class="text-sm font-semibold text-base-content/70 mb-3">UMKM Per Jenis Usaha</h4>
        <div class="space-y-2">
            @forelse($umkmPerJenis as $jenis)
            <div class="flex items-center justify-between text-sm">
                <span class="text-base-content/70 truncate flex-1">{{ $jenis['nama'] }}</span>
                <div class="flex items-center gap-2 ml-2">
                    <div class="w-20 bg-base-200 rounded-full h-2">
                        <div class="bg-info h-2 rounded-full" style="width: {{ $totalUsaha > 0 ? round(($jenis['total'] / $totalUsaha) * 100) : 0 }}%"></div>
                    </div>
                    <span class="font-bold text-sm w-6 text-right">{{ $jenis['total'] }}</span>
                </div>
            </div>
            @empty
            <p class="text-sm text-base-content/40 text-center py-2">Belum ada data</p>
            @endforelse
        </div>
    </x-ui.card>

</div>
