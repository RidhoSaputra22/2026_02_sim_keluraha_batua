<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\PengurusController;
use App\Http\Controllers\Admin\PetaLayerController;
use App\Http\Controllers\Admin\ProfilWilayahController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\RtController as AdminRtController;
use App\Http\Controllers\Admin\RwController as AdminRwController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
// ─── Role-specific Dashboard Controllers ───────────────────────
use App\Http\Controllers\Auth\LoginController;
// ─── Admin Module Controllers ──────────────────────────────────
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataUmum\AsramaController;
use App\Http\Controllers\DataUmum\FaskesController;
use App\Http\Controllers\DataUmum\KendaraanController;
use App\Http\Controllers\DataUmum\KontrakanController;
use App\Http\Controllers\DataUmum\PbbController;
use App\Http\Controllers\DataUmum\RetribusiSampahController;
use App\Http\Controllers\DataUmum\PetugasKebersihanController;
use App\Http\Controllers\DataUmum\SekolahController;
use App\Http\Controllers\DataUmum\TempatIbadahController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\Kependudukan\KelahiranController;
use App\Http\Controllers\Kependudukan\KeluargaController as AdminKeluargaController;
use App\Http\Controllers\Kependudukan\KematianController;
use App\Http\Controllers\Kependudukan\MutasiController;
use App\Http\Controllers\Kependudukan\PendudukController as AdminPendudukController;
use App\Http\Controllers\Laporan\LaporanKependudukanController;
use App\Http\Controllers\Laporan\LaporanUsahaController as LaporanUsahaGlobalController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\ProfileController;
// ─── Data Umum, Agenda Controllers ──────────
use App\Http\Controllers\RtRw\DashboardController as RtRwDashboard;
use App\Http\Controllers\RtRw\LaporanController as RtRwLaporanController;
use App\Http\Controllers\RtRw\WargaController as RtRwWargaController;
use App\Http\Controllers\Usaha\JenisUsahaController;
use App\Http\Controllers\Usaha\LaporanUsahaController;
use App\Http\Controllers\Usaha\UsahaController;
// ─── Kependudukan Controllers ──────────────────────────────────
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard router — redirect ke dashboard sesuai role
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Global Search API
    Route::get('/search', GlobalSearchController::class)->name('global-search');

    // Profile (all authenticated users)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::view('/tentang-aplikasi', 'about.index')->name('about.index');

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  IMPORT & EXPORT DATA                                       ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,rt_rw')->prefix('import-export')->name('import-export.')->group(function () {
        // Export
        Route::get('/{module}/export', [ImportExportController::class, 'exportForm'])->name('export');
        Route::post('/{module}/export', [ImportExportController::class, 'export'])->name('export.process');
        // Import
        Route::get('/{module}/import', [ImportExportController::class, 'importForm'])->name('import');
        Route::post('/{module}/import', [ImportExportController::class, 'import'])->name('import.process');
        // Template
        Route::get('/{module}/template', [ImportExportController::class, 'downloadTemplate'])->name('template');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  ADMIN PANEL                                                ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Pengguna (CRUD)
        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::patch('users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');

        // Role (read-only)
        Route::get('roles', [AdminRoleController::class, 'index'])->name('roles.index');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  DATA MASTER                                                 ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin')->prefix('master')->name('master.')->group(function () {
        // Kependudukan (admin-scoped)
        Route::resource('penduduk', AdminPendudukController::class);
        Route::resource('keluarga', AdminKeluargaController::class);

        // ─── Kelola RW (CRUD + profil/biodata + pengurus) ──────────
        Route::resource('rw', AdminRwController::class);
        Route::delete('rw/{rw}/foto', [AdminRwController::class, 'deleteFoto'])->name('rw.delete-foto');
        Route::post('rw/{rw}/pengurus', [AdminRwController::class, 'storePengurus'])->name('rw.pengurus.store');
        Route::put('rw/{rw}/pengurus/{penguru}', [AdminRwController::class, 'updatePengurus'])->name('rw.pengurus.update');
        Route::delete('rw/{rw}/pengurus/{penguru}', [AdminRwController::class, 'destroyPengurus'])->name('rw.pengurus.destroy');

        // ─── Kelola RT (CRUD + profil/biodata + pengurus) ──────────
        Route::resource('rt', AdminRtController::class);
        Route::delete('rt/{rt}/foto', [AdminRtController::class, 'deleteFoto'])->name('rt.delete-foto');
        Route::post('rt/{rt}/pengurus', [AdminRtController::class, 'storePengurus'])->name('rt.pengurus.store');
        Route::put('rt/{rt}/pengurus/{penguru}', [AdminRtController::class, 'updatePengurus'])->name('rt.pengurus.update');
        Route::delete('rt/{rt}/pengurus/{penguru}', [AdminRtController::class, 'destroyPengurus'])->name('rt.pengurus.destroy');

        // ─── Pengurus RT/RW (standalone CRUD) ─────────────────────
        Route::resource('pengurus', PengurusController::class);

        // ─── Pegawai / Staff Kelurahan ────────────────────────────
        Route::resource('pegawai', PegawaiController::class)->except(['show']);

        // ─── Profil Kelurahan ─────────────────────────────────────
        Route::prefix('profil-wilayah')->name('profil-wilayah.')->group(function () {
            Route::get('/kelurahan/{kelurahan}', [ProfilWilayahController::class, 'kelurahanShow'])->name('kelurahan.show');
            Route::get('/kelurahan/{kelurahan}/edit', [ProfilWilayahController::class, 'kelurahanEdit'])->name('kelurahan.edit');
            Route::put('/kelurahan/{kelurahan}', [ProfilWilayahController::class, 'kelurahanUpdate'])->name('kelurahan.update');
            Route::delete('/kelurahan/{kelurahan}/foto', [ProfilWilayahController::class, 'kelurahanDeleteFoto'])->name('kelurahan.delete-foto');
        });

        // Referensi
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  RT/RW PANEL                                                ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:rt_rw')->prefix('rtrw')->name('rtrw.')->group(function () {
        Route::get('/dashboard', [RtRwDashboard::class, 'index'])->name('dashboard');

        // Warga
        Route::get('/warga', [RtRwWargaController::class, 'index'])->name('warga.index');
        Route::get('/warga/{penduduk}', [RtRwWargaController::class, 'show'])->name('warga.show');
        Route::get('/keluarga', [RtRwWargaController::class, 'keluarga'])->name('keluarga.index');

        // Laporan
        Route::get('/laporan', [RtRwLaporanController::class, 'index'])->name('laporan.index');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: KEPENDUDUKAN — Admin, RT/RW                        ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,rt_rw')->prefix('kependudukan')->name('kependudukan.')->group(function () {
        // Redirect to admin-scoped resources (admin has full access via CheckRole bypass)
        Route::resource('penduduk', AdminPendudukController::class);
        Route::resource('keluarga', AdminKeluargaController::class);
        // Mutasi Penduduk (pindah/datang)
        Route::resource('mutasi', MutasiController::class)->except(['show']);

        // Kelahiran
        Route::resource('kelahiran', KelahiranController::class)->except(['show']);

        // Kematian
        Route::resource('kematian', KematianController::class)->except(['show']);
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: DATA USAHA — Admin, RT/RW                          ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,rt_rw')->prefix('usaha')->name('usaha.')->group(function () {
        // Daftar Usaha (CRUD)
        Route::get('/', [UsahaController::class, 'index'])->name('index');
        Route::get('/create', [UsahaController::class, 'create'])->name('create');
        Route::post('/', [UsahaController::class, 'store'])->name('store');
        Route::get('/{usaha}/edit', [UsahaController::class, 'edit'])->name('edit');
        Route::put('/{usaha}', [UsahaController::class, 'update'])->name('update');
        Route::delete('/{usaha}', [UsahaController::class, 'destroy'])->name('destroy');

        // Jenis Usaha (CRUD inline)
        Route::get('/jenis', [JenisUsahaController::class, 'index'])->name('jenis.index');
        Route::post('/jenis', [JenisUsahaController::class, 'store'])->name('jenis.store');
        Route::put('/jenis/{jenisUsaha}', [JenisUsahaController::class, 'update'])->name('jenis.update');
        Route::delete('/jenis/{jenisUsaha}', [JenisUsahaController::class, 'destroy'])->name('jenis.destroy');

        // Laporan Usaha
        Route::get('/laporan', [LaporanUsahaController::class, 'index'])->name('laporan');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: DATA UMUM — Admin, RT/RW                           ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,rt_rw')->prefix('data-umum')->name('data-umum.')->group(function () {
        // Fasilitas Kesehatan
        Route::resource('faskes', FaskesController::class)->except(['show']);
        // Sekolah
        Route::resource('sekolah', SekolahController::class)->except(['show']);
        // Tempat Ibadah
        Route::resource('tempat-ibadah', TempatIbadahController::class)->except(['show'])->parameters(['tempat-ibadah' => 'tempatIbadah']);
        // Petugas Kebersihan
        Route::resource('petugas-kebersihan', PetugasKebersihanController::class)->except(['show'])->parameters(['petugas-kebersihan' => 'petugasKebersihan']);
        // Kendaraan
        Route::resource('kendaraan', KendaraanController::class)->except(['show']);
        // Kontrakan & Rumah Kost
        Route::resource('kontrakan', KontrakanController::class)->except(['show']);
        // Asrama
        Route::resource('asrama', AsramaController::class)->except(['show']);
        // PBB (Pajak Bumi dan Bangunan)
        Route::resource('pbb', PbbController::class)->except(['show']);
        // Retribusi Sampah
        Route::resource('retribusi-sampah', RetribusiSampahController::class)->except(['show']);
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  PETA KELURAHAN — Admin, RT/RW                               ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,operator,rt_rw')->prefix('peta')->name('peta.')->group(function () {
        Route::get('/', [PetaController::class, 'index'])->name('index');
        Route::get('/geojson/kelurahan', [PetaController::class, 'geojsonKelurahan'])->name('geojson.kelurahan');
        Route::get('/geojson/rw', [PetaController::class, 'geojsonRw'])->name('geojson.rw');
        Route::get('/stats', [PetaController::class, 'stats'])->name('stats');
        Route::get('/geojson/layers', [PetaLayerController::class, 'geojsonLayers'])->name('geojson.layers');

        // RW Polygon Management (admin only)
        Route::middleware('role:admin')->group(function () {
            Route::get('/rw/{rw}/polygon', [PetaController::class, 'editRwPolygon'])->name('rw-polygon.edit');
            Route::put('/rw/{rw}/polygon', [PetaController::class, 'updateRwPolygon'])->name('rw-polygon.update');
            Route::delete('/rw/{rw}/polygon', [PetaController::class, 'deleteRwPolygon'])->name('rw-polygon.delete');
            Route::put('/rw/{rw}/color', [PetaController::class, 'updateRwColor'])->name('rw-color.update');
        });
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  ADMIN: PETA LAYER MANAGEMENT                                ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin')->prefix('admin/peta-layer')->name('admin.peta-layer.')->group(function () {
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

        // Polygon API (JSON)
        Route::post('/{petaLayer}/polygon', [PetaLayerController::class, 'storePolygon'])->name('polygon.store');
        Route::put('/{petaLayer}/polygon/{polygon}', [PetaLayerController::class, 'updatePolygon'])->name('polygon.update');
        Route::delete('/{petaLayer}/polygon/{polygon}', [PetaLayerController::class, 'destroyPolygon'])->name('polygon.destroy');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: LAPORAN — Admin only                                ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin')->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/kependudukan', [LaporanKependudukanController::class, 'index'])->name('kependudukan');
        Route::get('/usaha', [LaporanUsahaGlobalController::class, 'index'])->name('usaha');
    });
});
