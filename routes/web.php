<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDash;
use App\Http\Controllers\Bendahara\DashboardController as BendDash;
use App\Http\Controllers\Anggota\DashboardController as AnggotaDash;
use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\KegiatanController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\ForumTopicController;
use App\Http\Controllers\Admin\ForumPostController;

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
        Route::resource('kegiatan', KegiatanController::class);
        Route::post('kegiatan/{kegiatan}/toggle-publish', [KegiatanController::class,'togglePublish'])->name('kegiatan.toggle-publish');
        Route::delete('kegiatan/{kegiatan}/poster', [KegiatanController::class,'removePoster'])->name('kegiatan.remove-poster');
        Route::resource('pengumuman', PengumumanController::class);
        Route::post('pengumuman/{pengumuman}/toggle-publish', [PengumumanController::class,'togglePublish'])->name('pengumuman.toggle-publish');
        Route::post('pengumuman/{pengumuman}/toggle-pin', [PengumumanController::class,'togglePin'])->name('pengumuman.toggle-pin');
        Route::delete('pengumuman/{pengumuman}/cover', [PengumumanController::class,'removeCover'])->name('pengumuman.remove-cover');
        // Topik
        Route::resource('forum', ForumTopicController::class);
        Route::post('forum/{forum}/toggle-open', [ForumTopicController::class,'toggleOpen'])->name('forum.toggle-open');
        Route::post('forum/{forum}/toggle-pin', [ForumTopicController::class,'togglePin'])->name('forum.toggle-pin');
        Route::post('forum/{forum}/unmark-solved', [ForumTopicController::class,'unmarkSolved'])->name('forum.unmark-solved');

        // Post (nested, sebagian besar lewat halaman show topik)
        Route::post('forum/{forum}/posts', [ForumPostController::class,'store'])->name('forum.posts.store');
        Route::put('forum/{forum}/posts/{post}', [ForumPostController::class,'update'])->name('forum.posts.update');
        Route::delete('forum/{forum}/posts/{post}', [ForumPostController::class,'destroy'])->name('forum.posts.destroy');
        Route::post('forum/{forum}/posts/{post}/mark-solution', [ForumPostController::class,'markSolution'])->name('forum.posts.mark-solution');
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