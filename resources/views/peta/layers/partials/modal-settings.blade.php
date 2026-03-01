{{-- Modal for creating/editing layer settings --}}
<dialog id="layer-settings-modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box max-w-lg">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg" x-text="editingLayer ? 'Pengaturan Layer' : 'Buat Layer Baru'"></h3>
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost">✕</button>
            </form>
        </div>

        <div class="space-y-4">
            {{-- Nama --}}
            <div class="form-control w-full">
                <label class="label"><span class="label-text">Nama Layer <span class="text-error">*</span></span></label>
                <input type="text" class="input input-bordered w-full" x-model="modalForm.nama"
                    placeholder="Contoh: Daerah Rawan Banjir">
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Warna --}}
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">Warna <span class="text-error">*</span></span></label>
                    <div class="flex items-center gap-2">
                        <input type="color" x-model="modalForm.warna" class="w-10 h-9 rounded cursor-pointer border border-base-300">
                        <input type="text" class="input input-bordered input-sm w-24 font-mono" x-model="modalForm.warna" readonly>
                    </div>
                </div>

                {{-- Pattern Type --}}
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">Pola Arsir <span class="text-error">*</span></span></label>
                    <select class="select select-bordered w-full" x-model="modalForm.pattern_type">
                        @foreach(\App\Models\PetaLayer::patternTypes() as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Fill Opacity --}}
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">Opacity (0-1)</span></label>
                    <input type="range" min="0" max="1" step="0.05" class="range range-primary range-sm"
                        x-model="modalForm.fill_opacity">
                    <div class="flex justify-between text-[10px] text-base-content/40 mt-0.5">
                        <span>Transparan</span>
                        <span x-text="modalForm.fill_opacity"></span>
                        <span>Solid</span>
                    </div>
                </div>

                {{-- Stroke Width --}}
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">Tebal Garis (px)</span></label>
                    <input type="number" class="input input-bordered input-sm w-full"
                        x-model="modalForm.stroke_width" min="0.5" max="10" step="0.5">
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="form-control w-full">
                <label class="label"><span class="label-text">Deskripsi</span></label>
                <textarea class="textarea textarea-bordered w-full" rows="2" x-model="modalForm.deskripsi"
                    placeholder="Deskripsi singkat..."></textarea>
            </div>

            {{-- Active toggle --}}
            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-3">
                    <input type="checkbox" class="toggle toggle-primary toggle-sm" x-model="modalForm.is_active">
                    <span class="label-text text-sm">Layer aktif (ditampilkan di peta utama)</span>
                </label>
            </div>

            {{-- Preview --}}
            <div class="p-3 border border-base-200 rounded-xl bg-base-200/30">
                <p class="text-[10px] font-semibold text-base-content/40 uppercase tracking-wider mb-2">Preview</p>
                <div class="flex items-center gap-3">
                    <div class="w-16 h-10 rounded-lg border border-base-300 shadow-inner"
                        :style="'background-color:' + modalForm.warna + '; opacity:' + (parseFloat(modalForm.fill_opacity) + 0.3)">
                    </div>
                    <div class="text-xs text-base-content/50">
                        <span x-text="modalForm.nama || '(Nama layer)'"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-action">
            <form method="dialog">
                <button class="btn btn-ghost btn-sm">Batal</button>
            </form>
            <button class="btn btn-primary btn-sm" @click="saveLayerSettings()" :disabled="!modalForm.nama">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span x-text="editingLayer ? 'Simpan' : 'Buat Layer'"></span>
            </button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>
