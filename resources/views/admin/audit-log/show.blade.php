<x-layouts.app :title="'Detail Audit Log'">
    <x-slot:header>
        <x-layouts.page-header title="Detail Audit Log" description="Detail perubahan data #{{ $auditLog->id }}">
            <x-ui.button href="{{ route('admin.audit-log') }}" type="ghost" size="sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </x-ui.button>
        </x-layouts.page-header>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Info Utama --}}
        <x-ui.card class="lg:col-span-1">
            <h3 class="font-semibold mb-4">Informasi Aktivitas</h3>

            <div class="space-y-4">
                <div>
                    <p class="text-xs text-base-content/50 mb-1">Waktu</p>
                    <p class="font-medium">{{ $auditLog->created_at->translatedFormat('l, d F Y H:i:s') }}</p>
                    <p class="text-xs text-base-content/50">{{ $auditLog->created_at->diffForHumans() }}</p>
                </div>

                <div>
                    <p class="text-xs text-base-content/50 mb-1">User</p>
                    <p class="font-medium">{{ $auditLog->user_name }}</p>
                    @if($auditLog->user)
                        <p class="text-xs text-base-content/50">{{ $auditLog->user->email }}</p>
                    @endif
                </div>

                <div>
                    <p class="text-xs text-base-content/50 mb-1">Aksi</p>
                    @php
                        $badgeType = match($auditLog->event) {
                            'created' => 'success',
                            'updated' => 'warning',
                            'deleted' => 'error',
                            default   => 'info',
                        };
                        $badgeLabel = match($auditLog->event) {
                            'created' => 'Data Ditambahkan',
                            'updated' => 'Data Diperbarui',
                            'deleted' => 'Data Dihapus',
                            default   => ucfirst($auditLog->event),
                        };
                    @endphp
                    <x-ui.badge :type="$badgeType" size="md">{{ $badgeLabel }}</x-ui.badge>
                </div>

                <div>
                    <p class="text-xs text-base-content/50 mb-1">Deskripsi</p>
                    <p class="font-medium">{{ $auditLog->description }}</p>
                </div>

                <div>
                    <p class="text-xs text-base-content/50 mb-1">Modul</p>
                    <p class="font-medium">{{ \App\Models\AuditLog::getModelLabel(new $auditLog->auditable_type) }}</p>
                    <p class="text-xs text-base-content/50 font-mono">ID: {{ $auditLog->auditable_id }}</p>
                </div>

                <div class="divider my-1"></div>

                <div>
                    <p class="text-xs text-base-content/50 mb-1">IP Address</p>
                    <p class="text-sm font-mono">{{ $auditLog->ip_address ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs text-base-content/50 mb-1">User Agent</p>
                    <p class="text-xs text-base-content/60 break-all">{{ $auditLog->user_agent ?? '-' }}</p>
                </div>
            </div>
        </x-ui.card>

        {{-- Detail Perubahan --}}
        <x-ui.card class="lg:col-span-2">
            <h3 class="font-semibold mb-4">Detail Perubahan Data</h3>

            @if($auditLog->event === 'updated' && $auditLog->old_values && $auditLog->new_values)
                {{-- Show diff for updates --}}
                <div class="overflow-x-auto">
                    <table class="table table-sm table-zebra">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Nilai Lama</th>
                                <th>Nilai Baru</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditLog->new_values as $key => $newVal)
                            <tr>
                                <td class="font-mono text-xs font-semibold">{{ $key }}</td>
                                <td>
                                    <span class="bg-error/10 text-error px-2 py-1 rounded text-xs">
                                        {{ is_array($auditLog->old_values[$key] ?? null) ? json_encode($auditLog->old_values[$key]) : ($auditLog->old_values[$key] ?? '-') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="bg-success/10 text-success px-2 py-1 rounded text-xs">
                                        {{ is_array($newVal) ? json_encode($newVal) : ($newVal ?? '-') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @elseif($auditLog->event === 'created' && $auditLog->new_values)
                {{-- Show all values for creates --}}
                <div class="overflow-x-auto">
                    <table class="table table-sm table-zebra">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditLog->new_values as $key => $val)
                            <tr>
                                <td class="font-mono text-xs font-semibold">{{ $key }}</td>
                                <td>
                                    <span class="bg-success/10 text-success px-2 py-1 rounded text-xs">
                                        {{ is_array($val) ? json_encode($val) : ($val ?? '-') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @elseif($auditLog->event === 'deleted' && $auditLog->old_values)
                {{-- Show deleted values --}}
                <div class="overflow-x-auto">
                    <table class="table table-sm table-zebra">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Nilai (sebelum dihapus)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditLog->old_values as $key => $val)
                            <tr>
                                <td class="font-mono text-xs font-semibold">{{ $key }}</td>
                                <td>
                                    <span class="bg-error/10 text-error px-2 py-1 rounded text-xs">
                                        {{ is_array($val) ? json_encode($val) : ($val ?? '-') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @else
                <div class="text-center py-8 text-base-content/40">
                    <p>Tidak ada detail perubahan data yang tersedia.</p>
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
