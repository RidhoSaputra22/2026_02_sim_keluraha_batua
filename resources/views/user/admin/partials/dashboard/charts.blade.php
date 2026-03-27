{{-- SECTION 3: TREND MUTASI + PERSEBARAN PENDUDUK PER RW --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

	{{-- Chart: Trend Mutasi 6 Bulan --}}
	<x-ui.card title="Trend Mutasi (6 Bulan Terakhir)">
		<div class="h-72">
			<canvas id="chartMutasiTrend"></canvas>
		</div>
	</x-ui.card>

	{{-- Chart: Persebaran Penduduk Per RW --}}
	<x-ui.card title="Persebaran Penduduk Per RW">
		<div class="h-72">
			<canvas id="chartPendudukPerRw"></canvas>
		</div>
	</x-ui.card>

</div>
