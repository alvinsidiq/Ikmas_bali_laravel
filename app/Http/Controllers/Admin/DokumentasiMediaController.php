<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDokumentasiMediaRequest;
use App\Http\Requests\Admin\UpdateDokumentasiMediaRequest;
use App\Models\DokumentasiAlbum;
use App\Models\DokumentasiMedia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Support\MediaPath;

class DokumentasiMediaController extends Controller
{
    public function store(StoreDokumentasiMediaRequest $request, DokumentasiAlbum $album)
    {
        foreach ($request->file('media') as $file) {
            $path = $file->store('dokumentasi/media','public');
            $m = DokumentasiMedia::create([
                'album_id' => $album->id,
                'media_path' => $path,
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
                'sort_order' => $album->medias()->max('sort_order') + 1,
            ]);
            $album->increment('media_count');
            if (!$album->cover_path) { // set cover pertama kali
                $m->is_cover = true; $m->save();
                $album->cover_path = $m->media_path; $album->save();
            }
        }
        return back()->with('success','Media berhasil diunggah');
    }

    public function update(UpdateDokumentasiMediaRequest $request, DokumentasiAlbum $album, DokumentasiMedia $media)
    {
        abort_unless($media->album_id === $album->id, 404);
        $data = $request->validated();
        $media->update($data);
        return back()->with('success','Media diperbarui');
    }

    public function destroy(DokumentasiAlbum $album, DokumentasiMedia $media)
    {
        abort_unless($media->album_id === $album->id, 404);
        MediaPath::deleteIfLocal($media->media_path);
        $media->delete();
        $album->decrement('media_count');
        if ($media->is_cover) { $album->cover_path = null; $album->save(); }
        return back()->with('success','Media dihapus');
    }

    public function download(DokumentasiAlbum $album, DokumentasiMedia $media)
    {
        abort_unless($media->album_id === $album->id, 404);
        abort_unless(Storage::disk('public')->exists($media->media_path), 404);
        return Storage::disk('public')->download($media->media_path, basename($media->media_path), [ 'Content-Type' => $media->mime ?? 'application/octet-stream' ]);
    }
}
