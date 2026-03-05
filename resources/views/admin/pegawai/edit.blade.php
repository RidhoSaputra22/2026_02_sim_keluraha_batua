<x-layouts.app title="Edit Pegawai">
    <x-layouts.page-header title="Edit Pegawai: {{ $pegawai->nama }}" :back="route('master.pegawai.index')" />

    <x-ui.card>
        <form method="POST" action="{{ route('master.pegawai.update', $pegawai) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="nip" label="NIP" value="{{ old('nip', $pegawai->nip) }}" required />
                <x-ui.input name="nama" label="Nama Lengkap" value="{{ old('nama', $pegawai->nama) }}" required />
                <x-ui.input name="jabatan" label="Jabatan" value="{{ old('jabatan', $pegawai->jabatan) }}" required />
                <x-ui.input name="gol" label="Golongan" value="{{ old('gol', $pegawai->gol) }}" />
                <x-ui.input name="pangkat" label="Pangkat" value="{{ old('pangkat', $pegawai->pangkat) }}" />
                <x-ui.select name="status_pegawai" label="Status"
                    :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']"
                    selected="{{ old('status_pegawai', $pegawai->status_pegawai) }}" required />
                <x-ui.input name="no_urut" label="No Urut" type="number" value="{{ old('no_urut', $pegawai->no_urut) }}" />
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <a href="{{ route('master.pegawai.index') }}" class="btn btn-ghost">Batal</a>
                <x-ui.button type="submit" color="primary">Perbarui</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
