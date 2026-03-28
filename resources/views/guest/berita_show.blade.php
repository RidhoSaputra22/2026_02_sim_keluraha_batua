@php
    $shareUrl = route('guest.berita.show', $berita);
    $shareSummary = \Illuminate\Support\Str::limit(
        trim(preg_replace('/\s+/', ' ', strip_tags($berita->ringkasan ?: $berita->isi))),
        140,
    );
    $shareMessage = trim($berita->judul . ($shareSummary !== '' ? ' - ' . $shareSummary : ''));
    $whatsappShareUrl = 'https://wa.me/?text=' . rawurlencode($shareMessage . "\n" . $shareUrl);
    $facebookShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl);
@endphp

<x-guest::layout.app :title="$berita->judul">
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16">
        <a href="{{ route('guest.publikasi') }}"
            class="inline-flex items-center gap-2 font-semibold text-primary hover:bg-primary/5 rounded-md px-3 py-2 transition-colors">
            <x-guest::ui.icon name="arrow_back" size="sm" />
            Kembali ke Publikasi
        </a>

        <div class="mt-8">
            <x-guest::ui.badge variant="primary" class="mb-4">
                {{ \App\Models\Berita::kategoriOptions()[$berita->kategori] ?? ucfirst($berita->kategori) }}
            </x-guest::ui.badge>
            <h1 class="text-4xl font-extrabold leading-tight text-slate-900 md:text-5xl">{{ $berita->judul }}</h1>
            <div class="mt-4 text-slate-500">
                Dipublikasikan {{ $berita->published_at?->translatedFormat('d F Y H:i') }}
            </div>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-12">
            <article class=" lg:col-span-8">
                <div class="overflow-hidden rounded-md bg-slate-100 ">
                    @if ($berita->gambar)
                        <img src="{{ asset('storage/' . $berita->gambar) }}" alt="{{ $berita->judul }}"
                            class="w-full max-h-[520px] object-cover">
                    @else
                        <div class="flex h-[320px] items-center justify-center bg-primary/5">
                            <x-guest::ui.icon name="article" size="2xl" color="text-primary" />
                        </div>
                    @endif
                </div>
                <div x-data="{
                    copied: false,
                    copyTimer: null,
                    shareUrl: {{ \Illuminate\Support\Js::from($shareUrl) }},
                    shareTitle: {{ \Illuminate\Support\Js::from($berita->judul) }},
                    shareText: {{ \Illuminate\Support\Js::from($shareSummary !== '' ? $shareSummary : $berita->judul) }},
                    flashCopied() {
                        this.copied = true;
                        clearTimeout(this.copyTimer);
                        this.copyTimer = setTimeout(() => {
                            this.copied = false;
                        }, 2400);
                    },
                    async copyLink() {
                        try {
                            if (navigator.clipboard?.writeText) {
                                await navigator.clipboard.writeText(this.shareUrl);
                            } else {
                                const input = document.createElement('input');
                                input.value = this.shareUrl;
                                document.body.appendChild(input);
                                input.select();
                                document.execCommand('copy');
                                document.body.removeChild(input);
                            }
                
                            this.flashCopied();
                        } catch (error) {
                            console.error(error);
                        }
                    },
                    async shareNative() {
                        if (!navigator.share) {
                            await this.copyLink();
                            return;
                        }
                
                        try {
                            await navigator.share({
                                title: this.shareTitle,
                                text: this.shareText,
                                url: this.shareUrl,
                            });
                        } catch (error) {
                            if (error?.name !== 'AbortError') {
                                console.error(error);
                            }
                        }
                    }
                }" class="my-8">
                    <div class="flex items-center  gap-7">
                        <h3 class=" text-xl font-bold text-slate-900">Bagikan:</h3>
                        <a class="" href="{{ $whatsappShareUrl }}" target="_blank" rel="noopener"
                            title="Bagikan ke WhatsApp">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
                            </svg>
                            <span class="sr-only">Bagikan ke WhatsApp</span>
                        </a>
                        <a href="{{ $facebookShareUrl }}" target="_blank" rel="noopener" title="Bagikan ke Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-8 h-8"
                                viewBox="0 0 16 16">
                                <path
                                    d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
                            </svg>
                            <span class="sr-only">Bagikan ke Facebook</span>
                        </a>
                        <button type="button" x-on:click="shareNative" title="Bagikan ke platform lain"
                            class="cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg"  fill="currentColor"
                                class="w-8 h-8" viewBox="0 0 16 16">
                                <path
                                    d="M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.5 2.5 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5" />
                            </svg>
                        </button>


                    </div>

                    <p class="mt-4 text-sm text-green-600" x-cloak x-show="copied" x-transition.opacity>
                        Tautan berita berhasil disalin.
                    </p>
                </div>


                @if ($berita->ringkasan)
                    <div class="mb-8 text-xl leading-8 text-slate-600">
                        {{ $berita->ringkasan }}
                    </div>
                @endif

                <div class="prose prose-slate max-w-none prose-headings:text-slate-900 prose-p:leading-8">
                    {!! nl2br(e($berita->isi)) !!}
                </div>
            </article>

            <aside class="space-y-6 lg:col-span-4">
                <div >
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h3 class="text-xl font-bold text-slate-900">Berita Terkait</h3>
                        <a href="{{ route('guest.publikasi') }}"
                            class="text-sm font-semibold text-primary hover:underline">
                            Lihat semua
                        </a>
                    </div>

                    <div class="space-y-4">
                        @forelse ($relatedBerita as $item)
                            <a href="{{ route('guest.berita.show', $item) }}"
                                class="block rounded-md border border-slate-100 p-4 transition-colors hover:border-primary/30 hover:bg-primary/5">
                                <div class="mb-3 flex items-center justify-between gap-3">
                                    <x-guest::ui.badge variant="primary" size="sm">
                                        {{ \App\Models\Berita::kategoriOptions()[$item->kategori] ?? ucfirst($item->kategori) }}
                                    </x-guest::ui.badge>
                                    <span class="text-xs text-slate-400">
                                        {{ $item->published_at?->translatedFormat('d M Y') }}
                                    </span>
                                </div>

                                <div class="font-semibold leading-6 text-slate-900">{{ $item->judul }}</div>

                                @if ($item->ringkasan || $item->isi)
                                    <p class="mt-2 text-sm leading-6 text-slate-500">
                                        {{ \Illuminate\Support\Str::limit(trim(strip_tags($item->ringkasan ?: $item->isi)), 110) }}
                                    </p>
                                @endif
                            </a>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada berita terkait.</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </section>
</x-guest::layout.app>
