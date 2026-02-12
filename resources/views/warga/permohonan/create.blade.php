<x-layouts.app :title="'Ajukan Permohonan Surat'">

    <x-slot:header>
        <x-layouts.page-header title="Ajukan Permohonan Surat" description="Isi formulir untuk mengajukan permohonan surat baru">
            <x-slot:actions>
                <x-ui.button type="ghost" size="sm" href="{{ route('warga.permohonan.index') }}">
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

    <form method="POST" action="{{ route('warga.permohonan.store') }}">
        @csrf

        {{-- Informasi Pemohon --}}
        <x-ui.card class="mb-6">
            <h3 class="font-semibold text-lg mb-4">Informasi Pemohon</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label"><span class="label-text font-medium">Nama</span></label>
                    <input type="text" class="input input-bordered w-full" value="{{ auth()->user()->name }}" disabled />
                </div>
                <div>
                    <label class="label"><span class="label-text font-medium">Email</span></label>
                    <input type="text" class="input input-bordered w-full" value="{{ auth()->user()->email }}" disabled />
                </div>
                <div>
                    <label class="label"><span class="label-text font-medium">NIK</span></label>
                    <input type="text" class="input input-bordered w-full" value="{{ auth()->user()->nik ?? '-' }}" disabled />
                </div>
                <x-ui.input label="No. HP / WhatsApp" name="no_hp" placeholder="08xxxxxxxxxx" value="{{ old('no_hp', auth()->user()->phone) }}" />
            </div>
        </x-ui.card>

        {{-- Detail Permohonan --}}
        <x-ui.card class="mb-6">
            <h3 class="font-semibold text-lg mb-4">Detail Permohonan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.select
                    label="Jenis Surat"
                    name="jenis_id"
                    :options="$jenisList->pluck('nama', 'id')->toArray()"
                    selected="{{ old('jenis_id') }}"
                    placeholder="Pilih jenis surat..."
                    required
                />

                <x-ui.input label="Perihal / Keperluan" name="perihal" placeholder="Contoh: Pembuatan SKTM untuk keperluan beasiswa..." value="{{ old('perihal') }}" required />
            </div>

            <div class="mt-4">
                <x-ui.textarea label="Keterangan Tambahan" name="uraian" placeholder="Jelaskan keperluan Anda secara detail (opsional)..." rows="4">{{ old('uraian') }}</x-ui.textarea>
            </div>
        </x-ui.card>

        {{-- Info Box --}}
        <x-ui.alert type="info" class="mb-6">
            <div class="text-sm">
                <p class="font-semibold mb-1">Informasi:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Permohonan akan diproses oleh operator kelurahan</li>
                    <li>Pastikan data sudah benar sebelum mengirim</li>
                    <li>Anda dapat memantau status permohonan di halaman <strong>Riwayat</strong></li>
                    <li>Siapkan berkas fisik (KTP, KK, dll.) untuk pengambilan surat</li>
                </ul>
            </div>
        </x-ui.alert>

        {{-- Buttons --}}
        <div class="flex justify-end gap-3">
            <x-ui.button type="ghost" href="{{ route('warga.permohonan.index') }}">Batal</x-ui.button>
            <x-ui.button type="primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                Kirim Permohonan
            </x-ui.button>
        </div>
    </form>

</x-layouts.app>
