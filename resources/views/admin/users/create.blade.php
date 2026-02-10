<x-layouts.app :title="'Tambah Pengguna'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Pengguna" description="Buat akun pengguna baru" />
    </x-slot:header>

    <x-layouts.breadcrumb :items="[
        ['label' => 'Pengguna', 'url' => route('admin.users.index')],
        ['label' => 'Tambah Pengguna'],
    ]" />

    <x-ui.card class="max-w-3xl">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="name" label="Nama Lengkap" required placeholder="Masukkan nama lengkap" />
                <x-ui.input name="email" label="Email" type="email" required placeholder="contoh@email.com" />
                <x-ui.input name="password" label="Password" type="password" required placeholder="Minimal 8 karakter" />
                <x-ui.select name="role_id" label="Role" required :options="$roles->pluck('label', 'id')->toArray()" placeholder="Pilih role..." />
                <x-ui.input name="nip" label="NIP" placeholder="Nomor Induk Pegawai" />
                <x-ui.input name="nik" label="NIK" placeholder="Nomor Induk Kependudukan" />
                <x-ui.input name="phone" label="No. Telepon" placeholder="08xxxxxxxxxx" />
                <x-ui.input name="jabatan" label="Jabatan" placeholder="Jabatan pengguna" />
                <x-ui.input name="wilayah_rt" label="RT" placeholder="Nomor RT" />
                <x-ui.input name="wilayah_rw" label="RW" placeholder="Nomor RW" />
                <div class="md:col-span-2">
                    <x-ui.checkbox name="is_active" label="Status Akun" :single="true" :options="[['label' => 'Aktif']]" :checked="['1']" />
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <x-ui.button type="ghost" href="{{ route('admin.users.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan Pengguna</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
