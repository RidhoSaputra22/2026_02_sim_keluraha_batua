<x-layouts.app :title="'Dashboard'">

    <x-slot:header>
        <x-layouts.page-header title="Dashboard" description="Ringkasan data dan aktivitas Kelurahan Batua" />
    </x-slot:header>

    @include('admin.partials.dashboard.stat-cards')
    @include('admin.partials.dashboard.map-mutasi-aksi')
    @include('admin.partials.dashboard.charts')
    @include('admin.partials.dashboard.demografi')
    @include('admin.partials.dashboard.pendidikan-fasilitas')
    @include('admin.partials.dashboard.usaha-kk')
    @include('admin.partials.dashboard.users-audit')
    @include('admin.partials.dashboard.scripts')

</x-layouts.app>
