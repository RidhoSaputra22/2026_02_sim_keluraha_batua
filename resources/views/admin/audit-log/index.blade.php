<x-layouts.app :title="'Audit Log'">
    <x-slot:header>
        <x-layouts.page-header title="Audit Log" description="Riwayat aktivitas sistem" />
    </x-slot:header>

    <x-ui.card>
        <div class="text-center py-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-base-content/30 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-lg font-semibold text-base-content/60 mb-2">Audit Log</h3>
            <p class="text-base-content/40 max-w-md mx-auto">
                Fitur audit log akan mencatat semua perubahan data pada sistem. Setiap aktivitas pengguna akan tercatat secara otomatis untuk keperluan akuntabilitas dan keamanan.
            </p>
            <div class="mt-6">
                <x-ui.badge type="info" size="md">Segera Hadir</x-ui.badge>
            </div>
        </div>
    </x-ui.card>
</x-layouts.app>
