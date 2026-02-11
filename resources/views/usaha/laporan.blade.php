<x-layouts.app :title="'Laporan Usaha'">
    <x-slot:header>
        <x-layouts.page-header title="Laporan Usaha" description="Statistik dan ringkasan data usaha UMKM/PK5">
        </x-layouts.page-header>
    </x-slot:header>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <x-ui.stat title="Total Usaha" :value="$totalUsaha" description="Semua usaha terdaftar" />
        <x-ui.stat title="Usaha Aktif" :value="$totalAktif" description="Status aktif" />
        <x-ui.stat title="Tidak Aktif" :value="$totalTidakAktif" description="Status tidak aktif" />
        <x-ui.stat title="Jenis Usaha" :value="$totalJenis" description="Kategori terdaftar" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Per Jenis Usaha --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                Per Jenis Usaha
            </h3>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Jenis Usaha</th>
                            <th class="text-center">Jumlah</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perJenis as $jenis)
                        <tr>
                            <td class="font-medium">{{ $jenis->nama }}</td>
                            <td class="text-center">
                                <span class="badge badge-primary badge-sm">{{ $jenis->umkms_count }}</span>
                            </td>
                            <td>
                                @php $pct = $totalUsaha > 0 ? round(($jenis->umkms_count / $totalUsaha) * 100, 1) : 0; @endphp
                                <div class="flex items-center gap-2">
                                    <progress class="progress progress-primary w-20" value="{{ $pct }}" max="100"></progress>
                                    <span class="text-xs">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-base-content/50">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        {{-- Per RW --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                Per RW
            </h3>
            <div class="overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>RW</th>
                            <th class="text-center">Jumlah RT</th>
                            <th class="text-center">Jumlah Usaha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perRw as $rw)
                        <tr>
                            <td class="font-medium">RW {{ $rw->nomor }}</td>
                            <td class="text-center">{{ $rw->rts_count ?? 0 }}</td>
                            <td class="text-center">
                                <span class="badge badge-accent badge-sm">{{ $rw->umkm_count ?? 0 }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-base-content/50">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>

    {{-- Recent Usaha --}}
    <x-ui.card>
        <h3 class="text-lg font-semibold mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            Usaha Terbaru
        </h3>
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Nama Usaha</th>
                        <th>Pemilik</th>
                        <th>Jenis</th>
                        <th>Lokasi</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsaha as $u)
                    <tr>
                        <td class="font-medium">{{ $u->nama_ukm }}</td>
                        <td>{{ $u->nama_pemilik }}</td>
                        <td>{{ $u->jenisUsaha->nama ?? '-' }}</td>
                        <td>
                            @if($u->rt && $u->rt->rw)
                                RT {{ $u->rt->nomor }} / RW {{ $u->rt->rw->nomor }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $sc = match($u->status) { 'aktif' => 'badge-success', 'tidak_aktif' => 'badge-error', default => 'badge-ghost' };
                            @endphp
                            <span class="badge {{ $sc }} badge-sm">{{ ucfirst(str_replace('_', ' ', $u->status ?? 'N/A')) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-base-content/50">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
</x-layouts.app>
