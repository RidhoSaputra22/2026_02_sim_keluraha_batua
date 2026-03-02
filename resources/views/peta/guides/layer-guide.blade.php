{{-- Modal Guide: Cara Kelola Layer Peta --}}
<dialog id="layer-guide-modal" class="modal modal-bottom sm:modal-middle ">
    <div class="modal-box max-w-2xl " x-data="{ guideStep: 1, totalSteps: 7 }">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Panduan Kelola Layer</h3>
                    <p class="text-xs text-base-content/50">Cara membuat & mengelola layer custom di peta</p>
                </div>
            </div>
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost">✕</button>
            </form>
        </div>

        {{-- Progress bar --}}
        <div class="flex items-center gap-2 mb-6">
            <template x-for="step in totalSteps" :key="step">
                <div class="flex-1 h-1.5 rounded-full transition-all duration-300"
                    :class="step <= guideStep ? 'bg-primary' : 'bg-base-200'"></div>
            </template>
            <span class="text-[10px] text-base-content/40 font-mono ml-1" x-text="guideStep + '/' + totalSteps"></span>
        </div>

        <div class="min-h-[250px]">
            {{-- Step 1: Apa itu Layer --}}
            <div x-show="guideStep === 1" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-primary-content flex items-center justify-center text-lg font-bold">
                        1</div>
                    <div class="flex-1">
                        <h4 class="font-bold text-base mb-2">Apa itu Layer?</h4>
                        <p class="text-sm text-base-content/70 mb-3">
                            Layer adalah <strong>lapisan area/zona</strong> yang bisa ditambahkan di atas peta dasar.
                            Setiap layer berisi satu atau lebih polygon yang menandai wilayah tertentu.
                        </p>
                        <div class="bg-base-200/50 rounded-xl p-4">
                            <p class="text-xs font-semibold text-base-content/60 mb-2">Contoh penggunaan layer:</p>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-sm bg-red-400/60 border border-red-500"></div>
                                    <span class="text-xs text-base-content/60">Daerah Rawan Banjir</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-sm bg-green-400/60 border border-green-500"></div>
                                    <span class="text-xs text-base-content/60">Ruang Terbuka Hijau</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-sm bg-yellow-400/60 border border-yellow-500"></div>
                                    <span class="text-xs text-base-content/60">Zona Perdagangan</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-sm bg-blue-400/60 border border-blue-500"></div>
                                    <span class="text-xs text-base-content/60">Fasilitas Umum</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Buat Layer Baru --}}
            <div x-show="guideStep === 2" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-primary-content flex items-center justify-center text-lg font-bold">
                        2</div>
                    <div class="flex-1">
                        <h4 class="font-bold text-base mb-2">Membuat Layer Baru</h4>
                        <p class="text-sm text-base-content/70 mb-3">
                            Klik tombol <strong>"Tambah"</strong> di header sidebar kanan untuk membuka form pembuatan
                            layer.
                        </p>
                        <div class="space-y-2 mb-3">
                            <div class="flex items-start gap-2">
                                <span class="badge badge-primary badge-sm flex-shrink-0 mt-0.5">1</span>
                                <span class="text-sm text-base-content/70">Isi <strong>nama layer</strong>
                                    (wajib)</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="badge badge-primary badge-sm flex-shrink-0 mt-0.5">2</span>
                                <span class="text-sm text-base-content/70">Pilih <strong>warna</strong> untuk
                                    layer</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="badge badge-primary badge-sm flex-shrink-0 mt-0.5">3</span>
                                <span class="text-sm text-base-content/70">Atur <strong>opacity</strong> (transparansi)
                                    & <strong>tebal garis</strong></span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="badge badge-primary badge-sm flex-shrink-0 mt-0.5">4</span>
                                <span class="text-sm text-base-content/70">Pilih <strong>pola arsir</strong> jika
                                    diperlukan</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="badge badge-primary badge-sm flex-shrink-0 mt-0.5">5</span>
                                <span class="text-sm text-base-content/70">Klik <strong>"Buat Layer"</strong> untuk
                                    menyimpan</span>
                            </div>
                        </div>
                        <div class="alert alert-info py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs">Layer baru otomatis terpilih dan siap untuk digambar polygon.</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3: Pilih & Gambar Polygon --}}
            <div x-show="guideStep === 3" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-primary-content flex items-center justify-center text-lg font-bold">
                        3</div>
                    <div class="flex-1">
                        <h4 class="font-bold text-base mb-2">Pilih Layer & Gambar Polygon</h4>
                        <p class="text-sm text-base-content/70 mb-3">
                            Klik <strong>nama layer</strong> di sidebar untuk memilihnya (layer aktif ditandai garis
                            biru di kiri).
                        </p>
                        <div class="space-y-3">
                            <div class="bg-base-200/50 rounded-lg p-3">
                                <p class="text-xs font-semibold mb-2">Toolbar Gambar (kiri atas peta)</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded bg-base-300 flex items-center justify-center">
                                            <svg fill="#000000"   viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24"><path d="M21.9,11.5l-4.5-7.8c-0.2-0.3-0.5-0.5-0.9-0.5h-9c-0.4,0-0.7,0.2-0.9,0.5l-4.5,7.8c-0.2,0.3-0.2,0.7,0,1l4.5,7.8c0.2,0.3,0.5,0.5,0.9,0.5h9c0.4,0,0.7-0.2,0.9-0.5l4.5-7.8C22,12.2,22,11.8,21.9,11.5z"/></svg>
                                        </div>
                                        <span class="text-xs text-base-content/60">Polygon (segi lima)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded bg-base-300 flex items-center justify-center">
                                          <svg fill="#000000"   viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24"><path d="M21,2H3C2.4,2,2,2.4,2,3v18c0,0.6,0.4,1,1,1h18c0.6,0,1-0.4,1-1V3C22,2.4,21.6,2,21,2z M20"/></svg>
                                        </div>
                                        <span class="text-xs text-base-content/60">Rectangle (kotak)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-primary flex-shrink-0 mt-0.5">▸</span>
                                <span class="text-sm text-base-content/70">Klik titik-titik di peta untuk membuat
                                    polygon, lalu tutup di titik pertama</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-primary flex-shrink-0 mt-0.5">▸</span>
                                <span class="text-sm text-base-content/70">Polygon <strong>otomatis tersimpan</strong>
                                    setelah selesai digambar</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-primary flex-shrink-0 mt-0.5">▸</span>
                                <span class="text-sm text-base-content/70">Satu layer bisa memiliki <strong>banyak
                                        polygon</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 4: Kelola Polygon --}}
            <div x-show="guideStep === 4" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-primary-content flex items-center justify-center text-lg font-bold">
                        4</div>
                    <div class="flex-1">
                        <h4 class="font-bold text-base mb-2">Kelola Polygon dalam Layer</h4>
                        <p class="text-sm text-base-content/70 mb-3">
                            Setelah menggambar polygon, daftar polygon muncul di <strong>bagian bawah sidebar</strong>.
                        </p>
                        <div class="space-y-3">
                            <div class="bg-base-200/50 rounded-lg p-3 flex items-start gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-base-300 flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold">Ganti Nama</p>
                                    <p class="text-xs text-base-content/60">Klik field nama polygon di daftar, ketik
                                        nama baru, lalu tekan Enter</p>
                                </div>
                            </div>
                            <div class="bg-base-200/50 rounded-lg p-3 flex items-start gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-base-300 flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold">Zoom ke Polygon</p>
                                    <p class="text-xs text-base-content/60">Klik ikon kaca pembesar untuk memfokuskan
                                        peta ke polygon tertentu</p>
                                </div>
                            </div>
                            <div class="bg-base-200/50 rounded-lg p-3 flex items-start gap-3">
                                <div
                                    class="w-8 h-8 rounded-lg bg-error/10 flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-error" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold">Hapus Polygon</p>
                                    <p class="text-xs text-base-content/60">Klik ikon silang untuk menghapus polygon
                                        dari layer</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 5: Menu Layer --}}
            <div x-show="guideStep === 5" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-primary-content flex items-center justify-center text-lg font-bold">
                        5</div>
                    <div class="flex-1">
                        <h4 class="font-bold text-base mb-2">Menu Konteks Layer</h4>
                        <p class="text-sm text-base-content/70 mb-3">
                            Klik ikon <strong>tiga titik (⋮)</strong> di samping nama layer untuk akses menu berikut:
                        </p>
                        <div class="overflow-x-auto">
                            <table class="table table-xs">
                                <thead>
                                    <tr>
                                        <th class="text-xs">Menu</th>
                                        <th class="text-xs">Fungsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-semibold text-xs">Edit Polygon</td>
                                        <td class="text-xs text-base-content/60">Aktifkan layer untuk mode edit polygon
                                            di peta</td>
                                    </tr>
                                    <tr>
                                        <td class="font-semibold text-xs">Pengaturan</td>
                                        <td class="text-xs text-base-content/60">Ubah nama, warna, opacity, pola arsir
                                            layer</td>
                                    </tr>
                                    <tr>
                                        <td class="font-semibold text-xs">Aktifkan/Nonaktifkan</td>
                                        <td class="text-xs text-base-content/60">Toggle tampil/sembunyikan di peta
                                            utama kelurahan</td>
                                    </tr>
                                    <tr>
                                        <td class="font-semibold text-xs">Zoom ke Layer</td>
                                        <td class="text-xs text-base-content/60">Fokuskan peta ke area layer ini</td>
                                    </tr>
                                    <tr>
                                        <td class="font-semibold text-xs text-error">Hapus Layer</td>
                                        <td class="text-xs text-base-content/60">Hapus layer beserta semua polygon di
                                            dalamnya</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 6: Visibilitas & Urutan --}}
            <div x-show="guideStep === 6" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-primary-content flex items-center justify-center text-lg font-bold">
                        6</div>
                    <div class="flex-1">
                        <h4 class="font-bold text-base mb-2">Visibilitas & Urutan Layer</h4>
                        <div class="space-y-4">
                            {{-- Visibility --}}
                            <div>
                                <p class="text-xs font-semibold text-base-content/60 uppercase tracking-wider mb-2">
                                    Visibilitas</p>
                                <div class="bg-base-200/50 rounded-lg p-3">
                                    <div class="flex items-center gap-3 mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-base-content/70"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span class="text-sm text-base-content/70">Klik ikon <strong>mata</strong> di
                                            samping kiri nama layer untuk menampilkan/menyembunyikan layer di peta
                                            editor</span>
                                    </div>
                                    <p class="text-xs text-base-content/50 ml-8">
                                        Berbeda dengan <em>"Aktifkan/Nonaktifkan"</em> — visibilitas hanya berlaku di
                                        halaman editor ini, sedangkan aktif/nonaktif mengatur tampil di peta utama.
                                    </p>
                                </div>
                            </div>

                            {{-- Ordering --}}
                            <div>
                                <p class="text-xs font-semibold text-base-content/60 uppercase tracking-wider mb-2">
                                    Urutan Layer</p>
                                <div class="bg-base-200/50 rounded-lg p-3">
                                    <div class="flex items-center gap-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-base-content/70"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 8h16M4 16h16" />
                                        </svg>
                                        <span class="text-sm text-base-content/70">
                                            <strong>Seret</strong> (drag) ikon garis horizontal di kiri layer untuk
                                            mengubah urutan tampilan. Layer di atas akan ditampilkan <strong>di
                                                depan</strong> layer di bawahnya.
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 7: Tips --}}
            <div x-show="guideStep === 7" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-full bg-success text-success-content flex items-center justify-center text-lg font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-base mb-2">Tips & Catatan Penting</h4>
                        <div class="space-y-2">
                            <div class="flex items-start gap-2">
                                <span class="text-success flex-shrink-0 mt-0.5">✓</span>
                                <span class="text-sm text-base-content/70"><strong>Layer aktif (is_active)</strong>
                                    akan ditampilkan di peta utama kelurahan yang dilihat semua pengguna</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-success flex-shrink-0 mt-0.5">✓</span>
                                <span class="text-sm text-base-content/70">Polygon tersimpan <strong>otomatis</strong>
                                    ke server saat selesai digambar, diedit, atau dihapus</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-success flex-shrink-0 mt-0.5">✓</span>
                                <span class="text-sm text-base-content/70">Layer RW (batas wilayah tiap RW) selalu
                                    ditampilkan sebagai <strong>overlay referensi</strong></span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-success flex-shrink-0 mt-0.5">✓</span>
                                <span class="text-sm text-base-content/70">Gunakan warna berbeda untuk setiap layer
                                    agar mudah dibedakan</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="text-success flex-shrink-0 mt-0.5">✓</span>
                                <span class="text-sm text-base-content/70">Hapus layer akan menghapus <strong>semua
                                        polygon</strong> di dalamnya secara permanen</span>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3 py-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <span class="text-xs">Pastikan hanya layer yang relevan yang diaktifkan agar peta utama
                                tidak terlalu ramai.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <div class="modal-action mt-6">
            <div class="flex items-center justify-between w-full">
                <button class="btn btn-ghost btn-sm" @click="guideStep = Math.max(1, guideStep - 1)"
                    :disabled="guideStep === 1" :class="{ 'invisible': guideStep === 1 }">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Sebelumnya
                </button>

                <template x-if="guideStep < totalSteps">
                    <button class="btn btn-primary btn-sm" @click="guideStep++">
                        Selanjutnya
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </template>
                <template x-if="guideStep === totalSteps">
                    <form method="dialog">
                        <button class="btn btn-success btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Mengerti, Mulai!
                        </button>
                    </form>
                </template>
            </div>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>
