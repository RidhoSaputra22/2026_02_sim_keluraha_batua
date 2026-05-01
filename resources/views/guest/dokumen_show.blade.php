<x-guest::layout.app :title="$dokumenPublik->judul">
    <section class="bg-gradient-to-b from-primary/5 via-white to-white py-12 md:py-16">
        <div class="mx-auto flex max-w-7xl flex-col gap-8 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('guest.welcome') }}" class="transition-colors hover:text-primary">Beranda</a>
                <span>/</span>
                <a href="{{ route('guest.publikasi') }}" class="transition-colors hover:text-primary">Publikasi</a>
                <span>/</span>
                <span class="font-medium text-slate-700">Viewer Dokumen</span>
            </div>

            <div class="grid gap-8 lg:grid-cols-[minmax(0,1.7fr)_360px]">
                <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-5 md:px-8">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <div class="mb-3 flex flex-wrap items-center gap-2 text-sm">
                                    <x-guest::ui.badge variant="primary" size="sm">
                                        {{ \App\Models\DokumenPublik::kategoriOptions()[$dokumenPublik->kategori] ?? ucfirst($dokumenPublik->kategori) }}
                                    </x-guest::ui.badge>
                                    <span class="text-slate-400">•</span>
                                    <span class="text-slate-500">
                                        Dipublikasikan {{ $dokumenPublik->published_at?->translatedFormat('d F Y') }}
                                    </span>
                                </div>

                                <h1 class="text-2xl font-extrabold leading-tight text-slate-900 md:text-4xl">
                                    {{ $dokumenPublik->judul }}
                                </h1>

                                @if ($dokumenPublik->deskripsi)
                                    <p class="mt-4 max-w-3xl text-sm leading-7 text-slate-500 md:text-base">
                                        {{ $dokumenPublik->deskripsi }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <x-guest::ui.button href="{{ route('guest.publikasi.download', $dokumenPublik) }}"
                                    variant="outline" size="sm" icon="download" icon-position="left">
                                    Unduh Dokumen
                                </x-guest::ui.button>
                                <x-guest::ui.button href="{{ route('guest.publikasi') }}" variant="ghost" size="sm">
                                    Kembali
                                </x-guest::ui.button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50">
                        @if ($previewMode === 'image')
                            <div class="flex min-h-[70vh] items-center justify-center p-4 md:p-8">
                                <img src="{{ route('guest.dokumen.preview', $dokumenPublik) }}"
                                    alt="{{ $dokumenPublik->judul }}"
                                    class="max-h-[78vh] w-full rounded-2xl object-contain shadow-sm">
                            </div>
                        @elseif ($previewMode === 'iframe')
                            <iframe src="{{ route('guest.dokumen.preview', $dokumenPublik) }}#toolbar=1&navpanes=0"
                                title="Viewer {{ $dokumenPublik->judul }}" class="h-[78vh] w-full bg-white"></iframe>
                        @else
                            <div class="flex min-h-[70vh] items-center justify-center p-6 md:p-10">
                                <div class="max-w-xl text-center">
                                    <div
                                        class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-primary">
                                        <x-guest::ui.icon name="description" size="xl" color="text-primary" />
                                    </div>
                                    <h2 class="text-2xl font-bold text-slate-900">Preview belum tersedia</h2>
                                    <p class="mt-3 text-sm leading-7 text-slate-500 md:text-base">
                                        Format file ini belum dapat ditampilkan langsung di browser. Silakan unduh
                                        dokumen untuk membukanya di aplikasi yang sesuai.
                                    </p>
                                    <div class="mt-6">
                                        <x-guest::ui.button href="{{ route('guest.publikasi.download', $dokumenPublik) }}"
                                            icon="download" icon-position="left">
                                            Unduh Sekarang
                                        </x-guest::ui.button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="space-y-6">
                    <x-guest::ui.card variant="bordered" padding="lg" class="rounded-[2rem]">
                        @if ($dokumenPublik->cover_image)
                            <div class="mb-5 overflow-hidden rounded-2xl border border-slate-100 bg-slate-50">
                                <img src="{{ asset('storage/' . $dokumenPublik->cover_image) }}"
                                    alt="Cover {{ $dokumenPublik->judul }}" class="h-52 w-full object-cover">
                            </div>
                        @endif

                        <h2 class="text-lg font-bold text-slate-900">Informasi Dokumen</h2>
                        <dl class="mt-5 space-y-4 text-sm">
                            <div class="flex items-start justify-between gap-4">
                                <dt class="text-slate-500">Kategori</dt>
                                <dd class="text-right font-semibold text-slate-900">
                                    {{ \App\Models\DokumenPublik::kategoriOptions()[$dokumenPublik->kategori] ?? ucfirst($dokumenPublik->kategori) }}
                                </dd>
                            </div>
                            <div class="flex items-start justify-between gap-4">
                                <dt class="text-slate-500">Format</dt>
                                <dd class="text-right font-semibold uppercase text-slate-900">{{ $fileExtension }}</dd>
                            </div>
                            @if ($fileSizeLabel)
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="text-slate-500">Ukuran</dt>
                                    <dd class="text-right font-semibold text-slate-900">{{ $fileSizeLabel }}</dd>
                                </div>
                            @endif
                            @if ($dokumenPublik->mime_type)
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="text-slate-500">Tipe File</dt>
                                    <dd class="text-right font-semibold text-slate-900">{{ $dokumenPublik->mime_type }}</dd>
                                </div>
                            @endif
                            <div class="flex items-start justify-between gap-4">
                                <dt class="text-slate-500">Tanggal Terbit</dt>
                                <dd class="text-right font-semibold text-slate-900">
                                    {{ $dokumenPublik->published_at?->translatedFormat('d M Y, H:i') }}
                                </dd>
                            </div>
                        </dl>
                    </x-guest::ui.card>

                    <x-guest::ui.card variant="bordered" padding="lg" class="rounded-[2rem]">
                        <h2 class="text-lg font-bold text-slate-900">Aksi Cepat</h2>
                        <div class="mt-5 space-y-3">
                            <x-guest::ui.button href="{{ route('guest.publikasi.download', $dokumenPublik) }}"
                                class="w-full" icon="download" icon-position="left">
                                Unduh Dokumen
                            </x-guest::ui.button>

                            @if ($previewMode !== 'unsupported')
                                <x-guest::ui.button href="{{ route('guest.dokumen.preview', $dokumenPublik) }}"
                                    class="w-full" variant="ghost" size="sm" target="_blank" rel="noreferrer">
                                    Buka File Penuh
                                </x-guest::ui.button>
                            @endif
                        </div>
                    </x-guest::ui.card>

                    @if ($relatedDokumen->isNotEmpty())
                        <x-guest::ui.card variant="bordered" padding="lg" class="rounded-[2rem]">
                            <h2 class="text-lg font-bold text-slate-900">Dokumen Lainnya</h2>
                            <div class="mt-5 space-y-4">
                                @foreach ($relatedDokumen as $item)
                                    <a href="{{ route('guest.dokumen.show', $item) }}"
                                        class="block rounded-2xl border border-slate-100 p-4 transition-colors hover:border-primary/20 hover:bg-primary/5">
                                        <p class="font-semibold text-slate-900">{{ $item->judul }}</p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ \App\Models\DokumenPublik::kategoriOptions()[$item->kategori] ?? ucfirst($item->kategori) }}
                                            •
                                            {{ $item->published_at?->translatedFormat('d M Y') }}
                                        </p>
                                    </a>
                                @endforeach
                            </div>
                        </x-guest::ui.card>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-guest::layout.app>
