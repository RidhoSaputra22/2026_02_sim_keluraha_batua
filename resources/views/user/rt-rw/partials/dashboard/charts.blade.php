{{-- SECTION 3: TREND MUTASI + RT BREAKDOWN (if RW user) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    {{-- Chart: Trend Mutasi 6 Bulan --}}
    <x-ui.card title="Trend Mutasi (6 Bulan Terakhir)">
        <div class="h-72">
            <canvas id="chartMutasiTrend"></canvas>
        </div>
    </x-ui.card>

    {{-- RT Breakdown (for RW users) or Gender Chart (for RT users) --}}
    @if(count($rtBreakdown) > 0)
    <x-ui.card title="Data Per RT">
        <div class="h-72">
            <canvas id="chartRtBreakdown"></canvas>
        </div>
    </x-ui.card>
    @else
    <x-ui.card title="Komposisi Jenis Kelamin">
        <div class="h-72 flex items-center justify-center">
            <div class="w-64">
                <canvas id="chartGenderLarge"></canvas>
            </div>
        </div>
        <div class="flex justify-center gap-6 mt-2 text-sm text-base-content/60">
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-info inline-block"></span>
                Laki-laki: <strong>{{ $lakiLaki }}</strong>
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded-full bg-accent inline-block"></span>
                Perempuan: <strong>{{ $perempuan }}</strong>
            </span>
        </div>
    </x-ui.card>
    @endif

</div>
