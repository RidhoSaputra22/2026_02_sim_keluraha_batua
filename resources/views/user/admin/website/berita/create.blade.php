<x-layouts.app title="Tambah Berita">
    <x-layouts.page-header title="Tambah Berita" description="Buat konten baru untuk website publik">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.website.berita.index') }}" type="ghost">Kembali</x-ui.button>
        </x-slot:actions>
    </x-layouts.page-header>

    <form method="POST" action="{{ route('admin.website.berita.store') }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @include('admin.website.berita.form')
    </form>
</x-layouts.app>
