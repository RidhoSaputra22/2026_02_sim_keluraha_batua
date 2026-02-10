<x-layouts.app :title="'Kelola Pengguna'">
    <x-slot:header>
        <x-layouts.page-header title="Kelola Pengguna" description="Manajemen akun pengguna sistem">
            <x-slot:actions>
                <x-ui.button type="primary" size="sm" href="{{ route('admin.users.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Pengguna
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    {{-- Filters --}}
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <x-ui.input name="search" placeholder="Cari nama, email, NIP, NIK..." value="{{ request('search') }}" />
            </div>
            <div class="w-full md:w-48">
                <x-ui.select name="role_id" placeholder="Semua Role" :options="$roles->pluck('label', 'id')->toArray()" selected="{{ request('role_id') }}" />
            </div>
            <div class="w-full md:w-40">
                <x-ui.select name="is_active" placeholder="Semua Status" :options="['1' => 'Aktif', '0' => 'Nonaktif']" selected="{{ request('is_active') }}" />
            </div>
            <div class="flex gap-2">
                <x-ui.button type="primary" size="md">Cari</x-ui.button>
                <x-ui.button type="ghost" size="md" href="{{ route('admin.users.index') }}">Reset</x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- Table --}}
    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>NIP</th>
                        <th>Status</th>
                        <th>Login Terakhir</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="hover">
                        <td>
                            <div class="flex items-center gap-3">
                                <x-ui.avatar :name="$user->name" size="sm" />
                                <div>
                                    <div class="font-medium">{{ $user->name }}</div>
                                    @if($user->jabatan)
                                        <div class="text-xs text-base-content/60">{{ $user->jabatan }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <x-ui.badge type="{{ $user->role?->name === 'admin' ? 'primary' : 'ghost' }}" size="sm">
                                {{ $user->role?->label ?? '-' }}
                            </x-ui.badge>
                        </td>
                        <td class="font-mono text-sm">{{ $user->nip ?? '-' }}</td>
                        <td>
                            @if($user->is_active)
                                <x-ui.badge type="success" size="sm">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge type="error" size="sm">Nonaktif</x-ui.badge>
                            @endif
                        </td>
                        <td class="text-sm">{{ $user->last_login_at?->diffForHumans() ?? 'Belum login' }}</td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <x-ui.button type="ghost" size="xs" href="{{ route('admin.users.edit', $user) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </x-ui.button>
                                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                                    @csrf @method('PATCH')
                                    <x-ui.button type="{{ $user->is_active ? 'warning' : 'success' }}" size="xs" :outline="true">
                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </x-ui.button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna ini?')">
                                    @csrf @method('DELETE')
                                    <x-ui.button type="error" size="xs" :outline="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </x-ui.button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-base-content/60">Tidak ada data pengguna.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="mt-4">{{ $users->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.app>
