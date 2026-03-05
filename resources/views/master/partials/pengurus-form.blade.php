{{--
    Pengurus form fields partial.
    Variables: $penguru (RtRwPengurus|null), $pendudukList, $jabatanList, $kelurahanList, $rtRwUserList
--}}

@php
    $prefix = $penguru ? 'edit_' . $penguru->id . '_' : '';
    $currentMode = old($prefix . 'assign_user_mode', $penguru?->user_id ? 'existing' : 'none');
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    {{-- Penduduk --}}
    <x-ui.select label="Penduduk" name="penduduk_id" required
        :options="$pendudukList->mapWithKeys(fn($p) => [$p->id => $p->nik . ' - ' . $p->nama])->toArray()"
        selected="{{ old('penduduk_id', $penguru?->penduduk_id) }}" />

    {{-- Jabatan --}}
    <x-ui.select label="Jabatan" name="jabatan_id" required
        :options="$jabatanList->mapWithKeys(fn($j) => [$j->id => $j->nama])->toArray()"
        selected="{{ old('jabatan_id', $penguru?->jabatan_id) }}" />

    {{-- Status --}}
    <x-ui.select label="Status" name="status" required
        :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']"
        selected="{{ old('status', $penguru?->status ?? 'aktif') }}" />

    {{-- Tgl Mulai --}}
    <x-ui.input label="Tanggal Mulai" name="tgl_mulai" type="date"
        value="{{ old('tgl_mulai', $penguru?->tgl_mulai?->format('Y-m-d')) }}" />

    {{-- No Telp --}}
    <x-ui.input label="No. Telepon" name="no_telp" placeholder="08xxxxxxxxxx"
        value="{{ old('no_telp', $penguru?->no_telp) }}" />

    {{-- No Rekening --}}
    <x-ui.input label="No. Rekening" name="no_rekening" placeholder="Nomor rekening"
        value="{{ old('no_rekening', $penguru?->no_rekening) }}" />

    {{-- No NPWP --}}
    <x-ui.input label="No. NPWP" name="no_npwp" placeholder="Nomor NPWP"
        value="{{ old('no_npwp', $penguru?->no_npwp) }}" />

    {{-- Alamat --}}
    <div class="md:col-span-2 lg:col-span-2">
        <x-ui.textarea label="Alamat" name="alamat" placeholder="Alamat pengurus"
            value="{{ old('alamat', $penguru?->alamat) }}" />
    </div>
</div>

{{-- Akun Pengguna --}}
<div class="mt-4 pt-3 border-t border-base-300">
    <h5 class="text-sm font-semibold mb-2">Akun Pengguna (Login RT/RW)</h5>

    @if($penguru?->user)
        <div class="alert alert-info text-xs mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>Saat ini terhubung ke akun: <strong>{{ $penguru->user->email }}</strong></span>
        </div>
    @endif

    <div x-data="{ mode: '{{ $currentMode }}' }" class="space-y-3">
        <div class="flex flex-wrap gap-3">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="assign_user_mode" value="none" x-model="mode" class="radio radio-xs" />
                <span class="text-xs">Tanpa Akun</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="assign_user_mode" value="existing" x-model="mode" class="radio radio-xs radio-primary" />
                <span class="text-xs">Pilih Akun Existing</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="assign_user_mode" value="create_new" x-model="mode" class="radio radio-xs radio-success" />
                <span class="text-xs">Buat Akun Baru</span>
            </label>
        </div>

        <div x-show="mode === 'existing'" x-cloak>
            @if ($rtRwUserList->isEmpty())
                <div class="alert alert-warning text-xs">
                    <span>Semua user RT/RW sudah di-assign. Buat akun baru atau hapus assign yang ada terlebih dahulu.</span>
                </div>
            @else
                <x-ui.select label="Pilih User RT/RW" name="user_id"
                    :options="$rtRwUserList->mapWithKeys(fn($u) => [$u->id => $u->name . ' (' . $u->email . ')'])->toArray()"
                    selected="{{ old('user_id', $penguru?->user_id) }}" />
                <p class="text-xs text-base-content/60 mt-1">Wilayah RT/RW pada akun yang dipilih akan diperbarui.</p>
            @endif
        </div>

        <div x-show="mode === 'create_new'" x-cloak>
            <x-ui.input label="Email Akun Baru" name="new_user_email" type="email"
                placeholder="email@example.com"
                value="{{ old('new_user_email') }}" />
            <p class="text-xs text-base-content/60 mt-1">Nama dari data penduduk. Password sementara ditampilkan setelah disimpan.</p>
        </div>
    </div>
</div>
