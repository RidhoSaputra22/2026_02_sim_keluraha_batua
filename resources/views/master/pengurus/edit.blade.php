<x-layouts.app :title="'Edit Pengurus RT/RW'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Pengurus RT/RW" description="Ubah data pengurus {{ $penguru->jabatan->nama ?? '' }} - {{ $penguru->penduduk->nama ?? '' }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('master.pengurus.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    <x-ui.card>
        @if (session('new_user_credential'))
            <div class="alert alert-success mb-4 text-sm font-mono">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('new_user_credential') }}</span>
            </div>
        @endif
        <form method="POST" action="{{ route('master.pengurus.update', $penguru) }}">
            @csrf @method('PUT')

            {{-- Data Pengurus --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Pengurus</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.select label="Penduduk" name="penduduk_id" required :options="$pendudukList->mapWithKeys(fn($p) => [$p->id => $p->nik . ' - ' . $p->nama])->toArray()" selected="{{ old('penduduk_id', $penguru->penduduk_id) }}" />
                <x-ui.select label="Kelurahan" name="kelurahan_id" required :options="$kelurahanList->mapWithKeys(fn($k) => [$k->id => $k->nama])->toArray()" selected="{{ old('kelurahan_id', $penguru->kelurahan_id) }}" />
            </div>

            {{-- Data Jabatan --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Jabatan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                <x-ui.select label="Jabatan" name="jabatan_id" required :options="$jabatanList->mapWithKeys(fn($j) => [$j->id => $j->nama])->toArray()" selected="{{ old('jabatan_id', $penguru->jabatan_id) }}" />
                <x-ui.select label="RW" name="rw_id" :options="$rwList->mapWithKeys(fn($r) => [$r->id => 'RW ' . $r->nomor])->toArray()" selected="{{ old('rw_id', $penguru->rw_id) }}" />
                <x-ui.select label="RT" name="rt_id" :options="$rtList->mapWithKeys(fn($r) => [$r->id => 'RT ' . $r->nomor . ' / RW ' . ($r->rw->nomor ?? '-')])->toArray()" selected="{{ old('rt_id', $penguru->rt_id) }}" />
                <x-ui.input label="Tanggal Mulai" name="tgl_mulai" type="date" value="{{ old('tgl_mulai', $penguru->tgl_mulai) }}" />
                <x-ui.select label="Status" name="status" required :options="\App\Enums\StatusAktifEnum::options()" selected="{{ old('status', strtolower((string) ($penguru->status ?? \App\Enums\StatusAktifEnum::AKTIF->value))) }}" />
            </div>

            {{-- Data Tambahan --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Data Tambahan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-ui.input label="No. Telepon" name="no_telp" value="{{ old('no_telp', $penguru->no_telp) }}" />
                <x-ui.input label="No. Rekening" name="no_rekening" value="{{ old('no_rekening', $penguru->no_rekening) }}" />
                <x-ui.input label="No. NPWP" name="no_npwp" value="{{ old('no_npwp', $penguru->no_npwp) }}" />
                <div class="md:col-span-2">
                    <x-ui.textarea label="Alamat" name="alamat" value="{{ old('alamat', $penguru->alamat) }}" />
                </div>
            </div>

            {{-- Assign User RT/RW --}}
            <h3 class="text-lg font-semibold mb-4 border-b pb-2">Akun Pengguna (Login RT/RW)</h3>
            @if ($penguru->user)
                <div class="alert alert-info mb-4 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
                    <span>Akun terhubung: <strong>{{ $penguru->user->name }}</strong> &mdash; {{ $penguru->user->email }}
                        &nbsp;|&nbsp; wilayah_rw: {{ $penguru->user->wilayah_rw ?? '-' }}
                        &nbsp;|&nbsp; wilayah_rt: {{ $penguru->user->wilayah_rt ?? '-' }}
                    </span>
                </div>
            @endif
            <div x-data="{ mode: '{{ old('assign_user_mode', $penguru->user_id ? 'existing' : 'none') }}' }" class="space-y-4">
                <div class="flex flex-wrap gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="assign_user_mode" value="none" x-model="mode" class="radio radio-sm" />
                        <span class="text-sm">Tanpa Akun</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="assign_user_mode" value="existing" x-model="mode" class="radio radio-sm radio-primary" />
                        <span class="text-sm">Pilih Akun RT/RW yang Ada</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="assign_user_mode" value="create_new" x-model="mode" class="radio radio-sm radio-success" />
                        <span class="text-sm">Buat Akun Baru</span>
                    </label>
                </div>

                <div x-show="mode === 'existing'" x-cloak>
                    @if ($rtRwUserList->isEmpty())
                        <div class="alert alert-warning text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
                            <span>Semua user RT/RW sudah di-assign ke pengurus lain. Buat akun baru atau hapus assign yang ada terlebih dahulu.</span>
                        </div>
                    @else
                        <x-ui.select label="Pilih User RT/RW" name="user_id"
                            :options="$rtRwUserList->mapWithKeys(fn($u) => [$u->id => $u->name . ' (' . $u->email . ')'])->toArray()"
                            selected="{{ old('user_id', $penguru->user_id) }}" />
                        <p class="text-xs text-base-content/60 mt-1">Wilayah RT/RW pada akun yang dipilih akan diperbarui sesuai data jabatan ini.</p>
                    @endif
                </div>

                <div x-show="mode === 'create_new'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-ui.input label="Email Akun Baru" name="new_user_email" type="email"
                        placeholder="email@example.com"
                        value="{{ old('new_user_email') }}" />
                    <div class="flex items-end pb-1">
                        <p class="text-xs text-base-content/60">Nama akun akan diambil dari data penduduk. Password sementara akan ditampilkan setelah disimpan.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6 border-t pt-4">
                <x-ui.button type="ghost" href="{{ route('master.pengurus.index') }}">Batal</x-ui.button>
                <x-ui.button type="primary" :isSubmit="true">Update Data Pengurus</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.app>
