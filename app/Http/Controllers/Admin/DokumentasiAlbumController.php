<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDokumentasiAlbumRequest;
use App\Http\Requests\Admin\UpdateDokumentasiAlbumRequest;
use App\Models\DokumentasiAlbum;
use App\Models\DokumentasiMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\MediaPath;

class DokumentasiAlbumController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $status = $request->get('status'); // published|draft|null
        $tahun = $request->get('tahun');

        $items = DokumentasiAlbum::query()
            ->when($q, fn($qr)=>$qr->where('judul','like',"%$q%")
                                   ->orWhere('lokasi','like',"%$q%")
                                   ->orWhere('deskripsi','like',"%$q%")
                                   ->orWhere('tags','like',"%$q%"))
            ->when($status === 'published', fn($qr)=>$qr->where('is_published',true))
            ->when($status === 'draft', fn($qr)=>$qr->where('is_published',false))
            ->when($tahun, fn($qr)=>$qr->whereYear('tanggal_kegiatan',$tahun))
            ->latest('is_published')
            ->latest('tanggal_kegiatan')
            ->latest('id')
            ->paginate(12)->withQueryString();

        return view('admin.dokumentasi.albums.index', compact('items','q','status','tahun'));
    }

    public function create(){ return view('admin.dokumentasi.albums.create'); }

    public function store(StoreDokumentasiAlbumRequest $request)
    {
        $data = $request->validated();
        $a = new DokumentasiAlbum($data);
        $a->created_by = Auth::id();
        if ($request->boolean('is_published')) { $a->is_published = true; $a->published_at = now(); }
        $a->save();
        return redirect()->route('admin.dokumentasi.albums.edit', $a)->with('success','Album dibuat. Silakan unggah media.');
    }

    public function show(DokumentasiAlbum $album)
    {
        $album->load('medias');
        return view('admin.dokumentasi.albums.show', compact('album'));
    }

    public function edit(DokumentasiAlbum $album)
    {
        $album->load('medias');
        return view('admin.dokumentasi.albums.edit', compact('album'));
    }

    public function update(UpdateDokumentasiAlbumRequest $request, DokumentasiAlbum $album)
    {
        $data = $request->validated();
        $album->fill($data);
        $newPub = $request->boolean('is_published');
        if ($newPub && !$album->is_published) { $album->is_published = true; $album->published_at = now(); }
        if (!$newPub && $album->is_published) { $album->is_published = false; $album->published_at = null; }
        $album->save();
        return redirect()->route('admin.dokumentasi.albums.edit',$album)->with('success','Album diperbarui');
    }

    public function destroy(DokumentasiAlbum $album)
    {
        // Hapus file media & cover
        foreach ($album->medias as $m) { MediaPath::deleteIfLocal($m->media_path); }
        MediaPath::deleteIfLocal($album->cover_path);
        $album->delete();
        return redirect()->route('admin.dokumentasi.albums.index')->with('success','Album dihapus');
    }

    public function togglePublish(DokumentasiAlbum $album)
    {
        $album->is_published = !$album->is_published;
        $album->published_at = $album->is_published ? now() : null;
        $album->save();
        return back()->with('success','Status publish diperbarui');
    }

    public function setCover(DokumentasiAlbum $album, DokumentasiMedia $media)
    {
        abort_unless($media->album_id === $album->id, 404);
        // reset flag & set cover
        DokumentasiMedia::where('album_id',$album->id)->update(['is_cover'=>false]);
        $media->is_cover = true; $media->save();
        $album->cover_path = $media->media_path; $album->save();
        return back()->with('success','Cover album diperbarui');
    }

    public function removeCover(DokumentasiAlbum $album)
    {
        $album->cover_path = null; $album->save();
        DokumentasiMedia::where('album_id',$album->id)->update(['is_cover'=>false]);
        return back()->with('success','Cover dihapus');
    }
}
