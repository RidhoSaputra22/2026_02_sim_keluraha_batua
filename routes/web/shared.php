<?php

use App\Http\Controllers\Admin\Peta\PetaLayerController;
use App\Http\Controllers\Shared\DataUmum\AsramaController;
use App\Http\Controllers\Shared\DataUmum\FaskesController;
use App\Http\Controllers\Shared\DataUmum\KendaraanController;
use App\Http\Controllers\Shared\DataUmum\KontrakanController;
use App\Http\Controllers\Shared\DataUmum\PbbController;
use App\Http\Controllers\Shared\DataUmum\PetugasKebersihanController;
use App\Http\Controllers\Shared\DataUmum\RetribusiSampahController;
use App\Http\Controllers\Shared\DataUmum\SekolahController;
use App\Http\Controllers\Shared\DataUmum\TempatIbadahController;
use App\Http\Controllers\Shared\ImportExport\ImportExportController;
use App\Http\Controllers\Shared\Kependudukan\KelahiranController;
use App\Http\Controllers\Shared\Kependudukan\KeluargaController;
use App\Http\Controllers\Shared\Kependudukan\KematianController;
use App\Http\Controllers\Shared\Kependudukan\MutasiController;
use App\Http\Controllers\Shared\Kependudukan\PendudukController;
use App\Http\Controllers\Shared\Peta\PetaController;
use App\Http\Controllers\Shared\Usaha\JenisUsahaController;
use App\Http\Controllers\Shared\Usaha\LaporanUsahaController;
use App\Http\Controllers\Shared\Usaha\UsahaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin,rt_rw'])->prefix('import-export')->name('import-export.')->group(function () {
    Route::get('/{module}/export', [ImportExportController::class, 'exportForm'])->name('export');
    Route::post('/{module}/export', [ImportExportController::class, 'export'])->name('export.process');
    Route::get('/{module}/import', [ImportExportController::class, 'importForm'])->name('import');
    Route::post('/{module}/import', [ImportExportController::class, 'import'])->name('import.process');
    Route::get('/{module}/template', [ImportExportController::class, 'downloadTemplate'])->name('template');
});

Route::middleware(['auth', 'role:admin,rt_rw'])->prefix('kependudukan')->name('kependudukan.')->group(function () {
    Route::resource('penduduk', PendudukController::class);
    Route::resource('keluarga', KeluargaController::class);
    Route::resource('mutasi', MutasiController::class)->except(['show']);
    Route::resource('kelahiran', KelahiranController::class)->except(['show']);
    Route::resource('kematian', KematianController::class)->except(['show']);
});

Route::middleware(['auth', 'role:admin,rt_rw'])->prefix('usaha')->name('usaha.')->group(function () {
    Route::get('/', [UsahaController::class, 'index'])->name('index');
    Route::get('/create', [UsahaController::class, 'create'])->name('create');
    Route::post('/', [UsahaController::class, 'store'])->name('store');
    Route::get('/{usaha}/edit', [UsahaController::class, 'edit'])->name('edit');
    Route::put('/{usaha}', [UsahaController::class, 'update'])->name('update');
    Route::delete('/{usaha}', [UsahaController::class, 'destroy'])->name('destroy');

    Route::get('/jenis', [JenisUsahaController::class, 'index'])->name('jenis.index');
    Route::post('/jenis', [JenisUsahaController::class, 'store'])->name('jenis.store');
    Route::put('/jenis/{jenisUsaha}', [JenisUsahaController::class, 'update'])->name('jenis.update');
    Route::delete('/jenis/{jenisUsaha}', [JenisUsahaController::class, 'destroy'])->name('jenis.destroy');

    Route::get('/laporan', [LaporanUsahaController::class, 'index'])->name('laporan');
});

Route::middleware(['auth', 'role:admin,rt_rw'])->prefix('data-umum')->name('data-umum.')->group(function () {
    Route::resource('faskes', FaskesController::class)->except(['show']);
    Route::resource('sekolah', SekolahController::class)->except(['show']);
    Route::resource('tempat-ibadah', TempatIbadahController::class)
        ->except(['show'])
        ->parameters(['tempat-ibadah' => 'tempatIbadah']);
    Route::resource('petugas-kebersihan', PetugasKebersihanController::class)
        ->except(['show'])
        ->parameters(['petugas-kebersihan' => 'petugasKebersihan']);
    Route::resource('kendaraan', KendaraanController::class)->except(['show']);
    Route::resource('kontrakan', KontrakanController::class)->except(['show']);
    Route::resource('asrama', AsramaController::class)->except(['show']);
    Route::resource('pbb', PbbController::class)->except(['show']);
    Route::resource('retribusi-sampah', RetribusiSampahController::class)->except(['show']);
});

Route::middleware(['auth', 'role:admin,operator,rt_rw'])->prefix('peta')->name('peta.')->group(function () {
    Route::get('/', [PetaController::class, 'index'])->name('index');
    Route::get('/geojson/kelurahan', [PetaController::class, 'geojsonKelurahan'])->name('geojson.kelurahan');
    Route::get('/geojson/rw', [PetaController::class, 'geojsonRw'])->name('geojson.rw');
    Route::get('/stats', [PetaController::class, 'stats'])->name('stats');
    Route::get('/geojson/layers', [PetaLayerController::class, 'geojsonLayers'])->name('geojson.layers');

    Route::middleware('role:admin')->group(function () {
        Route::get('/rw/{rw}/polygon', [PetaController::class, 'editRwPolygon'])->name('rw-polygon.edit');
        Route::put('/rw/{rw}/polygon', [PetaController::class, 'updateRwPolygon'])->name('rw-polygon.update');
        Route::delete('/rw/{rw}/polygon', [PetaController::class, 'deleteRwPolygon'])->name('rw-polygon.delete');
        Route::put('/rw/{rw}/color', [PetaController::class, 'updateRwColor'])->name('rw-color.update');
    });
});
