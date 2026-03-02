<x-layouts.app :title="'Tambah Data PBB'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Data PBB" description="Tambah data Pajak Bumi dan Bangunan baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('data-umum.pbb.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('data-umum.pbb.store') }}">
            @csrf

            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Wajib Pajak</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @php $kelurahanOptions = $kelurahanList->pluck('nama', 'id')->toArray(); @endphp
                <x-ui.select label="Kelurahan" name="kelurahan_id" placeholder="Pilih Kelurahan"
                    :options="$kelurahanOptions" selected="{{ old('kelurahan_id') }}" required />
                <x-ui.input label="Nama Wajib Pajak" name="nama_wajib_pajak" placeholder="Nama lengkap wajib pajak"
                    value="{{ old('nama_wajib_pajak') }}" required />
                @php $rtOptions = $rtList->mapWithKeys(fn($rt) => [$rt->id => 'RT ' . $rt->nomor . ' / RW ' . ($rt->rw->nomor ?? '-')])->toArray(); @endphp
                <x-ui.select label="RT" name="rt_id" placeholder="Pilih RT" :options="$rtOptions"
                    selected="{{ old('rt_id') }}" />
                @if ($rwList->isNotEmpty())
                    @php $rwOptions = $rwList->mapWithKeys(fn($rw) => [$rw->id => 'RW ' . $rw->nomor])->toArray(); @endphp
                    <x-ui.select label="RW" name="rw_id" placeholder="Pilih RW" :options="$rwOptions"
                        selected="{{ old('rw_id') }}" />
                @endif
            </div>

            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Detail Pajak</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Objek Pajak" name="objek_pajak" placeholder="Deskripsi objek pajak"
                    value="{{ old('objek_pajak') }}" />
                <x-ui.input label="NOB (Nomor Objek Bangunan)" name="nob" placeholder="Nomor objek bangunan"
                    value="{{ old('nob') }}" />
                <x-ui.input label="Beban (Rp)" name="beban" type="number" placeholder="0"
                    value="{{ old('beban', 0) }}" />
                <x-ui.select label="Status" name="status" placeholder="Pilih Status"
                    :options="['Lunas' => 'Lunas', 'Belum' => 'Belum']" selected="{{ old('status', 'Belum') }}"
                    required />
                <x-ui.input label="Tahun Pajak" name="tahun_pajak" placeholder="Contoh: 2026"
                    value="{{ old('tahun_pajak', date('Y')) }}" />
            </div>

            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Keterangan</h3>
            <div class="mb-6">
                <x-ui.textarea name="keterangan" placeholder="Keterangan tambahan (opsional)"
                    value="{{ old('keterangan') }}" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('data-umum.pbb.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary" :isSubmit="true">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
