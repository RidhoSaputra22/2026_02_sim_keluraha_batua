<x-layouts.app title="Kelola Role">
    <x-layouts.page-header title="Kelola Role" subtitle="Daftar role pengguna sistem" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($roles as $role)
            <x-ui.card>
                <h3 class="font-bold text-lg">{{ $role->label }}</h3>
                <p class="text-sm text-base-content/70 mt-1">{{ $role->description }}</p>
                <div class="mt-3 flex items-center justify-between">
                    <x-ui.badge color="{{ $role->is_active ? 'success' : 'error' }}">
                        {{ $role->is_active ? 'Aktif' : 'Nonaktif' }}
                    </x-ui.badge>
                    <span class="text-sm text-base-content/60">{{ $role->users_count }} pengguna</span>
                </div>
            </x-ui.card>
        @endforeach
    </div>
</x-layouts.app>
