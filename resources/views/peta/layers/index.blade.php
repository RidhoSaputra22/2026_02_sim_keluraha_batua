<x-layouts.app :title="'Kelola Layer Peta'">

    <x-slot:header>
        <x-layouts.page-header title="Kelola Layer Peta"
            description="Kelola layer peta kustom seperti QGIS — pilih layer di sidebar, edit polygon di peta">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('peta.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Peta Utama
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @push('styles')
    @include('peta.layers.partials.styles')
    @endpush

    <div x-data="layerManager()">

        {{-- Main QGIS-style layout: Map (left) + Sidebar (right) --}}
        <div class="card bg-base-100 shadow-xl overflow-hidden">
            <div class="flex flex-col lg:flex-row" style="min-height: 700px;">

                {{-- LEFT: Map editor --}}
                @include('peta.layers.partials.map')

                {{-- RIGHT: Layer sidebar --}}
                @include('peta.layers.partials.sidebar')

            </div>
        </div>

        {{-- Layer Settings Modal --}}
        @include('peta.layers.partials.modal-settings')

        {{-- Toast --}}
        <div x-show="toast.show" x-transition class="toast toast-top toast-end z-[9999]">
            <div class="alert" :class="toast.type === 'success' ? 'alert-success' : 'alert-error'">
                <span x-text="toast.message"></span>
            </div>
        </div>

    </div>

    @push('scripts')
    @include('peta.layers.partials.scripts')
    @endpush

</x-layouts.app>
