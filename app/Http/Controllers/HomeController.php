<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\DokumentasiAlbum;
use App\Models\ForumTopic;
use App\Models\IuranTagihan;
use App\Models\Kegiatan;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'hasRole')) {
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('bendahara')) {
                return redirect()->route('bendahara.dashboard');
            }
        }

        $hasPengumuman = Schema::hasTable('pengumumen');
        $hasKegiatan = Schema::hasTable('kegiatans');
        $hasForum = Schema::hasTable('forum_topics');
        $hasDokumentasi = Schema::hasTable('dokumentasi_albums');
        $hasArsip = Schema::hasTable('arsips');
        $hasIuran = Schema::hasTable('iuran_tagihans');
        $hasUsers = Schema::hasTable('users');
        $hasRoles = Schema::hasTable('model_has_roles');

        $stats = [
            'Anggota Aktif' => $hasUsers
                ? ($hasRoles ? User::role('anggota')->count() : User::count())
                : 0,
            'Kegiatan Terjadwal' => $hasKegiatan ? Kegiatan::published()->count() : 0,
            'Pengumuman Terbit' => $hasPengumuman ? Pengumuman::published()->count() : 0,
            'Tagihan Terbuka' => $hasIuran ? IuranTagihan::whereIn('status', ['unpaid', 'partial'])->count() : 0,
        ];

        $pengumuman = $hasPengumuman
            ? Pengumuman::published()
                ->with('author')
                ->latest('is_pinned')
                ->latest('published_at')
                ->latest('id')
                ->take(3)
                ->get()
            : collect();

        $kegiatan = $hasKegiatan
            ? Kegiatan::published()
                ->orderBy('waktu_mulai')
                ->orderBy('id')
                ->take(3)
                ->get()
            : collect();

        $forumTopics = $hasForum
            ? ForumTopic::query()
                ->with('author')
                ->orderByDesc('is_pinned')
                ->orderByDesc('last_post_at')
                ->latest('id')
                ->take(3)
                ->get()
            : collect();

        $albums = $hasDokumentasi
            ? DokumentasiAlbum::published()
                ->latest('published_at')
                ->latest('id')
                ->take(3)
                ->get()
            : collect();

        $arsip = $hasArsip
            ? Arsip::published()
                ->latest('published_at')
                ->latest('id')
                ->take(3)
                ->get()
            : collect();

        $dueTagihan = ($hasIuran && $user)
            ? IuranTagihan::where('user_id', $user->id)
                ->whereIn('status', ['unpaid', 'partial'])
                ->orderBy('jatuh_tempo')
                ->limit(4)
                ->get()
            : collect();

        $iuranOverview = $hasIuran
            ? [
                'total' => IuranTagihan::count(),
                'paid' => IuranTagihan::where('status', 'paid')->count(),
                'open' => IuranTagihan::whereIn('status', ['unpaid', 'partial'])->count(),
            ]
            : ['total' => 0, 'paid' => 0, 'open' => 0];

        return view('home', [
            'user' => $user,
            'stats' => $stats,
            'pengumuman' => $pengumuman,
            'kegiatan' => $kegiatan,
            'forumTopics' => $forumTopics,
            'albums' => $albums,
            'arsip' => $arsip,
            'dueTagihan' => $dueTagihan,
            'iuranOverview' => $iuranOverview,
        ]);
    }
}
