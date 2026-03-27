{{-- Modal Guide: Cara Edit Polygon RW --}}
<dialog id="rw-polygon-guide-modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box max-w-2xl" x-data="{ guideStep: 1, totalSteps: 6 }">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Panduan Edit Polygon RW</h3>
                    <p class="text-xs text-base-content/50">Cara menggambar & mengelola batas wilayah RW</p>
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
            {{-- Step 1: Pilih RW --}}
        <div x-show="guideStep === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-primary-content flex items-center justify-center text-lg font-bold">1</div>
                <div class="flex-1">
                    <h4 class="font-bold text-base mb-2">Pilih RW yang Akan Diedit</h4>
                    <p class="text-sm text-base-content/70 mb-3">
                        Gunakan dropdown <strong>"Pilih RW"</strong> di toolbar atas untuk memilih RW yang ingin digambar atau diedit batas wilayahnya.
                    </p>
                    <div class="bg-base-200/50 rounded-xl p-4">
                        <div class="flex items-center gap-3 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs font-semibold text-info">Tips</span>
                        </div>
                        <p class="text-xs text-base-content/60">
                            Polygon RW lain yang sudah tersimpan akan ditampilkan sebagai referensi (warna abu-abu) agar tidak tumpang tindih.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2: Menggambar Polygon --}}
        <div x-show="guideStep === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-primary-content flex items-center justify-center text-lg font-bold">2</div>
                <div class="flex-1">
                    <h4 class="font-bold text-base mb-2">Menggambar Polygon Baru</h4>
                    <p class="text-sm text-base-content/70 mb-3">
                        Klik ikon <strong>polygon</strong> (segi lima) pada toolbar di <strong>kiri atas peta</strong>, lalu:
                    </p>
                    <div class="space-y-2">
                        <div class="flex items-start gap-2">
                            <span class="badge badge-primary badge-sm flex-shrink-0 mt-0.5">a</span>
                            <span class="text-sm text-base-content/70">Klik pada peta untuk menandai <strong>titik pertama</strong> batas wilayah</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="badge badge-primary badge-sm flex-shrink-0 mt-0.5">b</span>
                            <span class="text-sm text-base-content/70">Lanjutkan klik untuk menambah titik-titik batas berikutnya</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="badge badge-primary badge-sm flex-shrink-0 mt-0.5">c</span>
                            <span class="text-sm text-base-content/70">Klik <strong>titik pertama</strong> lagi untuk <strong>menutup polygon</strong></span>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <span class="text-xs">Hanya <strong>satu polygon</strong> per RW. Polygon baru akan menggantikan yang lama.</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3: Edit Polygon --}}
        <div x-show="guideStep === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-primary-content flex items-center justify-center text-lg font-bold">3</div>
                <div class="flex-1">
                    <h4 class="font-bold text-base mb-2">Mengedit Polygon yang Sudah Ada</h4>
                    <p class="text-sm text-base-content/70 mb-3">
                        Jika polygon sudah ada, Anda bisa mengeditnya:
                    </p>
                    <div class="space-y-3">
                        <div class="bg-base-200/50 rounded-lg p-3 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-base-300 flex items-center justify-center flex-shrink-0">
                                <svg fill="#000000"  viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21,12a1,1,0,0,0-1,1v6a1,1,0,0,1-1,1H5a1,1,0,0,1-1-1V5A1,1,0,0,1,5,4h6a1,1,0,0,0,0-2H5A3,3,0,0,0,2,5V19a3,3,0,0,0,3,3H19a3,3,0,0,0,3-3V13A1,1,0,0,0,21,12ZM6,12.76V17a1,1,0,0,0,1,1h4.24a1,1,0,0,0,.71-.29l6.92-6.93h0L21.71,8a1,1,0,0,0,0-1.42L17.47,2.29a1,1,0,0,0-1.42,0L13.23,5.12h0L6.29,12.05A1,1,0,0,0,6,12.76ZM16.76,4.41l2.83,2.83L18.17,8.66,15.34,5.83ZM8,13.17l5.93-5.93,2.83,2.83L10.83,16H8Z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold">Edit (ikon pensil)</p>
                                <p class="text-xs text-base-content/60">Klik toolbar edit, lalu seret titik-titik polygon untuk mengubah bentuk. Klik <strong>"Save"</strong> di toolbar untuk mengonfirmasi perubahan.</p>
                            </div>
                        </div>
                        <div class="bg-base-200/50 rounded-lg p-3 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-error/10 flex items-center justify-center flex-shrink-0">
                               <svg  viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="#000000"><path fill-rule="evenodd" clip-rule="evenodd" d="M10 3h3v1h-1v9l-1 1H4l-1-1V4H2V3h3V2a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v1zM9 2H6v1h3V2zM4 13h7V4H4v9zm2-8H5v7h1V5zm1 0h1v7H7V5zm2 0h1v7H9V5z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-semibold">Delete (ikon tempat sampah)</p>
                                <p class="text-xs text-base-content/60">Klik toolbar delete, pilih polygon yang akan dihapus, lalu klik <strong>"Save"</strong> di toolbar.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 4: Warna Polygon --}}
        <div x-show="guideStep === 4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-primary-content flex items-center justify-center text-lg font-bold">4</div>
                <div class="flex-1">
                    <h4 class="font-bold text-base mb-2">Mengubah Warna Polygon</h4>
                    <p class="text-sm text-base-content/70 mb-3">
                        Setiap RW memiliki warna polygon yang berbeda agar mudah dibedakan di peta utama.
                    </p>
                    <div class="space-y-2">
                        <div class="flex items-start gap-2">
                            <span class="badge badge-primary badge-sm flex-shrink-0 mt-0.5">1</span>
                            <span class="text-sm text-base-content/70">Klik <strong>color picker</strong> di toolbar "Warna Polygon"</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="badge badge-primary badge-sm flex-shrink-0 mt-0.5">2</span>
                            <span class="text-sm text-base-content/70">Pilih warna yang diinginkan</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="badge badge-primary badge-sm flex-shrink-0 mt-0.5">3</span>
                            <span class="text-sm text-base-content/70">Klik tombol <strong>"Simpan Warna"</strong> untuk menyimpan</span>
                        </div>
                    </div>
                    <div class="bg-base-200/50 rounded-xl p-3 mt-3">
                        <div class="flex items-center gap-2">
                            <div class="flex gap-1">
                                <div class="w-5 h-5 rounded-md bg-[#6366f1] border border-base-300"></div>
                                <div class="w-5 h-5 rounded-md bg-[#22c55e] border border-base-300"></div>
                                <div class="w-5 h-5 rounded-md bg-[#f59e0b] border border-base-300"></div>
                                <div class="w-5 h-5 rounded-md bg-[#ef4444] border border-base-300"></div>
                                <div class="w-5 h-5 rounded-md bg-[#06b6d4] border border-base-300"></div>
                            </div>
                            <span class="text-xs text-base-content/50">Contoh warna yang disarankan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 5: Simpan & Hapus --}}
        <div x-show="guideStep === 5" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-primary-content flex items-center justify-center text-lg font-bold">5</div>
                <div class="flex-1">
                    <h4 class="font-bold text-base mb-2">Simpan & Hapus Polygon</h4>
                    <p class="text-sm text-base-content/70 mb-3">
                        Perhatikan <strong>badge status</strong> di toolbar untuk mengetahui kondisi polygon:
                    </p>
                    <div class="space-y-2 mb-3">
                        <div class="flex items-center gap-3">
                            <span class="badge badge-success badge-sm gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Tersimpan
                            </span>
                            <span class="text-xs text-base-content/60">Polygon sudah tersimpan, tidak ada perubahan</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="badge badge-warning badge-sm gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01" /></svg>
                                Belum disimpan
                            </span>
                            <span class="text-xs text-base-content/60">Ada perubahan, klik <strong>Simpan</strong></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="badge badge-ghost badge-sm">Belum ada polygon</span>
                            <span class="text-xs text-base-content/60">Belum ada polygon yang digambar</span>
                        </div>
                    </div>
                    <div class="divider my-2 text-xs text-base-content/30">Aksi</div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-primary/5 rounded-lg p-3 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <p class="text-xs font-semibold">Simpan</p>
                            <p class="text-[10px] text-base-content/50">Menyimpan polygon ke server</p>
                        </div>
                        <div class="bg-error/5 rounded-lg p-3 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-error mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            <p class="text-xs font-semibold">Hapus</p>
                            <p class="text-[10px] text-base-content/50">Menghapus polygon dari server</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 6: Tips Penting --}}
        <div x-show="guideStep === 6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-success text-success-content flex items-center justify-center text-lg font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-base mb-2">Tips Penting</h4>
                    <div class="space-y-2">
                        <div class="flex items-start gap-2">
                            <span class="text-success flex-shrink-0 mt-0.5">✓</span>
                            <span class="text-sm text-base-content/70">Gunakan <strong>scroll mouse</strong> untuk zoom in/out peta agar lebih presisi saat menggambar</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-success flex-shrink-0 mt-0.5">✓</span>
                            <span class="text-sm text-base-content/70">Polygon RW lainnya ditampilkan transparan sebagai <strong>referensi</strong> batas wilayah</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-success flex-shrink-0 mt-0.5">✓</span>
                            <span class="text-sm text-base-content/70">Garis putus-putus menunjukkan <strong>batas kelurahan</strong> Batua</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-success flex-shrink-0 mt-0.5">✓</span>
                            <span class="text-sm text-base-content/70">Pastikan polygon <strong>tidak saling tumpang tindih</strong> antar RW</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="text-success flex-shrink-0 mt-0.5">✓</span>
                            <span class="text-sm text-base-content/70">Jangan lupa klik <strong>"Simpan"</strong> setelah menggambar atau mengedit polygon</span>
                        </div>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Sebelumnya
                </button>

                <template x-if="guideStep < totalSteps">
                    <button class="btn btn-primary btn-sm" @click="guideStep++">
                        Selanjutnya
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </template>
                <template x-if="guideStep === totalSteps">
                    <form method="dialog">
                        <button class="btn btn-success btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
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
