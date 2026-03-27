<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Guest\GuestController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GuestController::class, 'welcome'])->name('guest.welcome');
Route::get('/pencarian', [GuestController::class, 'globalSearch'])->name('guest.search');
Route::get('/profil', [GuestController::class, 'profil'])->name('guest.profil');
Route::get('/data-kelurahan', [GuestController::class, 'dataKelurahan'])->name('guest.data-kelurahan');
Route::get('/cek-data', [GuestController::class, 'cekData'])->name('guest.cek-data');
Route::post('/cek-data', [GuestController::class, 'cekDataSearch'])->name('guest.cek-data.search');
Route::get('/surat-online', [GuestController::class, 'suratOnline'])->name('guest.surat-online');
Route::get('/publikasi', [GuestController::class, 'publikasi'])->name('guest.publikasi');
Route::get('/publikasi/{berita:slug}', [GuestController::class, 'showBerita'])->name('guest.berita.show');
Route::get('/dokumen-publik/{dokumenPublik:slug}/unduh', [GuestController::class, 'downloadDokumenPublik'])->name('guest.publikasi.download');
Route::get('/parawisata', [GuestController::class, 'parawisata'])->name('guest.parawisata');
Route::get('/umkm', [GuestController::class, 'umkm'])->name('guest.umkm');
Route::get('/pengaduan', [GuestController::class, 'pengaduan'])->name('guest.pengaduan');
Route::post('/pengaduan', [GuestController::class, 'storePengaduan'])->name('guest.pengaduan.store');
Route::get('/kontak', [GuestController::class, 'kontak'])->name('guest.kontak');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});
