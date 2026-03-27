<x-layouts.app title="Edit Destinasi Wisata">
    <x-layouts.page-header title="Edit Destinasi Wisata" description="Perbarui konten wisata dan rekomendasi destinasi">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.website.destinasi-wisata.index') }}" type="ghost">Kembali</x-ui.button>
        </x-slot:actions>
    </x-layouts.page-header>

    <form method="POST" action="{{ route('admin.website.destinasi-wisata.update', $destinasiWisata) }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @method('PUT')
        @include('admin.website.destinasi-wisata.form')
    </form>
</x-layouts.app>
