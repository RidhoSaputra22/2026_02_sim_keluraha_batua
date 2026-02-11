<x-layouts.app :title="'Edit Permohonan Surat'">
    <x-slot:header>
        <x-layouts.page-header title="Edit Permohonan Surat" description="Ubah data permohonan surat #{{ $permohonan->nomor_surat }}">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('persuratan.permohonan.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </x-ui.button>
            </x-slot:actions>
        </x-layouts.page-header>
    </x-slot:header>

    @if($errors->any())
        <x-ui.alert type="error" class="mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    {{-- Status Info --}}
    <x-ui.card class="mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div>
                <span class="text-sm text-base-content/60">Status:</span>
                @php
                    $statusClass = match($permohonan->status_esign) {
                        'draft' => 'badge-warning',
                        'proses' => 'badge-info',
                        'signed' => 'badge-success',
                        'reject' => 'badge-error',
                        default => 'badge-ghost',
                    };
                    $statusLabel = match($permohonan->status_esign) {
                        'draft' => 'Draft',
                        'proses' => 'Proses Verifikasi',
                        'signed' => 'Selesai',
                        'reject' => 'Ditolak',
                        default => '-',
                    };
                @endphp
                <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>
            <div>
                <span class="text-sm text-base-content/60">Nomor:</span>
                <span class="font-mono font-medium">{{ $permohonan->nomor_surat }}</span>
            </div>
            <div>
                <span class="text-sm text-base-content/60">Input oleh:</span>
                <span>{{ $permohonan->petugasInput->name ?? '-' }}</span>
            </div>
            <div>
                <span class="text-sm text-base-content/60">Tgl Input:</span>
                <span>{{ $permohonan->tgl_input?->format('d/m/Y H:i') ?? '-' }}</span>
            </div>
        </div>
    </x-ui.card>

    <form method="POST" action="{{ route('persuratan.permohonan.update', $permohonan) }}">
        @csrf
        @method('PUT')

        {{-- Data Surat --}}
        <x-ui.card class="mb-6">
            <h3 class="font-semibold text-lg mb-4">Data Surat</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.select label="Kelurahan" name="kelurahan_id" :options="$kelurahanList->pluck('nama', 'id')->toArray()" selected="{{ old('kelurahan_id', $permohonan->kelurahan_id) }}" required />

                <x-ui.select label="Arah Surat" name="arah" :options="['keluar' => 'Surat Keluar', 'masuk' => 'Surat Masuk']" selected="{{ old('arah', $permohonan->arah) }}" required />

                <x-ui.select label="Jenis Surat" name="jenis_id" :options="$jenisList->pluck('nama', 'id')->toArray()" selected="{{ old('jenis_id', $permohonan->jenis_id) }}" required />

                <x-ui.select label="Sifat Surat" name="sifat_id" placeholder="Pilih sifat..." :options="$sifatList->pluck('nama', 'id')->toArray()" selected="{{ old('sifat_id', $permohonan->sifat_id) }}" />

                <x-ui.input label="Tanggal Surat" name="tanggal_surat" type="date" value="{{ old('tanggal_surat', $permohonan->tanggal_surat?->format('Y-m-d')) }}" />

                <x-ui.input label="Tujuan Surat" name="tujuan_surat" placeholder="Tujuan surat..." value="{{ old('tujuan_surat', $permohonan->tujuan_surat) }}" />
            </div>

            <div class="mt-4">
                <x-ui.input label="Perihal" name="perihal" placeholder="Perihal surat..." value="{{ old('perihal', $permohonan->perihal) }}" required />
            </div>

            <div class="mt-4">
                <x-ui.textarea label="Uraian / Keterangan" name="uraian" placeholder="Uraian isi surat..." rows="4">{{ old('uraian', $permohonan->uraian) }}</x-ui.textarea>
            </div>

            <div class="mt-4">
                <x-ui.input label="Nama Dalam Surat" name="nama_dalam_surat" placeholder="Nama yang tercantum dalam surat..." value="{{ old('nama_dalam_surat', $permohonan->nama_dalam_surat) }}" />
            </div>
        </x-ui.card>

        {{-- Data Pemohon --}}
        <x-ui.card class="mb-6">
            <h3 class="font-semibold text-lg mb-4">Data Pemohon</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input label="Nama Pemohon" name="pemohon_nama" placeholder="Nama lengkap pemohon..." value="{{ old('pemohon_nama', $permohonan->pemohon->nama ?? '') }}" required />

                <x-ui.input label="No. HP / WhatsApp" name="pemohon_no_hp" placeholder="08xxxxxxxxxx" value="{{ old('pemohon_no_hp', $permohonan->pemohon->no_hp_wa ?? '') }}" />

                <x-ui.input label="Email" name="pemohon_email" type="email" placeholder="email@contoh.com" value="{{ old('pemohon_email', $permohonan->pemohon->email ?? '') }}" />
            </div>
        </x-ui.card>

        {{-- Buttons --}}
        <div class="flex justify-end gap-3">
            <x-ui.button type="ghost" href="{{ route('persuratan.permohonan.index') }}">Batal</x-ui.button>
            <x-ui.button type="primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                Simpan Perubahan
            </x-ui.button>
        </div>
    </form>
</x-layouts.app>
