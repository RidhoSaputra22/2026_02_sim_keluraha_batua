<?php

use App\Http\Controllers\Admin\Access\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\Access\UserController as AdminUserController;
use App\Http\Controllers\Admin\Dashboard\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\Master\PegawaiController;
use App\Http\Controllers\Admin\Master\PengurusController;
use App\Http\Controllers\Admin\Master\ProfilWilayahController;
use App\Http\Controllers\Admin\Master\RtController as AdminRtController;
use App\Http\Controllers\Admin\Master\RwController as AdminRwController;
use App\Http\Controllers\Admin\Peta\PetaLayerController;
use App\Http\Controllers\Admin\Reports\LaporanKependudukanController;
use App\Http\Controllers\Admin\Reports\LaporanUsahaController as AdminLaporanUsahaController;
use App\Http\Controllers\Admin\Website\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\Website\DestinasiWisataController as AdminDestinasiWisataController;
use App\Http\Controllers\Admin\Website\DokumenPublikController as AdminDokumenPublikController;
use App\Http\Controllers\Admin\Website\LayananSuratController as AdminLayananSuratController;
use App\Http\Controllers\Admin\Website\PengaduanWargaController as AdminPengaduanWargaController;
use App\Http\Controllers\Shared\Kependudukan\KeluargaController as SharedKeluargaController;
use App\Http\Controllers\Shared\Kependudukan\PendudukController as SharedPendudukController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::patch('users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');

        Route::get('roles', [AdminRoleController::class, 'index'])->name('roles.index');

        Route::prefix('website')->name('website.')->group(function () {
            Route::resource('berita', AdminBeritaController::class)
                ->parameters(['berita' => 'berita'])
                ->except(['show']);
            Route::resource('dokumen-publik', AdminDokumenPublikController::class)
                ->parameters(['dokumen-publik' => 'dokumenPublik'])
                ->except(['show']);
            Route::resource('layanan-surat', AdminLayananSuratController::class)
                ->parameters(['layanan-surat' => 'layananSurat'])
                ->except(['show']);
            Route::resource('destinasi-wisata', AdminDestinasiWisataController::class)
                ->parameters(['destinasi-wisata' => 'destinasiWisata'])
                ->except(['show']);
            Route::resource('pengaduan-warga', AdminPengaduanWargaController::class)
                ->parameters(['pengaduan-warga' => 'pengaduanWarga'])
                ->only(['index', 'show', 'update', 'destroy']);
        });

        Route::prefix('peta-layer')->name('peta-layer.')->group(function () {
            Route::get('/', [PetaLayerController::class, 'index'])->name('index');
            Route::get('/create', [PetaLayerController::class, 'create'])->name('create');
            Route::post('/', [PetaLayerController::class, 'store'])->name('store');
            Route::get('/{petaLayer}/edit', [PetaLayerController::class, 'edit'])->name('edit');
            Route::put('/{petaLayer}', [PetaLayerController::class, 'update'])->name('update');
            Route::delete('/{petaLayer}', [PetaLayerController::class, 'destroy'])->name('destroy');
            Route::patch('/{petaLayer}/toggle-active', [PetaLayerController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/reorder', [PetaLayerController::class, 'reorder'])->name('reorder');
            Route::post('/store-json', [PetaLayerController::class, 'storeJson'])->name('store-json');
            Route::put('/{petaLayer}/update-json', [PetaLayerController::class, 'updateJson'])->name('update-json');
            Route::delete('/{petaLayer}/destroy-json', [PetaLayerController::class, 'destroyJson'])->name('destroy-json');
            Route::post('/{petaLayer}/polygon', [PetaLayerController::class, 'storePolygon'])->name('polygon.store');
            Route::put('/{petaLayer}/polygon/{polygon}', [PetaLayerController::class, 'updatePolygon'])->name('polygon.update');
            Route::delete('/{petaLayer}/polygon/{polygon}', [PetaLayerController::class, 'destroyPolygon'])->name('polygon.destroy');
            Route::post('/{petaLayer}/polygon-reorder', [PetaLayerController::class, 'reorderPolygons'])->name('polygon.reorder');
        });
    });

    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('penduduk', SharedPendudukController::class);
        Route::resource('keluarga', SharedKeluargaController::class);

        Route::resource('rw', AdminRwController::class);
        Route::delete('rw/{rw}/foto', [AdminRwController::class, 'deleteFoto'])->name('rw.delete-foto');
        Route::post('rw/{rw}/pengurus', [AdminRwController::class, 'storePengurus'])->name('rw.pengurus.store');
        Route::put('rw/{rw}/pengurus/{penguru}', [AdminRwController::class, 'updatePengurus'])->name('rw.pengurus.update');
        Route::delete('rw/{rw}/pengurus/{penguru}', [AdminRwController::class, 'destroyPengurus'])->name('rw.pengurus.destroy');

        Route::resource('rt', AdminRtController::class);
        Route::delete('rt/{rt}/foto', [AdminRtController::class, 'deleteFoto'])->name('rt.delete-foto');
        Route::post('rt/{rt}/pengurus', [AdminRtController::class, 'storePengurus'])->name('rt.pengurus.store');
        Route::put('rt/{rt}/pengurus/{penguru}', [AdminRtController::class, 'updatePengurus'])->name('rt.pengurus.update');
        Route::delete('rt/{rt}/pengurus/{penguru}', [AdminRtController::class, 'destroyPengurus'])->name('rt.pengurus.destroy');

        Route::resource('pengurus', PengurusController::class);
        Route::resource('pegawai', PegawaiController::class)->except(['show']);

        Route::prefix('profil-wilayah')->name('profil-wilayah.')->group(function () {
            Route::get('/kelurahan/{kelurahan}', [ProfilWilayahController::class, 'kelurahanShow'])->name('kelurahan.show');
            Route::get('/kelurahan/{kelurahan}/edit', [ProfilWilayahController::class, 'kelurahanEdit'])->name('kelurahan.edit');
            Route::put('/kelurahan/{kelurahan}', [ProfilWilayahController::class, 'kelurahanUpdate'])->name('kelurahan.update');
            Route::delete('/kelurahan/{kelurahan}/foto', [ProfilWilayahController::class, 'kelurahanDeleteFoto'])->name('kelurahan.delete-foto');
        });
    });

    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/kependudukan', [LaporanKependudukanController::class, 'index'])->name('kependudukan');
        Route::get('/usaha', [AdminLaporanUsahaController::class, 'index'])->name('usaha');
    });
});
