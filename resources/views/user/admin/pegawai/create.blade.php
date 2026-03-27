<x-layouts.app title="Tambah Pegawai">
    <x-layouts.page-header title="Tambah Pegawai" :back="route('master.pegawai.index')" />

    <x-ui.card>
        <form method="POST" action="{{ route('master.pegawai.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="nip" label="NIP" value="{{ old('nip') }}" required />
                <x-ui.input name="nama" label="Nama Lengkap" value="{{ old('nama') }}" required />
                <x-ui.input name="jabatan" label="Jabatan" value="{{ old('jabatan') }}" required />
                <x-ui.input name="gol" label="Golongan" value="{{ old('gol') }}" />
                <x-ui.input name="pangkat" label="Pangkat" value="{{ old('pangkat') }}" />
                <x-ui.select name="status_pegawai" label="Status"
                    :options="\App\Enums\StatusAktifEnum::options()"
                    selected="{{ old('status_pegawai', \App\Enums\StatusAktifEnum::AKTIF->value) }}" required />
                <x-ui.input name="no_urut" label="No Urut" type="number" value="{{ old('no_urut') }}" />
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <a href="{{ route('master.pegawai.index') }}" class="btn btn-ghost">Batal</a>
                <x-ui.button type="submit" color="primary">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
