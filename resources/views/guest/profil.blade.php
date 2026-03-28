<x-guest::layout.app title="Profil Kelurahan">

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- HERO HEADER --}}
        <x-guest::ui.hero size="md" background="white">
            <x-slot:title>Profil Kelurahan</x-slot:title>

            <x-slot:subtitle>
                Membangun lingkungan yang harmonis, transparan, dan berdaya saing melalui pelayanan publik yang prima dan inovasi digital.
            </x-slot:subtitle>
        </x-guest::ui.hero>

        {{-- VISI & MISI SECTION --}}
        <section class="mb-24 scroll-mt-24" id="visi-misi">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <x-guest::ui.card variant="bordered" padding="lg" class="border-l-4 border-primary">
                        <x-slot:icon>visibility</x-slot:icon>
                        <x-slot:title>Visi</x-slot:title>

                        <p class="text-xl italic text-slate-700 leading-relaxed">
                            "{{ $kelurahan?->visi ?? 'Menjadi Kelurahan yang unggul dalam pelayanan, mandiri dalam ekonomi, dan religius dalam tatanan kehidupan bermasyarakat.' }}"
                        </p>
                    </x-guest::ui.card>

                    <x-guest::ui.card variant="bordered" padding="lg">
                        <x-slot:icon>assignment_turned_in</x-slot:icon>
                        <x-slot:title>Misi</x-slot:title>

                        <ul class="space-y-4 text-slate-600">
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <x-guest::ui.icon name="check" size="sm" color="text-primary" />
                                </span>
                                <span>Meningkatkan kualitas pelayanan publik yang cepat, mudah, dan transparan</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <x-guest::ui.icon name="check" size="sm" color="text-primary" />
                                </span>
                                <span>Memberdayakan ekonomi lokal melalui UMKM dan koperasi</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <x-guest::ui.icon name="check" size="sm" color="text-primary" />
                                </span>
                                <span>Mewujudkan lingkungan yang bersih, sehat, dan berkelanjutan</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <x-guest::ui.icon name="check" size="sm" color="text-primary" />
                                </span>
                                <span>Membangun kerukunan dan toleransi antar umat beragama</span>
                            </li>
                        </ul>
                    </x-guest::ui.card>
                </div>

                <div class="bg-white p-8 rounded-xl shadow-sm">
                    <div class="aspect-square bg-primary/5 rounded-lg flex items-center justify-center">
                        <x-guest::ui.icon name="account_balance" size="xl" color="text-primary" />
                    </div>
                </div>
            </div>
        </section>

        {{-- STATISTIK SECTION --}}
        <section class="mb-24">
            <x-guest::ui.section-header
                title="Data Statistik"
                subtitle="Informasi terkini tentang Kelurahan"
                size="lg"
            />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <x-guest::ui.stat-card
                    title="Total Penduduk"
                    :value="number_format($totalPenduduk)"
                    :counter-value="$totalPenduduk"
                    icon="people"
                    description="Jiwa"
                    color="primary"
                />

                <x-guest::ui.stat-card
                    title="Jumlah KK"
                    :value="number_format($totalKK)"
                    :counter-value="$totalKK"
                    icon="home"
                    description="Kepala Keluarga"
                    color="success"
                />

                <x-guest::ui.stat-card
                    title="UMKM Aktif"
                    :value="number_format($totalUmkm)"
                    :counter-value="$totalUmkm"
                    icon="store"
                    description="Unit usaha"
                    color="warning"
                />

                <x-guest::ui.stat-card
                    title="Jumlah RW"
                    :value="(string) $totalRw"
                    :counter-value="$totalRw"
                    icon="landscape"
                    description="Rukun Warga"
                    color="info"
                />
            </div>
        </section>

        {{-- STRUKTUR ORGANISASI --}}
        <section class="mb-24">
            <x-guest::ui.section-header
                title="Struktur Organisasi"
                subtitle="Pejabat dan staff Kelurahan"
                size="lg"
            />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($pegawai as $index => $staff)
                    <x-guest::ui.card variant="bordered" class="{{ $index === 0 ? 'md:col-span-3' : '' }}">
                        <div class="{{ $index === 0 ? 'flex flex-col md:flex-row items-center gap-6' : 'text-center' }}">
                            <div class="{{ $index === 0 ? 'w-24 h-24' : 'w-20 h-20 mx-auto mb-4' }} rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <x-guest::ui.icon name="person" :size="$index === 0 ? 'xl' : 'lg'" color="text-primary" />
                            </div>
                            <div class="{{ $index === 0 ? 'text-center md:text-left flex-1' : '' }}">
                                <x-guest::ui.badge variant="primary" class="mb-2">{{ $staff->jabatan }}</x-guest::ui.badge>
                                <h3 class="{{ $index === 0 ? 'text-2xl' : 'text-lg' }} font-bold mb-1">{{ $staff->nama }}</h3>
                                @if ($staff->nip)
                                    <p class="text-sm text-slate-600">NIP. {{ $staff->nip }}</p>
                                @endif
                            </div>
                        </div>
                    </x-guest::ui.card>
                @empty
                    <x-guest::ui.card variant="bordered" class="md:col-span-3">
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <div class="w-24 h-24 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <x-guest::ui.icon name="person" size="xl" color="text-primary" />
                            </div>
                            <div class="text-center md:text-left flex-1">
                                <x-guest::ui.badge variant="primary" class="mb-2">Lurah</x-guest::ui.badge>
                                <h3 class="text-2xl font-bold mb-1">{{ $kelurahan?->nama_lurah ?? 'Kepala Kelurahan' }}</h3>
                                @if ($kelurahan?->nip_lurah)
                                    <p class="text-slate-600">NIP. {{ $kelurahan->nip_lurah }}</p>
                                @endif
                            </div>
                        </div>
                    </x-guest::ui.card>
                @endforelse
            </div>
        </section>

        {{-- LAYANAN SECTION --}}
        <section class="mb-24">
            <x-guest::ui.section-header
                title="Layanan Kelurahan"
                subtitle="Berbagai layanan yang kami sediakan"
                size="lg"
            />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <x-guest::ui.card variant="bordered">
                    <x-slot:icon>description</x-slot:icon>
                    <x-slot:title>Surat Menyurat</x-slot:title>

                    <p class="text-slate-600 mb-4">
                        Pengurusan berbagai jenis surat keterangan secara online maupun offline.
                    </p>

                    <x-slot:footer>
                        <x-guest::ui.button variant="outline" size="sm" class="w-full">
                            Lihat Detail
                        </x-guest::ui.button>
                    </x-slot:footer>
                </x-guest::ui.card>

                <x-guest::ui.card variant="bordered">
                    <x-slot:icon>person_search</x-slot:icon>
                    <x-slot:title>Data Kependudukan</x-slot:title>

                    <p class="text-slate-600 mb-4">
                        Cek dan update data kependudukan serta informasi administrasi lainnya.
                    </p>

                    <x-slot:footer>
                        <x-guest::ui.button variant="outline" size="sm" class="w-full">
                            Lihat Detail
                        </x-guest::ui.button>
                    </x-slot:footer>
                </x-guest::ui.card>

                <x-guest::ui.card variant="bordered">
                    <x-slot:icon>feedback</x-slot:icon>
                    <x-slot:title>Pengaduan</x-slot:title>

                    <p class="text-slate-600 mb-4">
                        Sampaikan keluhan, saran, dan aspirasi Anda kepada pemerintah kelurahan.
                    </p>

                    <x-slot:footer>
                        <x-guest::ui.button variant="outline" size="sm" class="w-full">
                            Lihat Detail
                        </x-guest::ui.button>
                    </x-slot:footer>
                </x-guest::ui.card>
            </div>
        </section>

    </main>

</x-guest::layout.app>
