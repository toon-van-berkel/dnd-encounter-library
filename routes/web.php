<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EncounterController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::resource('encounters', EncounterController::class);

require __DIR__.'/settings.php';
