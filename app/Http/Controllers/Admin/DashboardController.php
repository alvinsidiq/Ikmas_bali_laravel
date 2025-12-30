<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Arsip;
use App\Models\DokumentasiAlbum;
use App\Models\DokumentasiMedia;
use App\Models\Kegiatan;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index() {
        $stats = [
            'anggota_total' => 0,
            'anggota_aktif' => 0,
            'kegiatan_published' => 0,
            'kegiatan_upcoming' => 0,
            'pengumuman_published' => 0,
            'arsip_published' => 0,
            'album_published' => 0,
            'media_total' => 0,
        ];

        if (Schema::hasTable('users')) {
            $stats['anggota_total'] = User::role('anggota')->count();
        }
        if (Schema::hasTable('anggota_profiles')) {
            $stats['anggota_aktif'] = User::role('anggota')->whereHas('profile', fn($p)=>$p->where('is_active', true))->count();
        }
        if (Schema::hasTable('kegiatans')) {
            $stats['kegiatan_published'] = Kegiatan::published()->count();
            $stats['kegiatan_upcoming'] = Kegiatan::published()->upcoming()->count();
        }
        if (Schema::hasTable('pengumumen')) {
            $stats['pengumuman_published'] = Pengumuman::published()->count();
        }
        if (Schema::hasTable('arsips')) {
            $stats['arsip_published'] = Arsip::published()->count();
        }
        if (Schema::hasTable('dokumentasi_albums')) {
            $stats['album_published'] = DokumentasiAlbum::published()->count();
        }
        if (Schema::hasTable('dokumentasi_media')) {
            $stats['media_total'] = DokumentasiMedia::count();
        }

        return view('admin.dashboard', compact('stats'));
    }
}
