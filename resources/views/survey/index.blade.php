<x-layouts.app :title="'Survey Kepuasan'">
    <x-slot:header>
        <x-layouts.page-header title="Survey Kepuasan" description="Kelola data survey kepuasan layanan masyarakat">
            @if(in_array(auth()->user()->getRoleName(), [\App\Models\Role::ADMIN, \App\Models\Role::OPERATOR]))
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('survey.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Survey
                </x-ui.button>
            </x-slot:actions>
            @endif
        </x-layouts.page-header>
    </x-slot:header>

    @if(session('success'))
        <x-ui.alert type="success" class="mb-4">{{ session('success') }}</x-ui.alert>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <x-ui.stat title="Total Responden" :value="$totalSurvey" description="Data survey masuk" />
        <x-ui.stat title="Rata-rata Nilai" :value="number_format($rataRata ?? 0, 2)" description="Skala 1-5" />
    </div>

    {{-- Rekap Per Layanan --}}
    @if($rekapPerLayanan->count())
    <x-ui.card class="mb-6">
        <h3 class="font-semibold mb-3">Rekap Per Jenis Layanan</h3>
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Jenis Layanan</th>
                        <th class="text-center">Jumlah Responden</th>
                        <th class="text-center">Rata-rata Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapPerLayanan as $layanan)
                    <tr>
                        <td>{{ $layanan->nama }}</td>
                        <td class="text-center">{{ $layanan->survey_kepuasan_count }}</td>
                        <td class="text-center">
                            @php $avg = $layanan->survey_kepuasan_avg_nilai_rata_rata ?? 0; @endphp
                            <span class="badge {{ $avg >= 4 ? 'badge-success' : ($avg >= 3 ? 'badge-warning' : 'badge-error') }} badge-sm">
                                {{ number_format($avg, 2) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-ui.card>
    @endif

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('survey.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari berdasarkan pekerjaan, pendidikan..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-48">
                <x-ui.select name="jenis_layanan_id" placeholder="Semua Layanan" :options="$layananList->pluck('nama', 'id')->toArray()" selected="{{ request('jenis_layanan_id') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('survey.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Table --}}
    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th class="w-12">No</th>
                        <th>Jenis Layanan</th>
                        <th>Jenis Kelamin</th>
                        <th>Umur</th>
                        <th>Pendidikan</th>
                        <th>Pekerjaan</th>
                        <th class="text-center">Jumlah Nilai</th>
                        <th class="text-center">Rata-rata</th>
                        @if(in_array(auth()->user()->getRoleName(), [\App\Models\Role::ADMIN, \App\Models\Role::OPERATOR]))
                        <th class="w-32 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($surveyList as $item)
                    <tr class="hover">
                        <td class="text-sm text-base-content/60">{{ $surveyList->firstItem() + $loop->index }}</td>
                        <td class="font-medium">{{ $item->jenisLayanan->nama ?? '-' }}</td>
                        <td class="text-sm">{{ $item->jenis_kelamin ?? '-' }}</td>
                        <td class="text-sm">{{ $item->umur ?? '-' }}</td>
                        <td class="text-sm">{{ $item->pendidikan ?? '-' }}</td>
                        <td class="text-sm">{{ $item->pekerjaan ?? '-' }}</td>
                        <td class="text-center text-sm">{{ $item->jumlah_nilai ?? '-' }}</td>
                        <td class="text-center">
                            @php $val = $item->nilai_rata_rata ?? 0; @endphp
                            <span class="badge {{ $val >= 4 ? 'badge-success' : ($val >= 3 ? 'badge-warning' : 'badge-error') }} badge-sm">
                                {{ number_format($val, 2) }}
                            </span>
                        </td>
                        @if(in_array(auth()->user()->getRoleName(), [\App\Models\Role::ADMIN, \App\Models\Role::OPERATOR]))
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs" href="{{ route('survey.edit', $item) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <form method="POST" action="{{ route('survey.destroy', $item) }}" onsubmit="return confirm('Hapus data survey ini?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="error" size="xs" :outline="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </x-ui.button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-base-content/50">
                            <p>Belum ada data survey kepuasan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($surveyList->hasPages())
            <div class="mt-4">{{ $surveyList->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
