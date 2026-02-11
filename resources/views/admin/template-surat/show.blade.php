<x-layouts.app :title="'Detail Template Surat'">
    <x-slot:header>
        <x-layouts.page-header title="Detail Template Surat" description="{{ $templateSurat->nama }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.template-surat.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
                <x-ui.button type="primary" size="sm" href="{{ route('admin.template-surat.edit', $templateSurat) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Info --}}
        <x-ui.card class="lg:col-span-1">
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Informasi</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-base-content/60">Jenis Surat</label>
                    <p><span class="badge badge-outline">{{ $templateSurat->jenisSurat->nama ?? '-' }}</span></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-base-content/60">Status</label>
                    <p>
                        @if($templateSurat->is_active)
                            <span class="badge badge-success badge-sm">Aktif</span>
                        @else
                            <span class="badge badge-error badge-sm">Nonaktif</span>
                        @endif
                        @if($templateSurat->is_default)
                            <span class="badge badge-primary badge-sm ml-1">Default</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-base-content/60">Dibuat Pada</label>
                    <p class="text-sm">{{ $templateSurat->created_at?->format('d F Y, H:i') ?? '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-base-content/60">Terakhir Diperbarui</label>
                    <p class="text-sm">{{ $templateSurat->updated_at?->format('d F Y, H:i') ?? '-' }}</p>
                </div>
                @if($templateSurat->field_mapping)
                <div>
                    <label class="text-sm font-medium text-base-content/60">Field Mapping</label>
                    <pre class="bg-base-200 p-3 rounded-lg text-xs mt-1 overflow-x-auto">{{ json_encode($templateSurat->field_mapping, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif
            </div>
        </x-ui.card>

        {{-- Preview Template --}}
        <x-ui.card class="lg:col-span-2">
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Preview Template</h3>
            <div class="prose prose-sm max-w-none bg-base-200 p-6 rounded-lg">
                {!! $templateSurat->isi_template !!}
            </div>
        </x-ui.card>
    </div>
</x-layouts.app>
