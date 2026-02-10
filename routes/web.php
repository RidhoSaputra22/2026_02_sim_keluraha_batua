<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;

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

    // Dashboard — semua role, controller mengarahkan berdasarkan role
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // ─── Admin Only ────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Pengguna
        Route::get('/users', fn () => 'Users page')->name('users.index');
        // Role & Hak Akses
        Route::get('/roles', fn () => 'Roles page')->name('roles.index');
        // Audit Log
        Route::get('/audit-log', fn () => 'Audit log page')->name('audit-log');
    });

    // ─── Data Master — Admin Only ──────────────────────────────────
    Route::middleware('role:admin')->prefix('master')->name('master.')->group(function () {
        Route::get('/wilayah', fn () => 'Wilayah page')->name('wilayah.index');
        Route::get('/penandatangan', fn () => 'Penandatangan page')->name('penandatangan.index');
        Route::get('/jenis-surat', fn () => 'Jenis Surat page')->name('jenis-surat.index');
        Route::get('/template-surat', fn () => 'Template Surat page')->name('template-surat.index');
        Route::get('/referensi', fn () => 'Data Referensi page')->name('referensi.index');
    });

    // ─── Kependudukan — Admin, Operator, RT/RW ────────────────────
    Route::middleware('role:admin,operator,rt_rw')->prefix('kependudukan')->name('kependudukan.')->group(function () {
        Route::get('/penduduk', fn () => 'Data Penduduk page')->name('penduduk.index');
        Route::get('/kk', fn () => 'Kartu Keluarga page')->name('kk.index');
        Route::get('/mutasi', fn () => 'Mutasi Penduduk page')->name('mutasi.index');
        Route::get('/kelahiran', fn () => 'Kelahiran page')->name('kelahiran.index');
        Route::get('/kematian', fn () => 'Kematian page')->name('kematian.index');
    });

    // ─── Persuratan — Admin, Operator, Verifikator, Penandatangan ─
    Route::middleware('role:admin,operator,verifikator,penandatangan,warga')->prefix('persuratan')->name('persuratan.')->group(function () {
        Route::get('/permohonan', fn () => 'Permohonan Surat page')->name('permohonan.index');
        Route::get('/verifikasi', fn () => 'Verifikasi page')->name('verifikasi.index');
        Route::get('/tanda-tangan', fn () => 'Tanda Tangan page')->name('tanda-tangan.index');
        Route::get('/arsip', fn () => 'Arsip Surat page')->name('arsip.index');
        Route::get('/tracking', fn () => 'Tracking Layanan page')->name('tracking.index');
    });

    // ─── Data Usaha / PK5 — Admin, Operator ───────────────────────
    Route::middleware('role:admin,operator')->prefix('usaha')->name('usaha.')->group(function () {
        Route::get('/', fn () => 'Daftar Usaha page')->name('index');
        Route::get('/jenis', fn () => 'Jenis Usaha page')->name('jenis.index');
        Route::get('/laporan', fn () => 'Laporan Usaha page')->name('laporan');
    });

    // ─── Laporan — Admin, Operator, Verifikator ───────────────────
    Route::middleware('role:admin,operator,verifikator')->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/kependudukan', fn () => 'Laporan Kependudukan page')->name('kependudukan');
        Route::get('/persuratan', fn () => 'Laporan Persuratan page')->name('persuratan');
        Route::get('/usaha', fn () => 'Laporan Usaha page')->name('usaha');
    });

    // ─── RT/RW Khusus ─────────────────────────────────────────────
    Route::middleware('role:admin,rt_rw')->prefix('rtrw')->name('rtrw.')->group(function () {
        Route::get('/pengantar', fn () => 'Surat Pengantar page')->name('pengantar.index');
        Route::get('/laporan', fn () => 'Laporan & Pengaduan page')->name('laporan.index');
    });

    // ─── Warga Layanan Mandiri ────────────────────────────────────
    Route::middleware('role:admin,warga')->prefix('warga')->name('warga.')->group(function () {
        Route::get('/permohonan', fn () => 'Ajukan Permohonan page')->name('permohonan.index');
        Route::get('/riwayat', fn () => 'Riwayat & Tracking page')->name('riwayat');
        Route::get('/dokumen', fn () => 'Unduh Dokumen page')->name('dokumen');
    });
});

// Redirect welcome for now
Route::get('/welcome', function () {
    return view('welcome');
});
