<?php
namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\DokumentasiAlbum;
use App\Models\DokumentasiMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumentasiAnggotaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $tahun = $request->get('tahun');
        $lokasi = $request->get('lokasi');
        $tag = $request->get('tag');

        $albums = DokumentasiAlbum::query()
            ->where('is_published', true)
            ->when($q, fn($qr)=>$qr->where(function($x) use ($q){
                $x->where('judul','like',"%$q%")
                  ->orWhere('deskripsi','like',"%$q%")
                  ->orWhere('tags','like',"%$q%")
                  ->orWhere('lokasi','like',"%$q%");
            }))
            ->when($tahun, fn($qr)=>$qr->whereYear('tanggal_kegiatan', $tahun))
            ->when($lokasi, fn($qr)=>$qr->where('lokasi', $lokasi))
            ->when($tag, fn($qr)=>$qr->where('tags','like',"%$tag%"))
            ->latest('published_at')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('anggota.dokumentasi.index', compact('albums','q','tahun','lokasi','tag'));
    }

    public function show(DokumentasiAlbum $album)
    {
        abort_unless($album->is_published, 404);
        $album->load(['medias' => fn($q)=>$q->orderBy('sort_order')->orderBy('id')]);
        $album->increment('view_count');
        return view('anggota.dokumentasi.show', compact('album'));
    }

    public function mediaDownload(DokumentasiAlbum $album, DokumentasiMedia $media)
    {
        abort_unless($album->is_published && $media->album_id === $album->id, 404);
        abort_unless($media->media_path && Storage::disk('public')->exists($media->media_path), 404);
        return Storage::disk('public')->download($media->media_path, basename($media->media_path), [
            'Content-Type' => $media->mime ?? 'application/octet-stream'
        ]);
    }
}

