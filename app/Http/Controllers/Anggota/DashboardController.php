<?php
namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Arsip;
use App\Models\DokumentasiAlbum;
use App\Models\ForumTopic;
use App\Models\IuranPembayaran;
use App\Models\IuranTagihan;
use App\Models\Kegiatan;
use App\Models\Laporan;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index() {
        $user = auth()->user();

        $hasKegiatan = Schema::hasTable('kegiatans');
        $hasPengumuman = Schema::hasTable('pengumumen');
        $hasForum = Schema::hasTable('forum_topics');
        $hasDokumentasi = Schema::hasTable('dokumentasi_albums');
        $hasArsip = Schema::hasTable('arsips');
        $hasIuran = Schema::hasTable('iuran_tagihans');
        $hasPembayaran = Schema::hasTable('iuran_pembayarans');
        $hasLaporan = Schema::hasTable('laporans');

        $stats = [
            'kegiatan' => $hasKegiatan ? Kegiatan::published()->count() : 0,
            'pengumuman' => $hasPengumuman ? Pengumuman::published()->count() : 0,
            'forum' => $hasForum ? ForumTopic::count() : 0,
            'arsip' => $hasArsip ? Arsip::published()->count() : 0,
            'album' => $hasDokumentasi ? DokumentasiAlbum::published()->count() : 0,
            'laporan' => $hasLaporan ? Laporan::count() : 0,
        ];

        $iuran = [
            'outstanding_total' => $hasIuran
                ? IuranTagihan::where('user_id', $user->id)
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->get()
                    ->sum->sisa_bayar
                : 0,
            'unpaid_count' => $hasIuran
                ? IuranTagihan::where('user_id', $user->id)
                    ->whereIn('status', ['unpaid', 'partial'])
                    ->count()
                : 0,
            'pending_payments' => $hasIuran
                && $hasPembayaran
                ? IuranPembayaran::where('user_id', $user->id)
                    ->whereIn('status', ['submitted', 'pending_gateway'])
                    ->count()
                : 0,
            'last_payment' => $hasIuran
                && $hasPembayaran
                ? IuranPembayaran::where('user_id', $user->id)
                    ->latest('paid_at')
                    ->latest('id')
                    ->first()
                : null,
        ];

        return view('anggota.dashboard', compact('stats', 'iuran'));
    }
}
