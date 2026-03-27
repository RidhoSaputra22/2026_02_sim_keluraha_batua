{{-- SECTION 4: DEMOGRAFI (Agama, Pendidikan, Status Kawin) --}}
<div class="mb-2">
    <h2 class="text-lg font-bold text-base-content flex items-center gap-2">
        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Demografi Warga
    </h2>
    <p class="text-sm text-base-content/50 mb-4">Data demografi warga di wilayah {{ $wilayahLabel }}</p>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

    {{-- Chart: Agama --}}
    <x-ui.card title="Agama" compact>
        <div class="h-52 flex items-center justify-center">
            <canvas id="chartAgama"></canvas>
        </div>
        <div class="flex flex-wrap justify-center gap-x-3 gap-y-1 mt-2 text-xs text-base-content/60">
            @foreach($agamaData as $label => $val)
            <span>{{ ucfirst($label) }}: <strong>{{ $val }}</strong></span>
            @endforeach
        </div>
    </x-ui.card>

    {{-- Chart: Pendidikan --}}
    <x-ui.card title="Pendidikan" compact>
        <div class="h-52 flex items-center justify-center">
            <canvas id="chartPendidikan"></canvas>
        </div>
        <div class="flex flex-wrap justify-center gap-x-3 gap-y-1 mt-2 text-xs text-base-content/60">
            @foreach(array_slice($pendidikanData, 0, 5) as $label => $val)
            <span>{{ strtoupper(str_replace('_', ' ', $label)) }}: <strong>{{ $val }}</strong></span>
            @endforeach
        </div>
    </x-ui.card>

    {{-- Chart: Status Kawin --}}
    <x-ui.card title="Status Perkawinan" compact>
        <div class="h-52 flex items-center justify-center">
            <canvas id="chartStatusKawin"></canvas>
        </div>
        <div class="flex flex-wrap justify-center gap-x-3 gap-y-1 mt-2 text-xs text-base-content/60">
            @foreach($statusKawinData as $label => $val)
            <span>{{ ucfirst(str_replace('_', ' ', $label)) }}: <strong>{{ $val }}</strong></span>
            @endforeach
        </div>
    </x-ui.card>

</div>
