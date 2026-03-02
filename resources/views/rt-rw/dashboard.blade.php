<x-layouts.app :title="'Dashboard RT/RW'">

    <x-slot:header>
        <x-layouts.page-header title="Dashboard {{ $wilayahLabel }}" description="Ringkasan data warga dan aktivitas di wilayah Anda" />
    </x-slot:header>

    @include('rt-rw.partials.dashboard.stat-cards')
    @include('rt-rw.partials.dashboard.map-mutasi-aksi')
    @include('rt-rw.partials.dashboard.charts')
    @include('rt-rw.partials.dashboard.demografi')
    @include('rt-rw.partials.dashboard.fasilitas-warga')
    @include('rt-rw.partials.dashboard.recent-events')
    @include('rt-rw.partials.dashboard.scripts')

</x-layouts.app>
