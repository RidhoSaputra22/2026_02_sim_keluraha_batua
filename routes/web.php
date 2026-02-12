<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\JenisSuratController as AdminJenisSuratController;
use App\Http\Controllers\Admin\KeluargaController as AdminKeluargaController;
// ─── Role-specific Dashboard Controllers ───────────────────────
use App\Http\Controllers\Admin\PegawaiController as AdminPegawaiController;
use App\Http\Controllers\Admin\PenandatanganController as AdminPenandatanganController;
use App\Http\Controllers\Admin\PendudukController as AdminPendudukController;
use App\Http\Controllers\Admin\ReferensiController as AdminReferensiController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\TemplateSuratController as AdminTemplateSuratController;
// ─── Admin Module Controllers ──────────────────────────────────
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\WilayahController as AdminWilayahController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kependudukan\KelahiranController;
use App\Http\Controllers\Kependudukan\KematianController;
use App\Http\Controllers\Kependudukan\MutasiController;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboard;
use App\Http\Controllers\Penandatangan\DashboardController as PenandatanganDashboard;
use App\Http\Controllers\Persuratan\ArsipController;
use App\Http\Controllers\Persuratan\PermohonanController;
use App\Http\Controllers\Persuratan\TandaTanganController;
use App\Http\Controllers\Persuratan\TrackingController;
use App\Http\Controllers\Persuratan\VerifikasiController;
use App\Http\Controllers\RtRw\DashboardController as RtRwDashboard;
use App\Http\Controllers\RtRw\WargaController as RtRwWargaController;
use App\Http\Controllers\RtRw\PengantarController as RtRwPengantarController;
use App\Http\Controllers\RtRw\LaporanController as RtRwLaporanController;
use App\Http\Controllers\Usaha\JenisUsahaController;
use App\Http\Controllers\Usaha\LaporanUsahaController;
use App\Http\Controllers\Usaha\UsahaController;
use App\Http\Controllers\Laporan\LaporanKependudukanController;
use App\Http\Controllers\Laporan\LaporanPersuratanController;
use App\Http\Controllers\Laporan\LaporanUsahaController as LaporanUsahaGlobalController;
// ─── Ekspedisi, Data Umum, Agenda, Survey Controllers ──────────
use App\Http\Controllers\Ekspedisi\EkspedisiController;
use App\Http\Controllers\DataUmum\FaskesController;
use App\Http\Controllers\DataUmum\SekolahController;
use App\Http\Controllers\DataUmum\TempatIbadahController;
use App\Http\Controllers\DataUmum\PetugasKebersihanController;
use App\Http\Controllers\DataUmum\KendaraanController;
use App\Http\Controllers\Agenda\AgendaKegiatanController;
use App\Http\Controllers\Survey\SurveyKepuasanController;
// ─── Kependudukan Controllers ──────────────────────────────────
use App\Http\Controllers\Verifikator\DashboardController as VerifikatorDashboard;
use App\Http\Controllers\Warga\DashboardController as WargaDashboard;
use App\Http\Controllers\Warga\PermohonanController as WargaPermohonanController;
use App\Http\Controllers\Warga\RiwayatController as WargaRiwayatController;
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

        // Master Data
        Route::resource('jenis-surat', AdminJenisSuratController::class)->parameters(['jenis-surat' => 'jenisSurat']);
        Route::resource('template-surat', AdminTemplateSuratController::class)->parameters(['template-surat' => 'templateSurat']);
        Route::get('/referensi', [AdminReferensiController::class, 'index'])->name('referensi.index');

        // Audit Log
        Route::get('/audit-log', [AdminAuditLogController::class, 'index'])->name('audit-log');
        Route::get('/audit-log/{auditLog}', [AdminAuditLogController::class, 'show'])->name('audit-log.show');
        Route::delete('/audit-log/cleanup', [AdminAuditLogController::class, 'destroy'])->name('audit-log.destroy');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  DATA MASTER — Legacy aliases (redirect to admin routes)    ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin')->prefix('master')->name('master.')->group(function () {
        Route::get('/wilayah', fn () => redirect()->route('admin.wilayah.index'))->name('wilayah.index');
        Route::get('/penandatangan', fn () => redirect()->route('admin.penandatangan.index'))->name('penandatangan.index');
        Route::get('/jenis-surat', fn () => redirect()->route('admin.jenis-surat.index'))->name('jenis-surat.index');
        Route::get('/template-surat', fn () => redirect()->route('admin.template-surat.index'))->name('template-surat.index');
        Route::get('/referensi', fn () => redirect()->route('admin.referensi.index'))->name('referensi.index');
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

        // Warga
        Route::get('/warga', [RtRwWargaController::class, 'index'])->name('warga.index');
        Route::get('/warga/{penduduk}', [RtRwWargaController::class, 'show'])->name('warga.show');
        Route::get('/keluarga', [RtRwWargaController::class, 'keluarga'])->name('keluarga.index');

        // Surat Pengantar
        Route::get('/pengantar', [RtRwPengantarController::class, 'index'])->name('pengantar.index');
        Route::get('/pengantar/{surat}', [RtRwPengantarController::class, 'show'])->name('pengantar.show');

        // Laporan
        Route::get('/laporan', [RtRwLaporanController::class, 'index'])->name('laporan.index');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  WARGA PANEL                                                ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:warga')->prefix('warga')->name('warga.')->group(function () {
        Route::get('/dashboard', [WargaDashboard::class, 'index'])->name('dashboard');

        // Permohonan Surat
        Route::get('/permohonan', [WargaPermohonanController::class, 'index'])->name('permohonan.index');
        Route::get('/permohonan/create', [WargaPermohonanController::class, 'create'])->name('permohonan.create');
        Route::post('/permohonan', [WargaPermohonanController::class, 'store'])->name('permohonan.store');
        Route::get('/permohonan/{permohonan}', [WargaPermohonanController::class, 'show'])->name('permohonan.show');

        // Riwayat & Tracking
        Route::get('/riwayat', [WargaRiwayatController::class, 'index'])->name('riwayat.index');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: KEPENDUDUKAN — Admin, Operator, RT/RW             ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,operator,rt_rw')->prefix('kependudukan')->name('kependudukan.')->group(function () {
        // Redirect to admin-scoped resources (admin has full access via CheckRole bypass)
        Route::get('/penduduk', fn () => redirect()->route('admin.penduduk.index'))->name('penduduk.index');
        Route::get('/kk', fn () => redirect()->route('admin.keluarga.index'))->name('kk.index');

        // Mutasi Penduduk (pindah/datang)
        Route::resource('mutasi', MutasiController::class)->except(['show']);

        // Kelahiran
        Route::resource('kelahiran', KelahiranController::class)->except(['show']);

        // Kematian
        Route::resource('kematian', KematianController::class)->except(['show']);
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: PERSURATAN — Admin, Operator, Verifikator, etc.   ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,operator,verifikator,penandatangan,warga')->prefix('persuratan')->name('persuratan.')->group(function () {
        // Permohonan Surat (CRUD)
        Route::get('/permohonan', [PermohonanController::class, 'index'])->name('permohonan.index');
        Route::get('/permohonan/create', [PermohonanController::class, 'create'])->name('permohonan.create');
        Route::post('/permohonan', [PermohonanController::class, 'store'])->name('permohonan.store');
        Route::get('/permohonan/{permohonan}/edit', [PermohonanController::class, 'edit'])->name('permohonan.edit');
        Route::put('/permohonan/{permohonan}', [PermohonanController::class, 'update'])->name('permohonan.update');
        Route::delete('/permohonan/{permohonan}', [PermohonanController::class, 'destroy'])->name('permohonan.destroy');

        // Verifikasi (list + approve/reject actions)
        Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::post('/verifikasi/{surat}/approve', [VerifikasiController::class, 'approve'])->name('verifikasi.approve');
        Route::post('/verifikasi/{surat}/reject', [VerifikasiController::class, 'reject'])->name('verifikasi.reject');

        // Tanda Tangan (list + sign/reject actions)
        Route::get('/tanda-tangan', [TandaTanganController::class, 'index'])->name('tanda-tangan.index');
        Route::post('/tanda-tangan/{surat}/sign', [TandaTanganController::class, 'sign'])->name('tanda-tangan.sign');
        Route::post('/tanda-tangan/{surat}/reject', [TandaTanganController::class, 'reject'])->name('tanda-tangan.reject');

        // Arsip Surat (read-only index with filters)
        Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');

        // Tracking Layanan (search & track)
        Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: DATA USAHA / PK5 — Admin, Operator                ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,operator')->prefix('usaha')->name('usaha.')->group(function () {
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
    // ║  SHARED: EKSPEDISI SURAT — Admin, Operator                 ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,operator')->prefix('ekspedisi')->name('ekspedisi.')->group(function () {
        Route::get('/', [EkspedisiController::class, 'index'])->name('index');
        Route::get('/create', [EkspedisiController::class, 'create'])->name('create');
        Route::post('/', [EkspedisiController::class, 'store'])->name('store');
        Route::get('/{ekspedisi}/edit', [EkspedisiController::class, 'edit'])->name('edit');
        Route::put('/{ekspedisi}', [EkspedisiController::class, 'update'])->name('update');
        Route::delete('/{ekspedisi}', [EkspedisiController::class, 'destroy'])->name('destroy');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: DATA UMUM — Admin, Operator                       ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,operator')->prefix('data-umum')->name('data-umum.')->group(function () {
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
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: AGENDA & KEGIATAN — Admin, Operator               ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,operator')->prefix('agenda')->name('agenda.')->group(function () {
        Route::get('/', [AgendaKegiatanController::class, 'index'])->name('index');
        Route::get('/create', [AgendaKegiatanController::class, 'create'])->name('create');
        Route::post('/', [AgendaKegiatanController::class, 'store'])->name('store');
        Route::get('/{agenda}', [AgendaKegiatanController::class, 'show'])->name('show');
        Route::get('/{agenda}/edit', [AgendaKegiatanController::class, 'edit'])->name('edit');
        Route::put('/{agenda}', [AgendaKegiatanController::class, 'update'])->name('update');
        Route::delete('/{agenda}', [AgendaKegiatanController::class, 'destroy'])->name('destroy');
        Route::post('/{agenda}/hasil', [AgendaKegiatanController::class, 'storeHasil'])->name('hasil.store');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: SURVEY KEPUASAN — All Roles                       ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,operator,verifikator,penandatangan,rt_rw,warga')->prefix('survey')->name('survey.')->group(function () {
        Route::get('/', [SurveyKepuasanController::class, 'index'])->name('index');
        Route::get('/create', [SurveyKepuasanController::class, 'create'])->name('create');
        Route::post('/', [SurveyKepuasanController::class, 'store'])->name('store');
        Route::get('/{survey}/edit', [SurveyKepuasanController::class, 'edit'])->name('edit');
        Route::put('/{survey}', [SurveyKepuasanController::class, 'update'])->name('update');
        Route::delete('/{survey}', [SurveyKepuasanController::class, 'destroy'])->name('destroy');
    });

    // ╔══════════════════════════════════════════════════════════════╗
    // ║  SHARED: LAPORAN — Admin, Operator, Verifikator, Penandatangan ║
    // ╚══════════════════════════════════════════════════════════════╝
    Route::middleware('role:admin,operator,verifikator,penandatangan')->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/kependudukan', [LaporanKependudukanController::class, 'index'])->name('kependudukan');
        Route::get('/persuratan', [LaporanPersuratanController::class, 'index'])->name('persuratan');
        Route::get('/usaha', [LaporanUsahaGlobalController::class, 'index'])->name('usaha');
    });
});
