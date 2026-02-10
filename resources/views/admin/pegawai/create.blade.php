<x-layouts.app :title="'Tambah Pegawai'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Pegawai" description="Tambah data pegawai/staf baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.pegawai.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.pegawai.store') }}">
            @csrf

            {{-- Data Pribadi --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Pribadi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Nama Lengkap" name="nama" placeholder="Masukkan nama lengkap" value="{{ old('nama') }}" required />
                <x-ui.input label="NIK" name="nik" placeholder="Masukkan 16 digit NIK" value="{{ old('nik') }}" maxlength="16" />
                <x-ui.input label="NIP" name="nip" placeholder="Masukkan NIP" value="{{ old('nip') }}" />
                <x-ui.input label="Tanggal Lahir" name="tgl_lahir" type="date" value="{{ old('tgl_lahir') }}" />
                <x-ui.input label="No. Telepon" name="no_telp" placeholder="08xxxxxxxxxx" value="{{ old('no_telp') }}" />
                <x-ui.input label="Email" name="email" type="email" placeholder="email@contoh.com" value="{{ old('email') }}" />
                <div class="md:col-span-2">
                    <x-ui.textarea label="Alamat" name="alamat" placeholder="Alamat lengkap" value="{{ old('alamat') }}" />
                </div>
            </div>

            {{-- Data Kepegawaian --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Kepegawaian</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <x-ui.input label="Jabatan" name="jabatan" placeholder="Contoh: Kasi Pemerintahan" value="{{ old('jabatan') }}" required />
                <x-ui.input label="Pangkat" name="pangkat" placeholder="Contoh: Penata Tk. I" value="{{ old('pangkat') }}" />
                <x-ui.input label="Golongan" name="golongan" placeholder="Contoh: III/d" value="{{ old('golongan') }}" />
                <x-ui.input label="Tanggal Mulai Tugas" name="tgl_mulai" type="date" value="{{ old('tgl_mulai') }}" />
                <x-ui.input label="No. Urut" name="no_urut" type="number" placeholder="Urutan tampil" value="{{ old('no_urut') }}" min="1" />
                <x-ui.select label="Status Pegawai" name="status_pegawai" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'pensiun' => 'Pensiun', 'mutasi' => 'Mutasi']" selected="{{ old('status_pegawai', 'aktif') }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('admin.pegawai.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan Data Pegawai</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
