<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDash;
use App\Http\Controllers\Bendahara\DashboardController as BendDash;
use App\Http\Controllers\Anggota\DashboardController as AnggotaDash;
use App\Http\Controllers\Admin\AnggotaController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Admin
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function(){
        Route::get('/dashboard', [AdminDash::class, 'index'])->name('dashboard');
        Route::resource('anggota', AnggotaController::class);
        Route::post('anggota/{anggota}/toggle-active', [AnggotaController::class,'toggleActive'])->name('anggota.toggle-active');
        Route::post('anggota/{anggota}/reset-password', [AnggotaController::class,'resetPassword'])->name('anggota.reset-password');
    });

    // Bendahara
    Route::prefix('bendahara')->name('bendahara.')->middleware('role:bendahara')->group(function(){
        Route::get('/dashboard', [BendDash::class, 'index'])->name('dashboard');
    });

    // Anggota
    Route::prefix('anggota')->name('anggota.')->middleware('role:anggota')->group(function(){
        Route::get('/dashboard', [AnggotaDash::class, 'index'])->name('dashboard');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
