<x-layouts.app :title="'Role & Hak Akses'">
    <x-slot:header>
        <x-layouts.page-header title="Role & Hak Akses" description="Daftar role dan permission sistem" />
    </x-slot:header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
        <x-ui.card>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </div>
                <div>
                    <h3 class="font-bold">{{ $role->label }}</h3>
                    <p class="text-xs text-base-content/60">{{ $role->name }}</p>
                </div>
            </div>

            <p class="text-sm text-base-content/70 mb-3">{{ $role->description }}</p>

            <div class="flex items-center justify-between mb-3">
                <span class="text-sm">Pengguna terdaftar</span>
                <x-ui.badge type="primary" size="sm">{{ $role->users_count }}</x-ui.badge>
            </div>

            <div class="divider my-2"></div>

            <div class="text-sm">
                <span class="font-medium">Permissions:</span>
                <div class="flex flex-wrap gap-1 mt-2">
                    @foreach($role->permissions ?? [] as $perm)
                        <x-ui.badge type="ghost" size="xs">{{ $perm }}</x-ui.badge>
                    @endforeach
                </div>
            </div>

            <div class="mt-3">
                @if($role->is_active)
                    <x-ui.badge type="success" size="sm">Aktif</x-ui.badge>
                @else
                    <x-ui.badge type="error" size="sm">Nonaktif</x-ui.badge>
                @endif
            </div>
        </x-ui.card>
        @endforeach
    </div>
</x-layouts.app>
