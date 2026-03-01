<x-layouts.app :title="'Buat Layer Peta'">

    <x-slot:header>
        <x-layouts.page-header title="Buat Layer Peta Baru"
            description="Tentukan nama, warna, dan pola arsir untuk layer baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('admin.peta-layer.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        <form method="POST" action="{{ route('admin.peta-layer.store') }}">
            @csrf
            @include('peta.layers._form')

            <div class="flex justify-end gap-2 mt-6">
                <x-ui.button type="ghost" href="{{ route('admin.peta-layer.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Simpan Layer
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>

</x-layouts.app>
