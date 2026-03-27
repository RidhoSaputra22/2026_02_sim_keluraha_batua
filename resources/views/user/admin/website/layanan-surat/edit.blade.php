<x-layouts.app title="Edit Layanan Surat">
    <x-layouts.page-header title="Edit Layanan Surat" description="Perbarui informasi dan daftar persyaratan surat">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.website.layanan-surat.index') }}" type="ghost">Kembali</x-ui.button>
        </x-slot:actions>
    </x-layouts.page-header>

    <form method="POST" action="{{ route('admin.website.layanan-surat.update', $layananSurat) }}" class="mt-6">
        @csrf
        @method('PUT')
        @include('admin.website.layanan-surat.form')
    </form>
</x-layouts.app>
