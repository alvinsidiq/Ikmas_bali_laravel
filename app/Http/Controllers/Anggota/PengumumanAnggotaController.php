<?php
namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class PengumumanAnggotaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $kat = $request->get('kat');
        $pinnedOnly = $request->boolean('pinned');
        $unreadOnly = $request->boolean('unread');
        $categories = Pengumuman::CATEGORY_OPTIONS;

        $user = Auth::user();
        $hasPivot = Schema::hasTable('pengumuman_user');
        $readIds = $hasPivot ? $user->pengumumanTerbaca()->pluck('pengumumen.id')->toArray() : [];

        $items = Pengumuman::query()
            ->where('is_published', true)
            ->when($q, function($qr) use ($q){
                $qr->where(function($x) use ($q){
                    $x->where('judul','like',"%$q%")
                      ->orWhere('kategori','like',"%$q%")
                      ->orWhere('isi','like',"%$q%");
                });
            })
            ->when($kat, fn($qr)=>$qr->where('kategori',$kat))
            ->when($pinnedOnly, fn($qr)=>$qr->where('is_pinned',true))
            ->when($unreadOnly && $hasPivot, fn($qr)=>$qr->whereNotIn('id', $readIds ?: [0]))
            ->latest('is_pinned')
            ->latest('published_at')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('anggota.pengumuman.index', compact('items','q','kat','pinnedOnly','unreadOnly','readIds','categories'));
    }

    public function show(Pengumuman $pengumuman)
    {
        abort_unless($pengumuman->is_published, 404);

        if (Schema::hasTable('pengumuman_user')) {
            $user = Auth::user();
            $user->pengumumanTerbaca()->syncWithoutDetaching([
                $pengumuman->id => ['read_at' => now()],
            ]);
        }

        return view('anggota.pengumuman.show', compact('pengumuman'));
    }

    public function markAllRead(Request $request)
    {
        if (!Schema::hasTable('pengumuman_user')) {
            return back()->with('error','Fitur penanda baca belum siap. Jalankan migrasi terlebih dahulu.');
        }
        $user = Auth::user();
        $ids = Pengumuman::where('is_published', true)->pluck('id')->all();
        if ($ids) {
            $attach = [];
            $now = now();
            foreach ($ids as $id) { $attach[$id] = ['read_at' => $now]; }
            $user->pengumumanTerbaca()->syncWithoutDetaching($attach);
        }
        return back()->with('success','Semua pengumuman ditandai terbaca.');
    }
}
