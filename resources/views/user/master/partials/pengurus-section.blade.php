{{--
    Pengurus management section for RT/RW show pages.
    Variables: $parentType ('rt'|'rw'), $parent (Rt|Rw model), $pendudukList, $jabatanList, $kelurahanList, $rtRwUserList
--}}

@php
    $pengurusList = $parent->pengurus->sortBy('jabatan.nama');
    $storeRoute = $parentType === 'rt'
        ? route('master.rt.pengurus.store', $parent)
        : route('master.rw.pengurus.store', $parent);
@endphp

<x-ui.card>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">Pengurus {{ strtoupper($parentType) }}</h3>
        <button type="button"
            onclick="document.getElementById('pengurus-add-form').classList.toggle('hidden')"
            class="btn btn-primary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Pengurus
        </button>
    </div>

    {{-- Credential flash --}}
    @if(session('new_user_credential'))
        <div class="alert alert-success mb-4 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('new_user_credential') }}</span>
        </div>
    @endif

    {{-- Tambah Pengurus Form (hidden by default) --}}
    <div id="pengurus-add-form" class="hidden mb-6 border border-base-300 rounded-lg p-4 bg-base-200/30">
        <h4 class="font-semibold text-base mb-3">Tambah Pengurus Baru</h4>
        <form method="POST" action="{{ $storeRoute }}">
            @csrf
            @include('master.partials.pengurus-form', ['penguru' => null])
            <div class="flex justify-end gap-2 mt-4 border-t pt-3">
                <button type="button" onclick="document.getElementById('pengurus-add-form').classList.add('hidden')" class="btn btn-ghost btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Pengurus</button>
            </div>
        </form>
    </div>

    {{-- Daftar Pengurus --}}
    <div class="overflow-x-auto">
        <table class="table table-zebra table-sm">
            <thead>
                <tr>
                    <th>Nama / NIK</th>
                    <th>Jabatan</th>
                    <th>Status</th>
                    <th>Mulai Menjabat</th>
                    <th>No. Telp</th>
                    <th>Akun</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengurusList as $penguru)
                <tr class="hover" x-data="{ editing: false }">
                    <td>
                        <div class="font-medium">{{ $penguru->penduduk->nama ?? '-' }}</div>
                        <div class="text-xs text-base-content/60">{{ $penguru->penduduk->nik ?? '-' }}</div>
                    </td>
                    <td>{{ $penguru->jabatan->nama ?? '-' }}</td>
                    <td>
                        @if(strtolower((string) ($penguru->status ?? '')) === \App\Enums\StatusAktifEnum::AKTIF->value)
                            <x-ui.badge type="success">{{ \App\Enums\StatusAktifEnum::AKTIF->label() }}</x-ui.badge>
                        @else
                            <x-ui.badge type="ghost">{{ \App\Enums\StatusAktifEnum::NONAKTIF->label() }}</x-ui.badge>
                        @endif
                    </td>
                    <td>{{ $penguru->tgl_mulai?->format('d M Y') ?? '-' }}</td>
                    <td>{{ $penguru->no_telp ?? '-' }}</td>
                    <td>
                        @if($penguru->user)
                            <span class="text-xs text-success">{{ $penguru->user->email }}</span>
                        @else
                            <span class="text-xs text-base-content/40">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex justify-end gap-1">
                            @php
                                $editId = 'pengurus-edit-' . $penguru->id;
                            @endphp
                            <button type="button"
                                onclick="document.getElementById('{{ $editId }}').classList.toggle('hidden')"
                                class="btn btn-ghost btn-xs">Edit</button>
                            @php
                                $destroyRoute = $parentType === 'rt'
                                    ? route('master.rt.pengurus.destroy', [$parent, $penguru])
                                    : route('master.rw.pengurus.destroy', [$parent, $penguru]);
                            @endphp
                            <button type="button"
                                @click="$dispatch('confirm-delete', { action: '{{ $destroyRoute }}', message: 'Hapus pengurus {{ $penguru->penduduk->nama ?? '' }}?' })"
                                class="btn btn-error btn-outline btn-xs">Hapus</button>
                        </div>
                    </td>
                </tr>
                {{-- Inline Edit Row --}}
                <tr id="{{ $editId }}" class="hidden bg-base-200/50">
                    <td colspan="7" class="p-4">
                        @php
                            $updateRoute = $parentType === 'rt'
                                ? route('master.rt.pengurus.update', [$parent, $penguru])
                                : route('master.rw.pengurus.update', [$parent, $penguru]);
                        @endphp
                        <form method="POST" action="{{ $updateRoute }}">
                            @csrf
                            @method('PUT')
                            <h4 class="font-semibold text-sm mb-3">Edit Pengurus: {{ $penguru->penduduk->nama ?? '' }}</h4>
                            @include('master.partials.pengurus-form', ['penguru' => $penguru])
                            <div class="flex justify-end gap-2 mt-4 border-t pt-3">
                                <button type="button" onclick="document.getElementById('{{ $editId }}').classList.add('hidden')" class="btn btn-ghost btn-xs">Batal</button>
                                <button type="submit" class="btn btn-primary btn-xs">Perbarui</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-base-content/60">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        Belum ada data pengurus. Klik "Tambah Pengurus" untuk menambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-ui.card>
