<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EncounterController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'can:access-admin'])->group(function () {
    Route::get('admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('admin/encounters/{encounter}/toggle-status', [AdminController::class, 'toggleStatus'])
        ->name('admin.encounters.toggle-status');
});

Route::middleware('auth')->group(function () {
    Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('encounters/{encounter}/favorite', [FavoriteController::class, 'store'])
        ->name('encounters.favorite');
    Route::delete('encounters/{encounter}/favorite', [FavoriteController::class, 'destroy'])
        ->name('encounters.unfavorite');

    Route::resource('encounters', EncounterController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);
});

Route::resource('encounters', EncounterController::class)
    ->only(['index', 'show']);

require __DIR__.'/settings.php';
