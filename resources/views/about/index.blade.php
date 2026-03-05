<x-layouts.app :title="'Tentang Aplikasi'">
    <x-slot:header>
        <x-layouts.page-header
            title="Tentang Aplikasi"
            description="Informasi umum mengenai Sistem Informasi Manajemen Kelurahan Batua" />
    </x-slot:header>

    <div class="grid grid-cols-1 gap-6">
        <x-ui.card>
            <h3 class="text-lg font-semibold mb-2">SIM RW Batua</h3>
            <p class="text-sm text-base-content/70 leading-relaxed">
                SIM RW Batua merupakan sistem informasi yang dirancang untuk mendukung
                pelayanan administrasi kelurahan secara terintegrasi dan efisien.
                Aplikasi ini mencakup pengelolaan data kependudukan, data umum, data usaha,
                serta pelaporan wilayah guna meningkatkan akurasi, transparansi, dan efektivitas layanan.
                <br><br>
                Sistem ini dikembangkan oleh Kelompok KKL XI Universitas Dipanegara Makassar
                sebagai bagian dari kontribusi dalam mendukung transformasi digital di lingkungan pemerintahan kelurahan.
            </p>

            <div class="divider my-5"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="p-4 rounded-lg bg-base-200/60">
                    <p class="text-base-content/60 mb-1">Nama Aplikasi</p>
                    <p class="font-medium">SIM RW Batua</p>
                </div>
                <div class="p-4 rounded-lg bg-base-200/60">
                    <p class="text-base-content/60 mb-1">Versi Aplikasi</p>
                    <p class="font-medium">1.3.35</p>
                </div>
            </div>
        </x-ui.card>
    </div>
</x-layouts.app>
