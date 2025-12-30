<?php
namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Arsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArsipAnggotaController extends Controller
{
    public function index(Request $request)
    {
        $q   = trim((string)$request->get('q'));
        $kat = $request->get('kat');
        $th  = $request->get('tahun');
        $tag = $request->get('tag');

        $items = Arsip::query()
            ->where('is_published', true)
            ->when($q, function($qr) use ($q){
                $qr->where(function($x) use ($q){
                    $x->where('judul','like',"%$q%")
                      ->orWhere('nomor_dokumen','like',"%$q%")
                      ->orWhere('ringkasan','like',"%$q%")
                      ->orWhere('tags','like',"%$q%");
                });
            })
            ->when($kat, fn($qr)=>$qr->where('kategori',$kat))
            ->when($th,  fn($qr)=>$qr->where('tahun',$th))
            ->when($tag, fn($qr)=>$qr->where('tags','like',"%$tag%"))
            ->latest('published_at')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('anggota.arsip.index', compact('items','q','kat','th','tag'));
    }

    public function show(Arsip $arsip)
    {
        abort_unless($arsip->is_published, 404);
        return view('anggota.arsip.show', compact('arsip'));
    }

    public function download(Arsip $arsip)
    {
        abort_unless($arsip->is_published, 404);
        abort_unless($arsip->file_path && Storage::disk('public')->exists($arsip->file_path), 404);
        $name = $arsip->file_name ?: ('arsip-'.$arsip->id);
        return Storage::disk('public')->download($arsip->file_path, $name, [
            'Content-Type' => $arsip->file_mime ?? 'application/octet-stream'
        ]);
    }
}

