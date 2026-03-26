<x-layouts.app title="Edit Dokumen Publik">
    <x-layouts.page-header title="Edit Dokumen Publik" description="Perbarui file dan metadata dokumen publik">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.website.dokumen-publik.index') }}" type="ghost">Kembali</x-ui.button>
        </x-slot:actions>
    </x-layouts.page-header>

    <form method="POST" action="{{ route('admin.website.dokumen-publik.update', $dokumenPublik) }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @method('PUT')
        @include('admin.website.dokumen-publik.form')
    </form>
</x-layouts.app>
