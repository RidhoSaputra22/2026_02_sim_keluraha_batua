<x-layouts.app title="Tambah Pengguna">
    <x-layouts.page-header title="Tambah Pengguna" :back="route('admin.users.index')" />

    <x-ui.card>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf















</x-layouts.app>    </x-ui.card>        </form>            </div>                <x-ui.button type="submit" color="primary">Simpan</x-ui.button>                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Batal</a>            <div class="mt-6 flex justify-end gap-2">
            </div>                    selected="{{ old('role_id') }}" required />                <x-ui.select name="role_id" label="Role" :options="$roles->pluck('label', 'id')"                <x-ui.input name="password" label="Password" type="password" required />                <x-ui.input name="email" label="Email" type="email" value="{{ old('email') }}" required />                <x-ui.input name="name" label="Nama Lengkap" value="{{ old('name') }}" required />            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
