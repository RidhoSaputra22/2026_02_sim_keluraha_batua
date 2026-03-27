<x-layouts.app title="Kelola Pengguna">
    <x-layouts.page-header title="Kelola Pengguna" subtitle="Manajemen akun pengguna sistem">
        <x-ui.button href="{{ route('admin.users.create') }}" icon="plus" color="primary">
            Tambah Pengguna
        </x-ui.button>
    </x-layouts.page-header>

    {{-- Filter & Search --}}
    @php $roleOptions = $roles->pluck('label', 'id')->prepend('Semua Role', '')->toArray(); @endphp
    <x-ui.card class="mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <x-ui.input name="search" label="Cari" placeholder="Nama atau email..." value="{{ request('search') }}" class="w-64" />
            <x-ui.select name="role_id" label="Filter Role" :options="$roleOptions" selected="{{ request('role_id') }}" />
            <x-ui.button type="submit" color="primary" size="sm">Filter</x-ui.button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Reset</a>
        </form>
    </x-ui.card>

    {{-- Table --}}
    <x-ui.card>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                        <tr>
                            <td>{{ $users->firstItem() + $i }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <x-ui.badge>{{ $user->role?->label ?? '-' }}</x-ui.badge>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="badge {{ $user->is_active ? 'badge-success' : 'badge-error' }} cursor-pointer">
                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </td>
                            <td class="flex gap-1">
                                <x-ui.button href="{{ route('admin.users.edit', $user) }}" size="xs" color="warning">Edit</x-ui.button>
                                <x-ui.confirm-delete :action="route('admin.users.destroy', $user)" size="xs" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-base-content/50">Belum ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $users->links() }}</div>
    </x-ui.card>
</x-layouts.app>
