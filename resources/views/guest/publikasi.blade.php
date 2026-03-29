@php
    $totalBerita = collect($kategoriBeritaCounts)->sum();
    $totalDokumen = collect($kategoriDokumenCounts)->sum();
@endphp

<x-guest::layout.app title="Publikasi">
    <x-guest::ui.hero size="xl" background="white">
        <x-slot:title>
            Pusat <span class="text-primary">Publikasi & Informasi</span>
        </x-slot:title>

        <x-slot:subtitle>
            Berita, pengumuman, dokumen publik, dan berkas unduhan dikelola langsung dari panel admin sehingga isi
            website publik lebih konsisten dan mudah diperbarui.
        </x-slot:subtitle>

        <x-slot:actions>
            <x-guest::ui.search-bar action="{{ route('guest.publikasi') }}"
                placeholder="Cari berita, pengumuman, atau dokumen publik..." button-text="Cari"
                value="{{ $search }}" />
        </x-slot:actions>
    </x-guest::ui.hero>



    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-10">
        <div class="">
            <div class=" space-y-8">
                @if ($highlightBerita)
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-bold text-slate-900">
                                Sorotan Utama
                            </h3>
                            <p class="text-slate-500 mt-2">
                                Perluas wawasan dengan berita terbaru kami
                            </p>
                        </div>
                    </div>
                    <x-guest::ui.card variant="bordered" padding="none" class="overflow-hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2">
                            <div class="aspect-square md:aspect-auto bg-slate-100">
                                @if ($highlightBerita->gambar)
                                    <img src="{{ asset('storage/' . $highlightBerita->gambar) }}"
                                        alt="{{ $highlightBerita->judul }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-primary/5">
                                        <x-guest::ui.icon name="article" size="2xl" color="text-primary" />
                                    </div>
                                @endif
                            </div>
                            <div class="p-8 flex flex-col justify-center">
                                <x-guest::ui.badge variant="primary" size="sm" class="mb-4">Sorotan
                                    Utama</x-guest::ui.badge>
                                <div class="text-sm text-slate-400 mb-2">
                                    {{ $highlightBerita->published_at?->translatedFormat('d F Y') }}</div>
                                <h2 class="text-3xl font-bold text-slate-900 leading-tight">
                                    {{ $highlightBerita->judul }}</h2>
                                <p class="mt-4 text-slate-500 leading-7">
                                    {{ $highlightBerita->ringkasan ?: \Illuminate\Support\Str::limit(strip_tags($highlightBerita->isi), 180) }}
                                </p>
                                <div class="mt-6">
                                    <x-guest::ui.button href="{{ route('guest.berita.show', $highlightBerita) }}">
                                        Baca Selengkapnya
                                    </x-guest::ui.button>
                                </div>
                            </div>
                        </div>
                    </x-guest::ui.card>
                @endif

                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-900">Berita & Pengumuman</h3>
                        <p class="text-slate-500 mt-2">Daftar konten terbaru yang sudah dipublikasikan di website
                            publik.</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('guest.publikasi') }}"
                        class="px-4 py-2 rounded-full border {{ !request('kategori') ? 'bg-primary text-white border-primary' : 'border-slate-200 text-slate-600' }}">
                        Semua Berita
                    </a>
                    @foreach (\App\Models\Berita::kategoriOptions() as $value => $label)
                        <a href="{{ route('guest.publikasi', array_filter(['kategori' => $value, 'q' => request('q'), 'dokumen_kategori' => request('dokumen_kategori')])) }}"
                            class="px-4 py-2 rounded-full border {{ request('kategori') === $value ? 'bg-primary text-white border-primary' : 'border-slate-200 text-slate-600' }}">
                            {{ $label }} ({{ $kategoriBeritaCounts[$value] ?? 0 }})
                        </a>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse ($berita as $item)
                        <x-guest::ui.card variant="bordered" padding="none" class="overflow-hidden h-full">
                            <div class="aspect-[16/10] bg-slate-100">
                                @if ($item->gambar)
                                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->judul }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-primary/5">
                                        <x-guest::ui.icon name="article" size="xl" color="text-primary" />
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <x-guest::ui.badge variant="primary" size="sm" class="mb-3">
                                    {{ \App\Models\Berita::kategoriOptions()[$item->kategori] ?? ucfirst($item->kategori) }}
                                </x-guest::ui.badge>
                                <div class="text-sm text-slate-400 mb-2">
                                    {{ $item->published_at?->translatedFormat('d M Y') }}</div>
                                <h3 class="text-xl font-bold text-slate-900 leading-snug">{{ $item->judul }}</h3>
                                <p class="text-sm text-slate-500 leading-6 mt-3 mb-5">
                                    {{ $item->ringkasan ?: \Illuminate\Support\Str::limit(strip_tags($item->isi), 130) }}
                                </p>
                                <x-guest::ui.button href="{{ route('guest.berita.show', $item) }}" variant="outline">
                                    Baca Selengkapnya
                                </x-guest::ui.button>
                            </div>
                        </x-guest::ui.card>
                    @empty
                        <div class="md:col-span-2">
                            <x-guest::ui.card variant="bordered" padding="lg" class="text-center">
                                <x-guest::ui.icon name="newspaper" size="xl" color="text-slate-300"
                                    class="mb-4" />
                                <p class="text-lg font-semibold text-slate-900">Belum ada berita yang sesuai.</p>
                            </x-guest::ui.card>
                        </div>
                    @endforelse
                </div>

                @if($berita->hasPages())
                     <div class="mt-6 rounded-2xl border border-slate-100 bg-white p-4">
                            {{ $berita->links('vendor.pagination.guest-light') }}
                        </div>
                @endif

                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-900">Rilis Dokumen Terbaru</h3>
                        <p class="text-slate-500 mt-2">
                            Dokumen publik terbaru yang sudah dipublikasikan di website publik.
                        </p>
                    </div>
                </div>
                <div>
                    <div class="space-y-4">
                        @forelse ($dokumenPublik as $dokumen)
                            <div class="rounded-2xl border border-slate-100 p-4">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-11 h-11 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                                        <x-guest::ui.icon name="description" size="sm" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-slate-900">{{ $dokumen->judul }}</p>
                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ \App\Models\DokumenPublik::kategoriOptions()[$dokumen->kategori] ?? ucfirst($dokumen->kategori) }}
                                            •
                                            {{ $dokumen->published_at?->translatedFormat('d M Y') }}
                                        </p>
                                        @if ($dokumen->deskripsi)
                                            <p class="text-sm text-slate-500 mt-2">
                                                {{ \Illuminate\Support\Str::limit($dokumen->deskripsi, 90) }}
                                            </p>
                                        @endif
                                         <div class="mt-4">
                                    <x-guest::ui.button href="{{ route('guest.publikasi.download', $dokumen) }}"
                                        variant="outline" class="w-full" size="sm">
                                        Unduh Dokumen
                                    </x-guest::ui.button>
                                </div>
                                    </div>
                                </div>

                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada dokumen publik yang sesuai filter.</p>
                        @endforelse
                    </div>

                    @if ($dokumenPublik->hasPages())
                        <div class="mt-6 rounded-2xl border border-slate-100 bg-white p-4">
                            {{ $dokumenPublik->links('vendor.pagination.guest-light') }}
                        </div>
                    @endif
                </div>

                <div>{{ $berita->links('vendor.pagination.guest-light') }}</div>
            </div>


        </div>
    </section>
</x-guest::layout.app>
