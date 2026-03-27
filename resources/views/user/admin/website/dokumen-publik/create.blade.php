<x-layouts.app title="Tambah Dokumen Publik">
    <x-layouts.page-header title="Tambah Dokumen Publik" description="Unggah dokumen yang bisa diunduh warga">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.website.dokumen-publik.index') }}" type="ghost">Kembali</x-ui.button>
        </x-slot:actions>
    </x-layouts.page-header>

    <form method="POST" action="{{ route('admin.website.dokumen-publik.store') }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @include('admin.website.dokumen-publik.form')
    </form>
</x-layouts.app>
