<?php

use App\Http\Controllers\Auth\DashboardController as AuthDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Shared\Search\GlobalSearchController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [AuthDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/index', [AuthDashboardController::class, 'index'])->name('dashboard.index');

    Route::get('/search', GlobalSearchController::class)->name('global-search');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::view('/tentang-aplikasi', 'about.index')->name('about.index');
});
