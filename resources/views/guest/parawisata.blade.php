<x-guest::layout.app title="Parawisata">

    {{-- HERO SECTION --}}
    <x-guest::ui.hero size="lg" background="white">
        <x-slot:title>
            Jelajahi Pesona <span class="text-primary">Wisata Kelurahan</span> Kami
        </x-slot:title>

        <x-slot:subtitle>
            Temukan keindahan alam, kekayaan budaya, dan cita rasa kuliner autentik langsung di jantung kelurahan kami.
        </x-slot:subtitle>

        <x-slot:actions>
            <x-guest::ui.search-bar placeholder="Cari destinasi, kuliner, atau event..." button-text="Cari" action="#" />
        </x-slot:actions>
    </x-guest::ui.hero>

    {{-- MAIN CONTENT LAYOUT --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex flex-col lg:flex-row gap-12">
            {{-- Destination Grid --}}
            <div class="lg:w-2/3">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-2xl font-bold text-slate-900">Destinasi Populer</h2>
                    <div class="flex gap-2">
                        <x-guest::ui.button variant="outline" size="sm" icon="grid_view" class="!px-2" />
                        <x-guest::ui.button variant="outline" size="sm" icon="list" class="!px-2" />
                    </div>
                </div>

                @php
                $destinations = [
                ['title' => 'Hutan Pinus Desa', 'category' => 'ALAM', 'desc' => 'Nikmati udara segar dan pemandangan
                hijau yang menenangkan di kawasan konservasi hutan pinus lokal kami.', 'img' =>
                'https://lh3.googleusercontent.com/aida-public/AB6AXuAx4BAlh-i2bGA6cdzzBppKuycdApxc7w4pOD_BwEBsI4NqEDE2i169Xlx7oD4TAjLrMv9TpGxMawzPJuKqdxYACYdbgDg1GZTgHzidT0oLRqmU33qrJoFyFo9iUCcoHke2ZeELwbntqWBJC2kAjFIF6xtHymTRFOiO3NHyBMeZtjdSlqoGTl-JBf_P2yvosUQwHIFKChNRl90AUoTEyfw8DaC3AUameTfLxW-4SRctPySoUQZP0jlo-r58166UbM4Fu5u7pZTZ6Y-M'],
                ['title' => 'Sentra Kuliner Lokal', 'category' => 'KULINER', 'desc' => 'Jelajahi berbagai hidangan khas
                kelurahan yang menggugah selera dengan bumbu warisan turun-temurun.', 'img' =>
                'https://lh3.googleusercontent.com/aida-public/AB6AXuCK5Z634oQ1NLYrdwiPRR5t82TGLPO8t5EHKbOP6Bvr9w82q8cJvMYXDKpFCN-LhAHEzXkXaUTkqCIx8ilqMK_xfFwZRLfU1vKgPel9Wq1hilTF5CaFMAsyrF-M7j0KElDiPiPge8UoPRXybZquRgXveApDRKLGYNeJA_N0oQITHIV6odM-SIiNdiG7LFhGvFIfZ0EhvNPzwqEQE6jOU0Vi0MM0aGq3uuP36IquPzIldxC3WDedcjaIhfjMpQj_gWEC_BRtgFwNMqaU'],
                ['title' => 'Situs Cagar Budaya', 'category' => 'SEJARAH', 'desc' => 'Menelusuri jejak sejarah dan
                arsitektur kuno yang masih terawat dengan baik di wilayah kelurahan.', 'img' =>
                'https://lh3.googleusercontent.com/aida-public/AB6AXuAXROqUyqvSJP9e0zH4-4xRSdzLW7uLrm5tFwHVDdsJO5GnBOrJ1jcLZy5ZS1FLa1abYzMBnyggN0RSFmMlsJEjapjoFolUd98lQse5kG1DyNAzTJsi-c__xLKcYmK7Ajzz2NTLwp0_WW_ahI2k_VBOdNnY18tjUwSRBlkgX88-lx90w9wGJ27isBnjm09oEYPLDb_PBWiQm4anR66a7P0vkRkbDbZh5tb2HNQ0I1Pj6OhACl3SEjOgYzKvLYOyFes6Y7rfZuJs3NJh'],
                ['title' => 'Taman Teratai Kelurahan', 'category' => 'REKREASI', 'desc' => 'Area terbuka hijau yang
                ideal untuk piknik keluarga dan olahraga ringan di sore hari.', 'img' =>
                'https://lh3.googleusercontent.com/aida-public/AB6AXuDxtdwFV-h-eeKTf74MjYBjjWodw9viiTORrY6HgwX8239VK9Jxj_7ww4omBGJOdEnJU8E3RktP9NCTx80BQl5FOwxrE_IWs8kb1KCndiEQz0volxRc8dyn_FVxlzyYLcnPLy62phecoEbSSGzAiFUSi5vAm_k9MMVXi-6A3UjgPr2zvFdqwcuY2mcLeOc5gL1EvPKNmqV-rdsatDgL-mTKAzWRaaJjaXmhWtpvxMdNOJVwruRDleGgB4YA2e_GmAts6BRGcOy29gP-'],
                ['title' => 'Sentra Batik Tulis', 'category' => 'KERAJINAN', 'desc' => 'Lihat langsung proses pembuatan
                kain batik tradisional oleh pengrajin lokal berbakat kami.', 'img' =>
                'https://lh3.googleusercontent.com/aida-public/AB6AXuAklObp0FubFT-pTXIvGgluKOcEVOTHltetyFTgq9-Zwd9eUJksQRLidJTICo9AXr1I4ePqOil183j-icpRlo6FfVh6M2oBhnSrbpXEzuTNriPTL7Fmz9jMstRwjNMlWMsyjN7ghdu2Qr9N8TxuiUh-06ESbKLlf1RzJBuhfkEntBbqOgfDyVxS4pweLDe0LfZrTh4Bj_5AL2XB2t52GhraxZ3BqhU3djCDMq_SWtUPF3RxnikDRU39T48rdX1tEqAujksQaNGnwME_'],
                ['title' => 'Embung Tirta Kencana', 'category' => 'WISATA AIR', 'desc' => 'Waduk kecil yang asri dengan
                fasilitas memancing dan spot foto matahari terbenam yang indah.', 'img' =>
                'https://lh3.googleusercontent.com/aida-public/AB6AXuDusGsxnHFojvYNMYy7t0w4QmfvjN0roc5c9UcZ7ZSiyqoolP0qWb7BipbvgwNpGdk-olQRT31MtP6L3oYOPqVe8t4ljtqoS-46bVlokxVdOTprFOv-7AuK8uy3VALic9jHFux8XECE1qH5oX2GWP_eprmTi0oMT3fsTK1POpnerCtHGss4Cry_DN0uFuxZtRSxV3_M6oUDGnbsrBiYdfBOlriBpN5AczZVW6uK0e6kD9a3zTT4Ir-666nsfQhGqJT6Cn_Zp5dz9KT2'],
                ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach($destinations as $dest)
                    <x-guest::ui.card variant="bordered" padding="none"
                        class="group overflow-hidden flex flex-col hover:shadow-2xl hover:shadow-slate-200/50 transition-all duration-300">
                        <div class="relative aspect-[16/10] overflow-hidden">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                alt="{{ $dest['title'] }}" src="{{ $dest['img'] }}" />
                            <div class="absolute top-4 left-4">
                                <x-guest::ui.badge variant="primary" size="sm">{{ $dest['category'] }}</x-guest::ui.badge>
                            </div>
                        </div>
                        <div class="p-6 flex flex-col flex-grow">
                            <h3
                                class="text-xl font-bold text-slate-900 mb-2 group-hover:text-primary transition-colors">
                                {{ $dest['title'] }}</h3>
                            <p class="text-slate-500 text-sm mb-6 flex-grow leading-relaxed">{{ $dest['desc'] }}</p>
                            <x-guest::ui.button size="sm">Lihat Detail</x-guest::ui.button>
                        </div>
                    </x-guest::ui.card>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12 flex justify-center">
                    <nav class="inline-flex -space-x-px">
                        <a class="px-4 py-2 bg-white border border-slate-200 text-slate-400 rounded-l-lg hover:bg-slate-50"
                            href="#">Prev</a>
                        <a class="px-4 py-2 bg-primary text-white border border-primary font-medium" href="#">1</a>
                        <a class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50"
                            href="#">2</a>
                        <a class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50"
                            href="#">3</a>
                        <a class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-r-lg hover:bg-slate-50"
                            href="#">Next</a>
                    </nav>
                </div>
            </div>

            {{-- Sidebar --}}
            <aside class="lg:w-1/3 space-y-8">
                {{-- Recommendations Widget --}}
                <x-guest::ui.card variant="bordered" padding="lg">
                    <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <x-guest::ui.icon name="auto_awesome" color="text-yellow-500" />
                        Rekomendasi Pekan Ini
                    </h3>

                    @php
                    $recommendations = [
                    ['title' => 'Bukit Pandang Sunrise', 'rating' => '4.9', 'reviews' => '128', 'info' => 'Rp 15.000 /
                    orang', 'img' =>
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuCqHc6Rx7gyP4ANreaZnzUFL8B6STqNICRmjVui4LQNbatXQQ-HVdFlwXf21sEH94cEErgcHEpbTKIRHth8tHbsbH9kc1gWPyEQujbyaMWo-KtW7tNE2CgTnB6-2mXJNLXDKkiQCkPXltP4IH5Z18l_vuFwvzNf18nv7-4HyVrJsUVV9n0javtkyMS9tt6TwP1-V-LtM_E-ix-0JL7lp45-L5dW1Bo-O6CtemiGyBBZijYtXRt1HkSks4ScbbCwDj18h8TUbpHKg-z1'],
                    ['title' => 'Kedai Kopi Luwak Desa', 'rating' => '4.7', 'reviews' => '210', 'info' => 'Buka 08.00 -
                    20.00', 'img' =>
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuCvXLKiNUq3zRfmD_bl7TJ-lQNFEor6uPVPvcw1qm8lD9eWgS034kkFvkBj9JsnmpD-E66tqmNlsg63PwM1ejk1fMAZFacwLtyA5HEQ2c0ZY72b5a1jiaxQbPMCbkuQwYI1wvSIBIHIZDodnG8Su44qjf3-okQbXyGwEUSfx8aP6fkrJyCddDplHoutoa-Thf04Ki6uWlwPEgFdem_UWL48hWoBA6nguTTaYWJScxD0qu3LteKio0m1DG6zGwG_Fuk9XZTqEzubwR6z'],
                    ['title' => 'Pasar Seni Akhir Pekan', 'rating' => '4.8', 'reviews' => '89', 'info' => 'Event
                    Sab-Min', 'img' =>
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuAOL6IxHVE0E5mjhkQz0qlB-QyX12VRjMvl9Qm6FEEHlAvKqjUtb6yS3C8uJs2fYH5yIQmoaodzoz8L-x2LdI7tJa8ScTrKsBTnB4e-Uqe39kl9h2y3yCZxpUilUApWNrBSWwlQzQEfJXYW2fM4fiMYBFub8qSG-n-1h8n8CXtS3hF0d5q1qXd8SOm0kHNdGXdpVIjYtrQl3Gl23VMvqnZfc77FAprM0FzI51CV2FgMuAa1q3duTTEcLher6qJ3mINqXaXoUi2J4Hxw'],
                    ];
                    @endphp

                    <div class="space-y-6">
                        @foreach($recommendations as $rec)
                        <div class="flex gap-4 group cursor-pointer">
                            <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0">
                                <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                    alt="{{ $rec['title'] }}" src="{{ $rec['img'] }}" />
                            </div>
                            <div>
                                <h4
                                    class="font-bold text-slate-900 group-hover:text-primary transition-colors text-sm line-clamp-1">
                                    {{ $rec['title'] }}</h4>
                                <div class="flex items-center gap-1 my-1">
                                    <x-guest::ui.icon name="star" size="sm" color="text-yellow-400" />
                                    <span class="text-xs font-semibold text-slate-600">{{ $rec['rating'] }}</span>
                                    <span class="text-xs text-slate-400">({{ $rec['reviews'] }} ulasan)</span>
                                </div>
                                <span class="text-xs text-primary font-medium">{{ $rec['info'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <x-guest::ui.button variant="outline" class="w-full mt-8">Lihat Semua Rekomendasi</x-guest::ui.button>
                </x-guest::ui.card>

                {{-- Newsletter --}}
                <x-guest::ui.card class="bg-primary/5 border-primary/10" padding="lg">
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Ingin Update Terbaru?</h3>
                    <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                        Dapatkan informasi mengenai event budaya dan promo wisata kelurahan langsung di email Anda.
                    </p>
                    <div class="space-y-3">
                        <x-guest::ui.input name="email" type="email" placeholder="Alamat Email" />
                        <x-guest::ui.button class="w-full">Berlangganan</x-guest::ui.button>
                    </div>
                </x-guest::ui.card>

                {{-- Location Map Widget --}}
                <x-guest::ui.card variant="bordered" padding="lg" class="overflow-hidden">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Lokasi Kami</h3>
                    <div class="w-full aspect-video rounded-lg overflow-hidden bg-slate-100 relative">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <x-guest::ui.icon name="location_on" size="xl" color="text-slate-300" />
                        </div>
                        <img class="w-full h-full object-cover opacity-50" alt="Peta wilayah kelurahan"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCL6soMkHBbI4u4py2YI-t5vg0_2Nl4aHPSH0OBx15YMsQWOux9EOtLbH-JTG0lxOptXDg1NXVmgCaqzMoVnoi4dOstBdULn4-uDaJIjSmqNHSNq8i__giRsS68QEOJr67uLsnBi9QGTIHA7Zf242T4Vtl884MT-2DvqNrWxPCx5hEmvOczIvSu3b60swmuS4MFSp5umPBVIRUTd5S_v4SkRmxW-O4KWcaTDFGRCfol-6uwesQGbssCHGAei8f6AWmeeMRQzhHWL_J9" />
                    </div>
                    <p class="mt-4 text-xs text-slate-500">
                        Jl. Utama Kelurahan No. 123, Kecamatan Jaya, Kota Berdikari.
                    </p>
                </x-guest::ui.card>
            </aside>
        </div>
    </main>

</x-guest::layout.app>