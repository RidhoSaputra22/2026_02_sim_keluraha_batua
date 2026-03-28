<x-guest::layout.app :title="$berita->judul">
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16">
        <a href="{{ route('guest.publikasi') }}" class="inline-flex items-center gap-2 text-primary font-semibold hover:underline">
            <x-guest::ui.icon name="arrow_back" size="sm" />
            Kembali ke Publikasi
        </a>

        <div class="mt-8">
            <x-guest::ui.badge variant="primary" class="mb-4">
                {{ \App\Models\Berita::kategoriOptions()[$berita->kategori] ?? ucfirst($berita->kategori) }}
            </x-guest::ui.badge>
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 leading-tight">{{ $berita->judul }}</h1>
            <div class="mt-4 text-slate-500">
                Dipublikasikan {{ $berita->published_at?->translatedFormat('d F Y H:i') }}
            </div>
        </div>


        <div class="mt-10 grid grid-cols-1 lg:grid-cols-12 gap-10">
            <article class="lg:col-span-8 space-y-14">
                <div class=" rounded-md overflow-hidden bg-slate-100">
                    @if ($berita->gambar)
                        <img src="{{ asset('storage/' . $berita->gambar) }}" alt="{{ $berita->judul }}"
                            class="w-full max-h-[520px] object-cover">
                    @else
                        <div class="h-[320px] flex items-center justify-center bg-primary/5">
                            <x-guest::ui.icon name="article" size="2xl" color="text-primary" />
                        </div>
                    @endif
                </div>
                @if ($berita->ringkasan)
                    <div class="text-xl text-slate-600 leading-8 mb-8">
                        {{ $berita->ringkasan }}
                    </div>
                @endif

                <div class="prose prose-slate max-w-none prose-p:leading-8 prose-headings:text-slate-900">
                    {!! nl2br(e($berita->isi)) !!}
                </div>
            </article>

            <aside class="lg:col-span-4 space-y-6">
                <x-guest::ui.card variant="bordered" padding="lg">
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Bagikan Informasi</h3>
                    <p class="text-sm text-slate-500 leading-6">
                        Anda dapat membagikan artikel ini kepada warga lain agar informasi penting kelurahan lebih cepat tersebar.
                    </p>
                </x-guest::ui.card>

                <x-guest::ui.card variant="bordered" padding="lg">
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Berita Terkait</h3>
                    <div class="space-y-4">
                        @forelse ($relatedBerita as $item)
                            <a href="{{ route('guest.berita.show', $item) }}" class="block rounded-md border border-slate-100 p-4 hover:border-primary/30 hover:bg-primary/5 transition-colors">
                                <div class="text-xs text-slate-400 mb-2">{{ $item->published_at?->translatedFormat('d M Y') }}</div>
                                <div class="font-semibold text-slate-900">{{ $item->judul }}</div>
                            </a>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada berita terkait.</p>
                        @endforelse
                    </div>
                </x-guest::ui.card>
            </aside>
        </div>
    </section>
</x-guest::layout.app>
