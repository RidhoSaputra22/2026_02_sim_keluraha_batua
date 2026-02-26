<x-layouts.app :title="'Data Referensi'">
    <x-slot:header>
        <x-layouts.page-header title="Data Referensi" description="Master data referensi yang digunakan dalam sistem kelurahan" />
    </x-slot:header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($referensiData as $ref)
        <x-ui.card>
            <div class="flex items-start gap-4">
                <div class="bg-{{ $ref['color'] }}/10 p-3 rounded-lg shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-{{ $ref['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ref['icon'] }}" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-lg font-semibold">{{ $ref['title'] }}</h3>
                        <x-ui.badge :type="$ref['color']" size="lg" class="font-bold">{{ $ref['count'] }}</x-ui.badge>
                    </div>
                    <p class="text-sm text-base-content/60">{{ $ref['description'] }}</p>
                </div>
            </div>

            {{-- Tampilkan data sesuai kategori --}}
            <div class="mt-4 pt-4 border-t">
                @if($ref['title'] === 'Agama')
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $item)
                            <x-ui.badge type="primary" size="sm" :outline="true">{{ $item }}</x-ui.badge>
                        @endforeach
                    </div>
                @elseif($ref['title'] === 'Pendidikan')
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Tidak/Belum Sekolah', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'D1/D2', 'D3/Diploma', 'S1/Sarjana', 'S2/Magister', 'S3/Doktor'] as $item)
                            <x-ui.badge type="primary" size="sm" :outline="true">{{ $item }}</x-ui.badge>
                        @endforeach
                    </div>
                @elseif($ref['title'] === 'Status Kawin')
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $item)
                            <x-ui.badge type="primary" size="sm" :outline="true">{{ $item }}</x-ui.badge>
                        @endforeach
                    </div>
                @elseif($ref['title'] === 'Pekerjaan')
                    <p class="text-sm text-base-content/40 italic">Belum ada data pekerjaan yang diinput.</p>
                @endif
            </div>
        </x-ui.card>
        @endforeach
    </div>

    {{-- Info Box --}}
    <x-ui.card class="mt-6">
        <div class="flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-info shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h4 class="font-medium mb-1">Tentang Data Referensi</h4>
                <p class="text-sm text-base-content/60">Data referensi ini digunakan sebagai pilihan (dropdown/select) pada form-form di seluruh sistem. Data ini bersifat statis dan sesuai dengan standar Dukcapil/SIAK. Untuk menambah atau mengubah data referensi, silakan hubungi administrator sistem.</p>
            </div>
        </div>
    </x-ui.card>
</x-layouts.app>
