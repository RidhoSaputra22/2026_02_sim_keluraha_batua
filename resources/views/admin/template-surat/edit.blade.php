<x-layouts.app :title="'Edit Template Surat'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Template Surat" description="Ubah template &quot;{{ $templateSurat->nama }}&quot;">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('master.template-surat.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('master.template-surat.update', $templateSurat) }}">
            @csrf @method('PUT')

            {{-- Informasi Template --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi Template</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="Nama Template" name="nama" value="{{ old('nama', $templateSurat->nama) }}" required />

                @php
                    $jenisSuratOptions = $jenisSuratList->pluck('nama', 'id')->toArray();
                @endphp
                <x-ui.select label="Jenis Surat" name="jenis_surat_id" placeholder="Pilih Jenis Surat" :options="$jenisSuratOptions" selected="{{ old('jenis_surat_id', $templateSurat->jenis_surat_id) }}" />
            </div>

            {{-- Isi Template --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Isi Template</h3>
            <div class="mb-6">
                <x-ui.textarea label="Isi Template (HTML)" name="isi_template" rows="12" required>{{ old('isi_template', $templateSurat->isi_template) }}</x-ui.textarea>
                <p class="text-sm text-base-content/60 mt-1">Gunakan placeholder dalam kurung kurawal untuk field dinamis: <code class="text-primary">{nama}</code>, <code class="text-primary">{nik}</code>, <code class="text-primary">{alamat}</code></p>
            </div>

            {{-- Field Mapping --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Field Mapping (Opsional)</h3>
            <div class="mb-6">
                <x-ui.textarea label="Field Mapping (JSON)" name="field_mapping" rows="4">{{ old('field_mapping', $templateSurat->field_mapping ? json_encode($templateSurat->field_mapping, JSON_PRETTY_PRINT) : '') }}</x-ui.textarea>
                <p class="text-sm text-base-content/60 mt-1">Definisikan pemetaan antara placeholder template dan kolom database dalam format JSON.</p>
            </div>

            {{-- Pengaturan --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Pengaturan</h3>
            <div class="flex flex-wrap gap-6 mb-6">
                <x-ui.checkbox label="Jadikan template default untuk jenis surat ini" name="is_default" value="1" :checked="old('is_default', $templateSurat->is_default)" />
                <x-ui.checkbox label="Template aktif (dapat digunakan)" name="is_active" value="1" :checked="old('is_active', $templateSurat->is_active)" />
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('master.template-surat.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">Update Template</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
