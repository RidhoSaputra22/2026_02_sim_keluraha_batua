<?php

use App\Http\Controllers\RtRw\DashboardController as RtRwDashboardController;
use App\Http\Controllers\RtRw\LaporanController as RtRwLaporanController;
use App\Http\Controllers\RtRw\WargaController as RtRwWargaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:rt_rw'])->prefix('rtrw')->name('rtrw.')->group(function () {
    Route::get('/dashboard', [RtRwDashboardController::class, 'index'])->name('dashboard');

    Route::get('/warga', [RtRwWargaController::class, 'index'])->name('warga.index');
    Route::get('/warga/{penduduk}', [RtRwWargaController::class, 'show'])->name('warga.show');
    Route::get('/keluarga', [RtRwWargaController::class, 'keluarga'])->name('keluarga.index');

    Route::get('/laporan', [RtRwLaporanController::class, 'index'])->name('laporan.index');
});
