<x-layouts.app title="Edit Berita">
    <x-layouts.page-header title="Edit Berita" description="Perbarui konten berita yang tampil di website publik">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.website.berita.index') }}" type="ghost">Kembali</x-ui.button>
        </x-slot:actions>
    </x-layouts.page-header>

    <form method="POST" action="{{ route('admin.website.berita.update', $berita) }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @method('PUT')
        @include('admin.website.berita.form')
    </form>
</x-layouts.app>
