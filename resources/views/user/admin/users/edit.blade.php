<x-layouts.app title="Edit Pengguna">
    <x-layouts.page-header title="Edit Pengguna: {{ $user->name }}" :back="route('admin.users.index')" />

    <x-ui.card>
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="name" label="Nama Lengkap" value="{{ old('name', $user->name) }}" required />
                <x-ui.input name="email" label="Email" type="email" value="{{ old('email', $user->email) }}" required />
                <x-ui.input name="password" label="Password (kosongkan jika tidak diubah)" type="password" />
                <x-ui.select name="role_id" label="Role" :options="$roles->pluck('label', 'id')"
                    selected="{{ old('role_id', $user->role_id) }}" required />
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Batal</a>
                <x-ui.button type="submit" color="primary">Perbarui</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
