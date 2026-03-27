<x-layouts.app :title="'Peta Kelurahan'">

    <x-slot:header>
        <x-layouts.page-header title="Peta Kelurahan Batua"
            description="Peta interaktif wilayah kelurahan beserta data statistik per RW" />
    </x-slot:header>

    @push('styles')
        @include('peta.partials.styles')
    @endpush

    <div x-data="petaApp()">

        {{-- Main layout: Map left + Sidebar right --}}
        <div class="card bg-base-100 shadow-xl overflow-hidden">
            <div class="flex flex-col lg:flex-row" style="min-height: 600px;">

                {{-- LEFT: Map --}}
                @include('peta.partials.map')

                {{-- RIGHT: Sidebar --}}
                @include('peta.partials.sidebar')

            </div>
        </div>

    </div>

    @push('scripts')
        @include('peta.partials.scripts')
    @endpush

</x-layouts.app>
