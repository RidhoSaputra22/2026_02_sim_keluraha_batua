<x-layouts.app :title="'Edit Pengguna'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Pengguna" description="Ubah data akun pengguna: {{ $user->name }}" />
    </x-slot:header>

    <x-layouts.breadcrumb :items="[
        ['label' => 'Pengguna', 'url' => route('admin.users.index')],
        ['label' => 'Edit: ' . $user->name],
    ]" />

    <x-ui.card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="name" label="Nama Lengkap" required value="{{ $user->name }}" />
                <x-ui.input name="email" label="Email" type="email" required value="{{ $user->email }}" />
                <x-ui.input name="password" label="Password Baru" type="password" placeholder="Kosongkan jika tidak diubah" helpText="Kosongkan jika tidak ingin mengubah password" />
                <x-ui.select name="role_id" label="Role" required :options="$roles->pluck('label', 'id')->toArray()" selected="{{ $user->role_id }}" />
                <x-ui.input name="nip" label="NIP" value="{{ $user->nip }}" />
                <x-ui.input name="nik" label="NIK" value="{{ $user->nik }}" />
                <x-ui.input name="phone" label="No. Telepon" value="{{ $user->phone }}" />
                <x-ui.input name="jabatan" label="Jabatan" value="{{ $user->jabatan }}" />
                <x-ui.input name="wilayah_rt" label="RT" value="{{ $user->wilayah_rt }}" />
                <x-ui.input name="wilayah_rw" label="RW" value="{{ $user->wilayah_rw }}" />
                <div class="md:col-span-2">
                    <x-ui.checkbox name="is_active" label="Status Akun" :single="true" :options="[['label' => 'Aktif']]" :checked="$user->is_active ? ['1'] : []" />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <x-ui.button type="ghost" href="{{ route('admin.users.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Perbarui Pengguna</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
