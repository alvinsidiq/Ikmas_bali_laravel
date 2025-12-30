<?php
namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\LaporanAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $st = $request->get('status'); // open|in_progress|resolved|rejected
        $jenis = $request->get('jenis');

        if (!Schema::hasTable('laporans')) {
            $items = new LengthAwarePaginator([], 0, 12, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
            return view('anggota.laporan.index', compact('items','q','st','jenis'));
        }

        $items = Laporan::query()
            ->when($q, fn($qr)=>$qr->where(function($x) use ($q){
                $x->where('judul','like',"%$q%")
                  ->orWhere('deskripsi','like',"%$q%")
                  ->orWhere('kode','like',"%$q%");
            }))
            ->when($jenis, function($qr) use ($jenis){
                $map = [
                    'kegiatan' => ['Kegiatan','Laporan Kegiatan'],
                    'pengumuman' => ['Pengumuman','Laporan Pengumuman'],
                ];
                $values = $map[$jenis] ?? [$jenis];
                $qr->whereIn('kategori', (array)$values);
            })
            ->when($st, fn($qr)=>$qr->where('status',$st))
            ->latest('status')
            ->latest('updated_at')
            ->latest('id')
            ->paginate(12)->withQueryString();

        return view('anggota.laporan.index', compact('items','q','st','jenis'));
    }

    public function show(Laporan $laporan)
    {
        $laporan->load(['attachments','comments.user']);
        return view('anggota.laporan.show', compact('laporan'));
    }

    public function downloadAttachment(Laporan $laporan, LaporanAttachment $attachment)
    {
        abort_unless($attachment->laporan_id === $laporan->id, 404);
        abort_unless(Storage::disk('public')->exists($attachment->file_path), 404);
        $name = $attachment->file_name ?: ('lampiran-'.$attachment->id);
        return Storage::disk('public')->download($attachment->file_path, $name, [ 'Content-Type' => $attachment->file_mime ?? 'application/octet-stream' ]);
    }
}
