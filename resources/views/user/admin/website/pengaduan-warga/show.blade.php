<x-layouts.app title="Detail Pengaduan Warga">
    <x-layouts.page-header title="Detail Pengaduan Warga" description="Tinjau isi laporan dan perbarui status tindak lanjut">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.website.pengaduan-warga.index') }}" type="ghost">Kembali</x-ui.button>
        </x-slot:actions>
    </x-layouts.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card title="Informasi Pelapor">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-base-content/60">Kode Pengaduan</div>
                        <div class="font-mono">{{ $pengaduanWarga->kode_pengaduan }}</div>
                    </div>
                    <div>
                        <div class="text-base-content/60">Tanggal</div>
                        <div>{{ $pengaduanWarga->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-base-content/60">Nama</div>
                        <div>{{ $pengaduanWarga->nama }}</div>
                    </div>
                    <div>
                        <div class="text-base-content/60">No. HP</div>
                        <div>{{ $pengaduanWarga->no_hp }}</div>
                    </div>
                    <div>
                        <div class="text-base-content/60">Email</div>
                        <div>{{ $pengaduanWarga->email ?: '-' }}</div>
                    </div>
                    <div>
                        <div class="text-base-content/60">Kategori</div>
                        <div>{{ \App\Models\PengaduanWarga::kategoriOptions()[$pengaduanWarga->kategori] ?? ucfirst($pengaduanWarga->kategori) }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-base-content/60">Lokasi</div>
                        <div>{{ $pengaduanWarga->lokasi ?: '-' }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-base-content/60">Subjek</div>
                        <div class="font-semibold">{{ $pengaduanWarga->subjek }}</div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Isi Laporan">
                <div class="whitespace-pre-line text-sm leading-7">{{ $pengaduanWarga->isi_laporan }}</div>

                @if ($pengaduanWarga->lampiran)
                    <div class="mt-6">
                        <a href="{{ asset('storage/' . $pengaduanWarga->lampiran) }}" target="_blank" rel="noreferrer"
                            class="btn btn-outline btn-sm">
                            Lihat Lampiran
                        </a>
                    </div>
                @endif
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card title="Status Tindak Lanjut">
                <form method="POST" action="{{ route('admin.website.pengaduan-warga.update', $pengaduanWarga) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <x-ui.select name="status" label="Status" :options="\App\Models\PengaduanWarga::statusOptions()"
                        selected="{{ old('status', $pengaduanWarga->status) }}" required />
                    <x-ui.textarea name="catatan_admin" label="Catatan Admin" rows="8"
                        value="{{ old('catatan_admin', $pengaduanWarga->catatan_admin) }}"
                        placeholder="Catatan internal atau tindak lanjut untuk laporan ini." />

                    <x-ui.button type="primary">Simpan Perubahan</x-ui.button>
                </form>
            </x-ui.card>

            <x-ui.card title="Info Tambahan">
                <div class="text-sm space-y-3">
                    <div>
                        <div class="text-base-content/60">Ditindaklanjuti</div>
                        <div>{{ $pengaduanWarga->ditindaklanjuti_at?->format('d M Y H:i') ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-base-content/60">Status Saat Ini</div>
                        <div>{{ \App\Models\PengaduanWarga::statusOptions()[$pengaduanWarga->status] ?? ucfirst($pengaduanWarga->status) }}</div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
