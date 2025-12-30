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
use App\Http\Controllers\Admin\ArsipController;
use App\Http\Controllers\Admin\DokumentasiAlbumController;
use App\Http\Controllers\Admin\DokumentasiMediaController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporan;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Anggota\KegiatanAnggotaController as AnggotaKegiatan;
use App\Http\Controllers\Anggota\PengumumanAnggotaController as AnggotaPengumuman;
use App\Http\Controllers\Anggota\ForumTopicController as AnggotaForum;
use App\Http\Controllers\Anggota\ForumPostController as AnggotaPost;
use App\Http\Controllers\Anggota\ArsipAnggotaController as AnggotaArsip;
use App\Http\Controllers\Anggota\DokumentasiAnggotaController as AnggotaDokumentasi;
use App\Http\Controllers\Anggota\LaporanController as AnggotaLaporan;
use App\Http\Controllers\Anggota\IuranController as AnggotaIuran;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Bendahara\IuranDashboardController as BendIuranDashboard;
use App\Http\Controllers\Bendahara\IuranTagihanController as BendTagihan;
use App\Http\Controllers\Bendahara\IuranPembayaranController as BendPembayaran;
use App\Http\Controllers\Bendahara\IuranBulkController as BendBulk;
use App\Http\Controllers\Bendahara\IuranReportController as BendReport;
use App\Http\Controllers\Webhook\XenditWebhookController;

