<x-layouts.app :title="'Audit Log'">
    <x-slot:header>
        <x-layouts.page-header title="Audit Log" description="Riwayat aktivitas sistem — mencatat semua perubahan data" />
    </x-slot:header>

    {{-- Statistik --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <x-ui.stat title="Total Log" :value="number_format($stats['total'])" icon="clipboard-document-list" />
        <x-ui.stat title="Hari Ini" :value="number_format($stats['today'])" icon="clock" color="info" />
        <x-ui.stat title="Ditambah" :value="number_format($stats['created'])" icon="plus-circle" color="success" />
        <x-ui.stat title="Diperbarui" :value="number_format($stats['updated'])" icon="pencil-square" color="warning" />
        <x-ui.stat title="Dihapus" :value="number_format($stats['deleted'])" icon="trash" color="error" />
    </div>

    <x-ui.card>
        {{-- Filter & Search --}}
        <form method="GET" action="{{ route('admin.audit-log') }}" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                <div class="md:col-span-2">
                    <x-ui.input name="search" placeholder="Cari deskripsi, user..." value="{{ request('search') }}" />
                </div>
                <div>
                    @php
                        $eventOptions = ['' => 'Semua Event', 'created' => 'Ditambah', 'updated' => 'Diperbarui', 'deleted' => 'Dihapus'];
                    @endphp
                    <x-ui.select name="event" :options="$eventOptions" selected="{{ request('event') }}" placeholder="Semua Event" />
                </div>
                <div>
                    @php
                        $userOptions = $users->pluck('name', 'id')->prepend('Semua User', '')->toArray();
                    @endphp
                    <x-ui.select name="user_id" :options="$userOptions" selected="{{ request('user_id') }}" placeholder="Semua User" />
                </div>
                <div>
                    @php
                        $modelOptions = collect(['' => 'Semua Modul'])->merge($modelTypes->pluck('label', 'value'))->toArray();
                    @endphp
                    <x-ui.select name="model_type" :options="$modelOptions" selected="{{ request('model_type') }}" placeholder="Semua Modul" />
                </div>
                <div class="flex gap-2">
                    <x-ui.button type="primary" size="sm" class="btn-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        Filter
                    </x-ui.button>
                    @if(request()->hasAny(['search', 'event', 'user_id', 'model_type', 'date_from', 'date_to']))
                        <x-ui.button type="ghost" size="sm" href="{{ route('admin.audit-log') }}">Reset</x-ui.button>
                    @endif
                </div>
            </div>
            {{-- Date range --}}
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3 mt-3">
                <div>
                    <x-ui.input name="date_from" type="date" value="{{ request('date_from') }}" placeholder="Dari tanggal" />
                </div>
                <div>
                    <x-ui.input name="date_to" type="date" value="{{ request('date_to') }}" placeholder="Sampai tanggal" />
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th class="w-12">#</th>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>Modul</th>
                        <th>ID</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr class="hover">
                        <td class="text-xs text-base-content/50">{{ $logs->firstItem() + $loop->index }}</td>
                        <td class="text-xs whitespace-nowrap">
                            <div>{{ $log->created_at->translatedFormat('d M Y') }}</div>
                            <div class="text-base-content/50">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td>
                            <div class="text-sm font-medium">{{ $log->user_name }}</div>
                            <div class="text-xs text-base-content/50">{{ $log->ip_address }}</div>
                        </td>
                        <td>
                            @php
                                $badgeType = match($log->event) {
                                    'created' => 'success',
                                    'updated' => 'warning',
                                    'deleted' => 'error',
                                    default   => 'info',
                                };
                                $badgeLabel = match($log->event) {
                                    'created' => 'Tambah',
                                    'updated' => 'Ubah',
                                    'deleted' => 'Hapus',
                                    default   => ucfirst($log->event),
                                };
                            @endphp
                            <x-ui.badge :type="$badgeType" size="sm">{{ $badgeLabel }}</x-ui.badge>
                        </td>
                        <td class="text-sm max-w-xs truncate">{{ $log->description }}</td>
                        <td class="text-xs">
                            {{ \App\Models\AuditLog::getModelLabel(new $log->auditable_type) }}
                        </td>
                        <td class="text-xs text-base-content/50">{{ $log->auditable_id }}</td>
                        <td>
                            <x-ui.button type="ghost" size="xs" href="{{ route('admin.audit-log.show', $log) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </x-ui.button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-base-content/40">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p>Belum ada aktivitas tercatat.</p>
                            <p class="text-sm mt-1">Log akan terisi otomatis saat ada perubahan data.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </x-ui.card>
</x-layouts.app>
