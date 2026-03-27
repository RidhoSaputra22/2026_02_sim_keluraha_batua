<x-guest::layout.app :title="$search !== '' ? 'Hasil Pencarian' : 'Global Search'">
    <x-guest::ui.hero size="lg" background="gradient">
        <x-slot:title>
            Pencarian <span class="text-primary"> Layanan Publik</span>
        </x-slot:title>

        <x-slot:subtitle>
            Cari layanan surat, berita, dokumen publik, pariwisata, UMKM, dan jalur cepat menuju halaman warga dalam
            satu tempat.
        </x-slot:subtitle>

        <x-slot:actions>
            <div class="w-full max-w-7xl">
                <x-guest::ui.search-bar action="{{ route('guest.search') }}"
                    placeholder="Cari layanan, berita, dokumen, UMKM, atau wisata..." button-text="Cari"
                    value="{{ $search }}" size="lg" />

                <div class="mt-4 flex flex-wrap items-center justify-center gap-2 text-sm">
                    <span class="text-slate-500">Pencarian populer:</span>
                    @foreach ($popularKeywords as $keyword)
                        <a href="{{ route('guest.search', ['q' => $keyword['query']]) }}"
                            class="rounded-full border border-slate-200 bg-white px-4 py-2 text-slate-600 transition-colors hover:border-primary/30 hover:text-primary">
                            {{ $keyword['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </x-slot:actions>
    </x-guest::ui.hero>

    @if ($search !== '')
        <section class="border-y border-slate-100 bg-white py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <x-guest::ui.card variant="bordered" padding="md">
                        <div class="text-sm text-slate-500">Kata Kunci</div>
                        <div class="mt-2 text-2xl font-bold text-slate-900">"{{ $search }}"</div>
                    </x-guest::ui.card>
                    <x-guest::ui.card variant="bordered" padding="md">
                        <div class="text-sm text-slate-500">Total Hasil</div>
                        <div class="mt-2 text-3xl font-bold text-slate-900">{{ $results->count() }}</div>
                    </x-guest::ui.card>
                    <x-guest::ui.card variant="bordered" padding="md">
                        <div class="text-sm text-slate-500">Kategori Tersentuh</div>
                        <div class="mt-2 text-3xl font-bold text-slate-900">{{ $summaryCards->count() }}</div>
                    </x-guest::ui.card>
                </div>

                @if ($summaryCards->isNotEmpty())
                    <div class="mt-6 flex flex-wrap gap-2">
                        @foreach ($summaryCards as $summary)
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-primary/5 px-4 py-2 text-sm font-semibold text-primary">
                                <x-guest::ui.icon :name="$summary['icon']" size="sm" color="text-primary" />
                                {{ $summary['title'] }} ({{ $summary['count'] }})
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    <section class="bg-slate-50 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <div class="space-y-8 lg:col-span-8">
                    @if ($search === '')
                        <x-guest::ui.card variant="bordered" padding="lg">
                            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <h2 class="text-2xl font-bold text-slate-900">Mulai dari kata kunci yang Anda
                                        butuhkan</h2>
                                    <p class="mt-2 text-sm leading-6 text-slate-500">
                                        Gunakan pencarian ini untuk menemukan layanan publik, dokumen, berita, UMKM,
                                        wisata lokal, atau jalur cepat ke halaman warga.
                                    </p>
                                </div>
                                <div
                                    class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                    <x-guest::ui.icon name="manage_search" size="lg" color="text-primary" />
                                </div>
                            </div>
                        </x-guest::ui.card>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            @foreach ($searchScopes as $scope)
                                <x-guest::ui.card variant="bordered" padding="lg" class="h-full">
                                    <div
                                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                        <x-guest::ui.icon :name="$scope['icon']" size="lg" color="text-primary" />
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-900">{{ $scope['title'] }}</h3>
                                    <p class="mt-3 text-sm leading-6 text-slate-500">{{ $scope['description'] }}</p>
                                </x-guest::ui.card>
                            @endforeach
                        </div>
                    @elseif ($resultGroups->isEmpty())
                        <x-guest::ui.card variant="bordered" padding="lg" class="text-center">
                            <div
                                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <x-guest::ui.icon name="search_off" size="xl" color="text-slate-400" />
                            </div>
                            <h2 class="text-2xl font-bold text-slate-900">Belum ada hasil yang cocok</h2>
                            <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-slate-500">
                                Coba gunakan kata yang lebih umum seperti nama layanan, jenis dokumen, sektor usaha,
                                atau topik berita yang ingin Anda temukan.
                            </p>
                        </x-guest::ui.card>
                    @else
                        @foreach ($resultGroups as $category => $items)
                            <x-guest::ui.card variant="bordered" padding="lg">
                                <div class="mb-6 flex items-start justify-between gap-4">
                                    <div>
                                        <h2 class="text-2xl font-bold text-slate-900">{{ $category }}</h2>
                                        <p class="mt-2 text-sm text-slate-500">{{ $items->count() }} hasil ditemukan
                                        </p>
                                    </div>
                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                        <x-guest::ui.icon :name="$items->first()['icon'] ?? 'search'" size="lg" color="text-primary" />
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    @foreach ($items as $item)
                                        <div
                                            class="rounded-2xl border border-slate-100 p-5 transition-colors hover:border-primary/20">
                                            <div class="flex items-start gap-4">
                                                <div
                                                    class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                                    <x-guest::ui.icon :name="$item['icon']" size="lg"
                                                        color="text-primary" />
                                                </div>

                                                <div class="min-w-0 flex-1">
                                                    <h3 class="text-lg font-bold text-slate-900">{{ $item['title'] }}
                                                    </h3>

                                                    @if (!empty($item['subtitle']))
                                                        <p class="mt-1 text-sm text-slate-500">{{ $item['subtitle'] }}
                                                        </p>
                                                    @endif

                                                    @if (!empty($item['description']))
                                                        <p class="mt-3 text-sm leading-6 text-slate-600">
                                                            {{ $item['description'] }}</p>
                                                    @endif

                                                    <div class="mt-4 flex flex-wrap gap-3">
                                                        <x-guest::ui.button :href="$item['url']" variant="outline"
                                                            size="sm">
                                                            {{ $item['action_label'] ?? 'Buka' }}
                                                        </x-guest::ui.button>

                                                        @if (!empty($item['secondary_url']) && !empty($item['secondary_label']))
                                                            <x-guest::ui.button :href="$item['secondary_url']" variant="ghost"
                                                                size="sm"
                                                                target="_blank"
                                                                rel="noreferrer"
                                                                >
                                                                {{ $item['secondary_label'] }}
                                                            </x-guest::ui.button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </x-guest::ui.card>
                        @endforeach
                    @endif
                </div>

                <aside class="space-y-6 lg:col-span-4">
                    <x-guest::ui.card variant="bordered" padding="lg">
                        <h3 class="text-xl font-bold text-slate-900">Jalur Cepat Warga</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Jika ingin langsung menuju halaman tertentu, gunakan pintasan berikut.
                        </p>

                        <div class="mt-6 space-y-3">
                            @foreach (collect($shortcutLinks)->take(6) as $shortcut)
                                <a href="{{ $shortcut['url'] }}"
                                    class="block rounded-2xl border border-slate-100 p-4 transition-colors hover:border-primary/20 hover:bg-primary/5">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                            <x-guest::ui.icon :name="$shortcut['icon']" size="md" color="text-primary" />
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900">{{ $shortcut['title'] }}</div>
                                            <div class="mt-1 text-sm text-slate-500">{{ $shortcut['subtitle'] }}</div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </x-guest::ui.card>

                    <x-guest::ui.card variant="bordered" padding="lg">
                        <h3 class="text-xl font-bold text-slate-900">Tips Pencarian</h3>
                        <div class="mt-5 space-y-4 text-sm text-slate-600">
                            <div class="flex items-start gap-3">
                                <x-guest::ui.icon name="lightbulb" size="sm" color="text-primary"
                                    class="mt-1" />
                                <p>Coba ketik nama layanan seperti "surat domisili", "izin usaha", atau "cek KTP".</p>
                            </div>

                            <div class="flex items-start gap-3">
                                <x-guest::ui.icon name="support_agent" size="sm" color="text-primary"
                                    class="mt-1" />
                                <p>Jika belum menemukan yang dicari, buka halaman kontak atau kirim pengaduan agar
                                    permintaan Anda tercatat.</p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <x-guest::ui.button href="{{ route('guest.kontak') }}" variant="outline" size="sm">
                                Kontak
                            </x-guest::ui.button>
                            <x-guest::ui.button href="{{ route('guest.pengaduan') }}" variant="ghost"
                                size="sm">
                                Pengaduan
                            </x-guest::ui.button>
                        </div>
                    </x-guest::ui.card>
                </aside>
            </div>
        </div>
    </section>
</x-guest::layout.app>
