<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;

// ─── Role-specific Dashboard Controllers ───────────────────────
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboard;
use App\Http\Controllers\Verifikator\DashboardController as VerifikatorDashboard;
use App\Http\Controllers\Penandatangan\DashboardController as PenandatanganDashboard;
use App\Http\Controllers\RtRw\DashboardController as RtRwDashboard;
use App\Http\Controllers\Warga\DashboardController as WargaDashboard;

// ─── Admin Module Controllers ──────────────────────────────────
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\PendudukController as AdminPendudukController;
use App\Http\Controllers\Admin\KeluargaController as AdminKeluargaController;
use App\Http\Controllers\Admin\WilayahController as AdminWilayahController;
use App\Http\Controllers\Admin\PenandatanganController as AdminPenandatanganController;
use App\Http\Controllers\Admin\PegawaiController as AdminPegawaiController;

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

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  ADMIN PANEL                                                ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Pengguna (CRUD)
        Route::resource('users', AdminUserController::class);
        Route::patch('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');

        // Role (read-only)
        Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');

        // Kependudukan (admin-scoped)
        Route::resource('penduduk', AdminPendudukController::class);
        Route::resource('keluarga', AdminKeluargaController::class);

        // Data Master (admin-scoped)
        Route::resource('wilayah', AdminWilayahController::class);
        Route::resource('penandatangan', AdminPenandatanganController::class);
        Route::resource('pegawai', AdminPegawaiController::class);

        // Audit Log
        Route::get('/audit-log', fn () => view('pages.admin.audit-log.index'))->name('audit-log');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  DATA MASTER — Legacy aliases (redirect to admin routes)    ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin')->prefix('master')->name('master.')->group(function () {
        Route::get('/wilayah', fn () => redirect()->route('admin.wilayah.index'))->name('wilayah.index');
        Route::get('/penandatangan', fn () => redirect()->route('admin.penandatangan.index'))->name('penandatangan.index');
        Route::get('/jenis-surat', fn () => 'Jenis Surat page')->name('jenis-surat.index');
        Route::get('/template-surat', fn () => 'Template Surat page')->name('template-surat.index');
        Route::get('/referensi', fn () => 'Data Referensi page')->name('referensi.index');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  OPERATOR PANEL                                             ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:operator')->prefix('operator')->name('operator.')->group(function () {
        Route::get('/dashboard', [OperatorDashboard::class, 'index'])->name('dashboard');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  VERIFIKATOR PANEL                                          ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:verifikator')->prefix('verifikator')->name('verifikator.')->group(function () {
        Route::get('/dashboard', [VerifikatorDashboard::class, 'index'])->name('dashboard');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  PENANDATANGAN PANEL                                        ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:penandatangan')->prefix('penandatangan')->name('penandatangan.')->group(function () {
        Route::get('/dashboard', [PenandatanganDashboard::class, 'index'])->name('dashboard');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  RT/RW PANEL                                                ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:rt_rw')->prefix('rtrw')->name('rtrw.')->group(function () {
        Route::get('/dashboard', [RtRwDashboard::class, 'index'])->name('dashboard');
        Route::get('/pengantar', fn () => 'Surat Pengantar page')->name('pengantar.index');
        Route::get('/laporan', fn () => 'Laporan & Pengaduan page')->name('laporan.index');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  WARGA PANEL                                                ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:warga')->prefix('warga')->name('warga.')->group(function () {
        Route::get('/dashboard', [WargaDashboard::class, 'index'])->name('dashboard');
        Route::get('/permohonan', fn () => 'Ajukan Permohonan page')->name('permohonan.index');
        Route::get('/riwayat', fn () => 'Riwayat & Tracking page')->name('riwayat');
        Route::get('/dokumen', fn () => 'Unduh Dokumen page')->name('dokumen');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: KEPENDUDUKAN — Admin, Operator, RT/RW             ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,operator,rt_rw')->prefix('kependudukan')->name('kependudukan.')->group(function () {
        // Redirect to admin-scoped resources (admin has full access via CheckRole bypass)
        Route::get('/penduduk', fn () => redirect()->route('admin.penduduk.index'))->name('penduduk.index');
        Route::get('/kk', fn () => redirect()->route('admin.keluarga.index'))->name('kk.index');
        Route::get('/mutasi', fn () => 'Mutasi Penduduk page')->name('mutasi.index');
        Route::get('/kelahiran', fn () => 'Kelahiran page')->name('kelahiran.index');
        Route::get('/kematian', fn () => 'Kematian page')->name('kematian.index');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: PERSURATAN — Admin, Operator, Verifikator, etc.   ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,operator,verifikator,penandatangan,warga')->prefix('persuratan')->name('persuratan.')->group(function () {
        Route::get('/permohonan', fn () => 'Permohonan Surat page')->name('permohonan.index');
        Route::get('/verifikasi', fn () => 'Verifikasi page')->name('verifikasi.index');
        Route::get('/tanda-tangan', fn () => 'Tanda Tangan page')->name('tanda-tangan.index');
        Route::get('/arsip', fn () => 'Arsip Surat page')->name('arsip.index');
        Route::get('/tracking', fn () => 'Tracking Layanan page')->name('tracking.index');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: DATA USAHA / PK5 — Admin, Operator                ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,operator')->prefix('usaha')->name('usaha.')->group(function () {
        Route::get('/', fn () => 'Daftar Usaha page')->name('index');
        Route::get('/jenis', fn () => 'Jenis Usaha page')->name('jenis.index');
        Route::get('/laporan', fn () => 'Laporan Usaha page')->name('laporan');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: LAPORAN — Admin, Operator, Verifikator             ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,operator,verifikator')->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/kependudukan', fn () => 'Laporan Kependudukan page')->name('kependudukan');
        Route::get('/persuratan', fn () => 'Laporan Persuratan page')->name('persuratan');
        Route::get('/usaha', fn () => 'Laporan Usaha page')->name('usaha');
    });
});