Route::view('/welcome', 'welcome')->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/dashboard', HomeController::class)->name('dashboard');

    // Admin
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function(){
        Route::get('/dashboard', [AdminDash::class, 'index'])->name('dashboard');
        Route::resource('anggota', AnggotaController::class);
        Route::post('anggota/{anggotum}/toggle-active', [AnggotaController::class,'toggleActive'])->name('anggota.toggle-active');
        Route::post('anggota/{anggotum}/reset-password', [AnggotaController::class,'resetPassword'])->name('anggota.reset-password');
        Route::resource('kegiatan', KegiatanController::class);
        Route::post('kegiatan/{kegiatan}/toggle-publish', [KegiatanController::class,'togglePublish'])->name('kegiatan.toggle-publish');
        Route::delete('kegiatan/{kegiatan}/poster', [KegiatanController::class,'removePoster'])->name('kegiatan.remove-poster');
        Route::resource('pengumuman', PengumumanController::class);
        Route::post('pengumuman/{pengumuman}/toggle-publish', [PengumumanController::class,'togglePublish'])->name('pengumuman.toggle-publish');
        Route::post('pengumuman/{pengumuman}/toggle-pin', [PengumumanController::class,'togglePin'])->name('pengumuman.toggle-pin');
        Route::match(['post','get'],'pengumuman/{pengumuman}/email', [PengumumanController::class,'sendEmail'])->name('pengumuman.email');
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
        Route::resource('arsip', ArsipController::class);
        Route::post('arsip/{arsip}/toggle-publish', [ArsipController::class,'togglePublish'])->name('arsip.toggle-publish');
        Route::delete('arsip/{arsip}/file', [ArsipController::class,'removeFile'])->name('arsip.remove-file');
        Route::get('arsip/{arsip}/download', [ArsipController::class,'download'])->name('arsip.download');
        Route::prefix('dokumentasi')->name('dokumentasi.')->group(function () {
            // Albums
            Route::resource('albums', DokumentasiAlbumController::class)->names('albums');
            Route::post('albums/{album}/toggle-publish', [DokumentasiAlbumController::class,'togglePublish'])->name('albums.toggle-publish');
            Route::post('albums/{album}/remove-cover', [DokumentasiAlbumController::class,'removeCover'])->name('albums.remove-cover');
            Route::post('albums/{album}/media/{media}/set-cover', [DokumentasiAlbumController::class,'setCover'])->name('albums.media.set-cover');

            // Media
            Route::post('albums/{album}/media', [DokumentasiMediaController::class,'store']);
            Route::put('albums/{album}/media/{media}', [DokumentasiMediaController::class,'update'])->name('albums.media.update');
            Route::delete('albums/{album}/media/{media}', [DokumentasiMediaController::class,'destroy'])->name('albums.media.destroy');
            Route::get('albums/{album}/media/{media}/download', [DokumentasiMediaController::class,'download'])->name('albums.media.download');
        });

        // Laporan (view only)
        Route::get('laporan', [AdminLaporan::class,'index'])->name('laporan.index');
        Route::get('laporan/create', [AdminLaporan::class,'create'])->name('laporan.create');
        Route::post('laporan', [AdminLaporan::class,'store'])->name('laporan.store');
        Route::get('laporan/{laporan}', [AdminLaporan::class,'show'])->name('laporan.show');
        Route::get('laporan/{laporan}/attachment/{attachment}/download', [AdminLaporan::class,'attachmentDownload'])->name('laporan.attachment.download');

        Route::resource('semesters', SemesterController::class);
    });

    // Bendahara
    Route::prefix('bendahara')->name('bendahara.')->middleware('role:bendahara')->group(function(){
        Route::get('/dashboard', [BendDash::class, 'index'])->name('dashboard');
        Route::get('/iuran', [BendIuranDashboard::class, 'index'])->name('iuran.dashboard');

        Route::get('/iuran/tagihan/export', [BendTagihan::class, 'export'])->name('tagihan.export');
        Route::resource('iuran/tagihan', BendTagihan::class)->names('tagihan');

        Route::get('/iuran/pembayaran', [BendPembayaran::class, 'index'])->name('pembayaran.index');
        Route::get('/iuran/pembayaran/{pembayaran}', [BendPembayaran::class, 'show'])->name('pembayaran.show');
        Route::post('/iuran/pembayaran/{pembayaran}/verify', [BendPembayaran::class, 'verify'])->name('pembayaran.verify');
        Route::post('/iuran/pembayaran/{pembayaran}/reject', [BendPembayaran::class, 'reject'])->name('pembayaran.reject');
        Route::delete('/iuran/pembayaran/{pembayaran}', [BendPembayaran::class, 'destroy'])->name('pembayaran.destroy');

        Route::get('/iuran/bulk', [BendBulk::class, 'index'])->name('bulk.index');
        Route::post('/iuran/bulk', [BendBulk::class, 'generate'])->name('bulk.generate');

        Route::get('/laporan', [BendReport::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [BendReport::class, 'exportIndexCsv'])->name('laporan.export.index');
        Route::get('/laporan/arus-kas', [BendReport::class, 'arusKas'])->name('laporan.arus');
        Route::get('/laporan/arus-kas/export', [BendReport::class, 'exportArusCsv'])->name('laporan.export.arus');
        Route::get('/laporan/piutang', [BendReport::class, 'piutang'])->name('laporan.piutang');
        Route::get('/laporan/piutang/export', [BendReport::class, 'exportPiutangCsv'])->name('laporan.export.piutang');
        Route::get('/laporan/anggota', [BendReport::class, 'anggotaIndex'])->name('laporan.anggota.index');
        Route::get('/laporan/anggota/export', [BendReport::class, 'exportAnggotaCsv'])->name('laporan.export.anggota');
        Route::get('/laporan/anggota/{user}', [BendReport::class, 'anggotaShow'])->name('laporan.anggota.show');
        Route::get('/laporan/anggota/{user}/export', [BendReport::class, 'exportLedgerCsv'])->name('laporan.export.anggota-ledger');
    });

    // Anggota
    Route::prefix('anggota')->name('anggota.')->middleware('role:anggota')->group(function(){
        Route::get('/home', function(){ return view('anggota.home'); })->name('home');
        Route::get('/dashboard', [AnggotaDash::class, 'index'])->name('dashboard');
        // Kegiatan (anggota)
        Route::get('kegiatan', [AnggotaKegiatan::class,'index'])->name('kegiatan.index');
        Route::get('kegiatan/saya', [AnggotaKegiatan::class,'mine'])->name('kegiatan.mine');
        Route::get('kegiatan/{kegiatan:slug}', [AnggotaKegiatan::class,'show'])->name('kegiatan.show');
        Route::post('kegiatan/{kegiatan}/daftar', [AnggotaKegiatan::class,'register'])->name('kegiatan.register');
        Route::delete('kegiatan/{kegiatan}/batal', [AnggotaKegiatan::class,'unregister'])->name('kegiatan.unregister');
        Route::get('kegiatan/{kegiatan}/ics', [AnggotaKegiatan::class,'ics'])->name('kegiatan.ics');

        // Pengumuman (anggota)
        Route::get('pengumuman', [AnggotaPengumuman::class,'index'])->name('pengumuman.index');
        Route::get('pengumuman/{pengumuman:slug}', [AnggotaPengumuman::class,'show'])->name('pengumuman.show');
        Route::post('pengumuman/mark-all-read', [AnggotaPengumuman::class,'markAllRead'])->name('pengumuman.mark-all-read');

        // Forum (anggota)
        Route::resource('forum', AnggotaForum::class);
        Route::post('forum/{forum}/toggle-open-self', [AnggotaForum::class,'toggleOpenSelf'])->name('forum.toggle-open-self');

        // Posts (nested)
        Route::post('forum/{forum}/posts', [AnggotaPost::class,'store'])->name('forum.posts.store');
        Route::put('forum/{forum}/posts/{post}', [AnggotaPost::class,'update'])->name('forum.posts.update');
        Route::delete('forum/{forum}/posts/{post}', [AnggotaPost::class,'destroy'])->name('forum.posts.destroy');
        Route::post('forum/{forum}/posts/{post}/mark-solution', [AnggotaPost::class,'markSolution'])->name('forum.posts.mark-solution');

        // Arsip (anggota)
        Route::get('arsip', [AnggotaArsip::class,'index'])->name('arsip.index');
        Route::get('arsip/{arsip:slug}', [AnggotaArsip::class,'show'])->name('arsip.show');
        Route::get('arsip/{arsip}/download', [AnggotaArsip::class,'download'])->name('arsip.download');

        // Dokumentasi (anggota)
        Route::get('dokumentasi', [AnggotaDokumentasi::class,'index'])->name('dokumentasi.index');
        Route::get('dokumentasi/{album:slug}', [AnggotaDokumentasi::class,'show'])->name('dokumentasi.show');
        Route::get('dokumentasi/{album:slug}/media/{media}/download', [AnggotaDokumentasi::class,'mediaDownload'])->name('dokumentasi.media.download');

        // Laporan (anggota)
        Route::get('laporan', [AnggotaLaporan::class,'index'])->name('laporan.index');
        Route::get('laporan/{laporan}', [AnggotaLaporan::class,'show'])->name('laporan.show');
        Route::get('laporan/{laporan}/attachment/{attachment}/download', [AnggotaLaporan::class,'downloadAttachment'])->name('laporan.attachment.download');

        // Iuran (anggota)
        Route::get('iuran', [AnggotaIuran::class,'dashboard'])->name('iuran.dashboard');
        Route::get('iuran/tagihan', [AnggotaIuran::class,'tagihanIndex'])->name('iuran.tagihan.index');
        Route::get('iuran/tagihan/{tagihan}', [AnggotaIuran::class,'tagihanShow'])->name('iuran.tagihan.show');
        Route::get('iuran/tagihan/{tagihan}/bayar', [AnggotaIuran::class,'bayarForm'])->name('iuran.tagihan.bayar.form');
        Route::post('iuran/tagihan/{tagihan}/bayar', [AnggotaIuran::class,'bayar'])->name('iuran.tagihan.bayar');
        Route::get('iuran/pembayaran/{pembayaran}/bukti', [AnggotaIuran::class,'buktiDownload'])->name('iuran.pembayaran.bukti');
        Route::get('iuran/pembayaran/{pembayaran}/sandbox', [AnggotaIuran::class,'sandboxCheckout'])->name('iuran.pembayaran.sandbox');
        Route::delete('iuran/pembayaran/{pembayaran}', [AnggotaIuran::class,'hapusPembayaran'])->name('iuran.pembayaran.destroy');
        Route::get('iuran/pembayaran/{pembayaran}/receipt', [AnggotaIuran::class,'receipt'])->name('iuran.pembayaran.receipt');
        Route::get('iuran/pembayaran/{pembayaran}/gateway/sukses', [AnggotaIuran::class,'gatewaySuccess'])->name('iuran.gateway.success');
        Route::get('iuran/pembayaran/{pembayaran}/gateway/batal', [AnggotaIuran::class,'gatewayCancel'])->name('iuran.gateway.cancel');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Webhook Xendit (tanpa auth, disable CSRF)
Route::post('/webhook/xendit', XenditWebhookController::class)
    ->name('webhook.xendit')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

require __DIR__.'/auth.php';
