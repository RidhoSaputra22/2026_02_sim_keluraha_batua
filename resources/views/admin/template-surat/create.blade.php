<x-layouts.app :title="'Tambah Template Surat'">
    <x-slot:header>
        <x-layouts.page-header title="Tambah Template Surat" description="Buat template baru untuk jenis surat tertentu">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.template-surat.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.template-surat.store') }}">
            @csrf

            {{-- Informasi Template --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Template</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Nama Template" name="nama" placeholder="Contoh: Template SKTM Standard" value="{{ old('nama') }}" required />

                @php
                    $jenisSuratOptions = $jenisSuratList->pluck('nama', 'id')->toArray();
                @endphp
                <x-ui.select label="Jenis Surat" name="jenis_surat_id" placeholder="Pilih Jenis Surat" :options="$jenisSuratOptions" selected="{{ old('jenis_surat_id') }}" />
            </div>

            {{-- Isi Template --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Isi Template</h3>
            <div class="mb-6">
                <x-ui.textarea label="Isi Template (HTML)" name="isi_template" rows="12" placeholder="Masukkan isi template surat dalam format HTML. Gunakan placeholder seperti {nama}, {nik}, {alamat} untuk field dinamis." required>{{ old('isi_template') }}</x-ui.textarea>
                <p class="text-sm text-base-content/60 mt-1">Gunakan placeholder dalam kurung kurawal untuk field yang akan diisi otomatis, misal: <code class="text-primary">{nama}</code>, <code class="text-primary">{nik}</code>, <code class="text-primary">{alamat}</code></p>
            </div>

            {{-- Field Mapping --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Field Mapping (Opsional)</h3>
            <div class="mb-6">
                <x-ui.textarea label="Field Mapping (JSON)" name="field_mapping" rows="4" placeholder='{"nama": "data_penduduk.nama", "nik": "data_penduduk.nik", "alamat": "data_penduduk.alamat"}'>{{ old('field_mapping') }}</x-ui.textarea>
                <p class="text-sm text-base-content/60 mt-1">Definisikan pemetaan antara placeholder template dan kolom database dalam format JSON.</p>
            </div>

            {{-- Pengaturan --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Pengaturan</h3>
            <div class="flex flex-wrap gap-6 mb-6">
                <x-ui.checkbox label="Jadikan template default untuk jenis surat ini" name="is_default" value="1" :checked="old('is_default')" />
                <x-ui.checkbox label="Template aktif (dapat digunakan)" name="is_active" value="1" :checked="old('is_active', true)" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('admin.template-surat.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Simpan Template</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
