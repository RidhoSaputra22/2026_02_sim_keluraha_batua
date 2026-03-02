{{-- SECTION 7: USERS + AUDIT LOG --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- User Management Overview --}}
    <x-ui.card title="Manajemen Pengguna" compact>
        <div class="space-y-1.5 mb-3">
            @foreach($usersPerRole as $role)
            <div class="flex items-center justify-between text-sm px-1">
                <span class="text-base-content/70">{{ \App\Models\Role::roleLabels()[$role->name] ?? ucfirst($role->name) }}</span>
                <div class="flex items-center gap-2">
                    <div class="w-16 bg-base-200 rounded-full h-1.5">
                        <div class="bg-primary h-1.5 rounded-full" style="width: {{ $totalUsers > 0 ? round(($role->users_count / $totalUsers) * 100) : 0 }}%"></div>
                    </div>
                    <span class="font-bold w-4 text-right">{{ $role->users_count }}</span>
                </div>
            </div>
            @endforeach
        </div>
        <x-slot:actions>
            <x-ui.button type="ghost" size="sm" href="#">Kelola Users &rarr;</x-ui.button>
        </x-slot:actions>
    </x-ui.card>

    {{-- Recent Users --}}
    <x-ui.card title="Pengguna Terbaru" compact>
        <div class="space-y-3">
            @forelse($recentUsers as $user)
            <div class="flex items-center gap-3">
                <x-ui.avatar :name="$user->name" size="sm" />
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ $user->name }}</p>
                    <p class="text-xs text-base-content/50">{{ \App\Models\Role::roleLabels()[$user->role?->name] ?? ucfirst($user->role?->name ?? '-') }}</p>
                </div>
                <span class="inline-flex items-center gap-1 text-xs {{ $user->is_active ? 'text-success' : 'text-error' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-success' : 'bg-error' }}"></span>
                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            @empty
            <p class="text-sm text-base-content/60 text-center py-4">Belum ada pengguna.</p>
            @endforelse
        </div>
    </x-ui.card>

    {{-- Aktivitas Terbaru (Audit Log) --}}
    <x-ui.card title="Aktivitas Terbaru" compact>
        <div class="space-y-2 max-h-64 overflow-y-auto">
            @forelse($recentLogs as $log)
            <div class="flex items-start gap-2 text-xs border-b border-base-200 pb-2 last:border-0">
                <div class="mt-0.5 shrink-0">
                    @if($log->event === 'created')
                        <span class="w-2 h-2 rounded-full bg-success inline-block"></span>
                    @elseif($log->event === 'updated')
                        <span class="w-2 h-2 rounded-full bg-info inline-block"></span>
                    @elseif($log->event === 'deleted')
                        <span class="w-2 h-2 rounded-full bg-error inline-block"></span>
                    @else
                        <span class="w-2 h-2 rounded-full bg-base-300 inline-block"></span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-base-content/70 truncate">
                        <span class="font-semibold">{{ $log->user_name ?? 'System' }}</span>
                        {{ $log->description ?? $log->event }}
                    </p>
                    <p class="text-base-content/40">{{ $log->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-base-content/40 text-center py-4">Belum ada aktivitas.</p>
            @endforelse
        </div>
        <x-slot:actions>
            <x-ui.button type="ghost" size="sm" href="#">Lihat Semua &rarr;</x-ui.button>
        </x-slot:actions>
    </x-ui.card>

</div>
