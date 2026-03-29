<x-guest::layout.app :title="'Administrasi Kependudukan - ' . $layananSurat->nama">
    @php
        $requiredCount = $layananSurat->persyaratans->where('is_required', true)->count();
        $optionalCount = $layananSurat->persyaratans->where('is_required', false)->count();
    @endphp

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pb-20">
        <div class="mb-8">
            <x-guest::ui.button href="{{ route('guest.administrasi') }}" variant="ghost" class="p-0">
                <span class="inline-flex items-center gap-2">
                    <x-guest::ui.icon name="arrow_back" size="sm" />
                    Kembali ke Daftar Layanan
                </span>
            </x-guest::ui.button>
        </div>

        <div class="space-y-8 px-6">
            <div class="">
                <p class="text-sm font-semibold uppercase tracking-[0.35em] text-primary/80">
                    Administrasi Kependudukan
                </p>
                <h1 class="mt-4 text-4xl font-extrabold text-slate-900 md:text-5xl">
                    {{ $layananSurat->nama }}
                </h1>
                <div class=" mt-5 h-1 w-24 rounded-full bg-primary"></div>
                <p class="mt-6 text-lg leading-8 text-slate-500">
                    Syarat yang anda harus penuhi :
                </p>
            </div>

            <div class="space-y-10">
                @forelse ($layananSurat->persyaratans as $syarat)
                    <div class="">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-md bg-primary/10 text-primary">
                                <x-guest::ui.icon name="assignment" size="sm" color="text-primary" />
                            </div>


                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                    <p class="text-lg font-semibold text-slate-900">{{ $syarat->nama }}</p>
                                    <x-guest::ui.badge :variant="$syarat->is_required ? 'primary' : 'default'" size="sm">
                                        {{ $syarat->is_required ? 'Wajib' : 'Tambahan' }}
                                    </x-guest::ui.badge>
                                </div>

                                @if ($syarat->keterangan)
                                    <p class="mt-2 text-sm leading-6 text-slate-500">
                                        {{ $syarat->keterangan }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada persyaratan yang dipublikasikan.</p>
                @endforelse
            </div>


        </div>

    </main>
</x-guest::layout.app>
