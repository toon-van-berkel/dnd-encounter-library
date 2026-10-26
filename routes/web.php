<?php

use App\Http\Controllers\EncounterController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::resource('encounters', EncounterController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);
});

Route::resource('encounters', EncounterController::class)
    ->only(['index', 'show']);

require __DIR__.'/settings.php';
