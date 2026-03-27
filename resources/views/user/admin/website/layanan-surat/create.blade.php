<x-layouts.app title="Tambah Layanan Surat">
    <x-layouts.page-header title="Tambah Layanan Surat" description="Atur persyaratan yang akan ditampilkan di halaman surat online">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.website.layanan-surat.index') }}" type="ghost">Kembali</x-ui.button>
        </x-slot:actions>
    </x-layouts.page-header>

    <form method="POST" action="{{ route('admin.website.layanan-surat.store') }}" class="mt-6">
        @csrf
        @include('admin.website.layanan-surat.form')
    </form>
</x-layouts.app>
