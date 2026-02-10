<x-layouts.app :title="'Edit Penandatangan'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Penandatangan" description="Ubah data {{ $penandatangan->nama }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.penandatangan.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.penandatangan.update', $penandatangan) }}">
            @csrf @method('PUT')

            {{-- Data Pribadi --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Pribadi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Nama Lengkap" name="nama" value="{{ old('nama', $penandatangan->nama) }}" required />
                <x-ui.input label="NIP" name="nip" value="{{ old('nip', $penandatangan->nip) }}" />
                <x-ui.input label="NIK" name="nik" value="{{ old('nik', $penandatangan->nik) }}" maxlength="16" />
                <x-ui.input label="No. Telepon" name="no_telp" value="{{ old('no_telp', $penandatangan->no_telp) }}" />
                <x-ui.input label="Email" name="email" type="email" value="{{ old('email', $penandatangan->email) }}" />
                <div></div>
                <div class="md:col-span-2">
                    <x-ui.textarea label="Alamat" name="alamat" value="{{ old('alamat', $penandatangan->alamat) }}" />
                </div>
            </div>

            {{-- Data Jabatan --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Jabatan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <x-ui.input label="Jabatan" name="jabatan" value="{{ old('jabatan', $penandatangan->jabatan) }}" required />
                <x-ui.input label="Pangkat" name="pangkat" value="{{ old('pangkat', $penandatangan->pangkat) }}" />
                <x-ui.input label="Golongan" name="golongan" value="{{ old('golongan', $penandatangan->golongan) }}" />
                <x-ui.input label="Tanggal Mulai" name="tgl_mulai" type="date" value="{{ old('tgl_mulai', $penandatangan->tgl_mulai) }}" />
                <x-ui.input label="Tanggal Selesai" name="tgl_selesai" type="date" value="{{ old('tgl_selesai', $penandatangan->tgl_selesai) }}" />
                <x-ui.select label="Status" name="status" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" selected="{{ old('status', $penandatangan->status) }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('admin.penandatangan.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Update Penandatangan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
