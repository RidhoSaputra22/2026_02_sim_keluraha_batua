<x-layouts.app title="Tambah Destinasi Wisata">
    <x-layouts.page-header title="Tambah Destinasi Wisata" description="Masukkan rekomendasi tempat atau kuliner untuk halaman wisata">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.website.destinasi-wisata.index') }}" type="ghost">Kembali</x-ui.button>
        </x-slot:actions>
    </x-layouts.page-header>

    <form method="POST" action="{{ route('admin.website.destinasi-wisata.store') }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @include('admin.website.destinasi-wisata.form')
    </form>
</x-layouts.app>
