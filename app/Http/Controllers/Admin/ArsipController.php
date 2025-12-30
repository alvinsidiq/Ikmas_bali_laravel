<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArsipRequest;
use App\Http\Requests\Admin\UpdateArsipRequest;
use App\Models\Arsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $kat = $request->get('kat');
        $th = $request->get('tahun');
        $status = $request->get('status'); // published|draft|null
        $tag = $request->get('tag');

        $items = Arsip::query()
            ->when($q, function($qr) use ($q){
                $qr->where('judul','like',"%$q%")
                   ->orWhere('nomor_dokumen','like',"%$q%")
                   ->orWhere('ringkasan','like',"%$q%");
            })
            ->when($kat, fn($qr)=>$qr->where('kategori',$kat))
            ->when($th, fn($qr)=>$qr->where('tahun',$th))
            ->when($status === 'published', fn($qr)=>$qr->where('is_published',true))
            ->when($status === 'draft', fn($qr)=>$qr->where('is_published',false))
            ->when($tag, fn($qr)=>$qr->where('tags','like',"%$tag%"));
            // ->latest('published_at') // Removed as it might not be present for all items
            // ->latest('id') // Removed as it might not be present for all items
            $items = $items->latest('id')
            ->paginate(15)->withQueryString();

        return view('admin.arsip.index', compact('items','q','kat','th','status','tag'));
    }

    public function create(){ return view('admin.arsip.create'); }

    public function store(StoreArsipRequest $request)
    {
        $data = $request->validated();
        $m = new Arsip($data);
        $m->uploaded_by = Auth::id();
        if ($request->boolean('is_published')) { $m->is_published = true; $m->published_at = now(); }

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('arsip/files','public');
            $m->file_path = $path;
            $m->file_name = $request->file('file')->getClientOriginalName();
            $m->file_mime = $request->file('file')->getClientMimeType();
            $m->file_size = $request->file('file')->getSize();
        }
        $m->save();

        return redirect()->route('admin.arsip.index')->with('success','Arsip berhasil ditambahkan');
    }

    public function show(Arsip $arsip)
    {
        return view('admin.arsip.show', compact('arsip'));
    }

    public function edit(Arsip $arsip)
    {
        return view('admin.arsip.edit', compact('arsip'));
    }

    public function update(UpdateArsipRequest $request, Arsip $arsip)
    {
        $data = $request->validated();
        $arsip->fill($data);

        $newPub = $request->boolean('is_published');
        if ($newPub && !$arsip->is_published) { $arsip->is_published = true; $arsip->published_at = now(); }
        if (!$newPub && $arsip->is_published) { $arsip->is_published = false; $arsip->published_at = null; }

        if ($request->hasFile('file')) {
            if ($arsip->file_path) Storage::disk('public')->delete($arsip->file_path);
            $path = $request->file('file')->store('arsip/files','public');
            $arsip->file_path = $path;
            $arsip->file_name = $request->file('file')->getClientOriginalName();
            $arsip->file_mime = $request->file('file')->getClientMimeType();
            $arsip->file_size = $request->file('file')->getSize();
        }

        $arsip->save();
        return redirect()->route('admin.arsip.index')->with('success','Arsip diperbarui');
    }

    public function destroy(Arsip $arsip)
    {
        if ($arsip->file_path) Storage::disk('public')->delete($arsip->file_path);
        $arsip->delete();
        return redirect()->route('admin.arsip.index')->with('success','Arsip dihapus');
    }

    public function togglePublish(Arsip $arsip)
    {
        $arsip->is_published = !$arsip->is_published;
        $arsip->published_at = $arsip->is_published ? now() : null;
        $arsip->save();
        return back()->with('success','Status publish diperbarui');
    }

    public function removeFile(Arsip $arsip)
    {
        if ($arsip->file_path) {
            Storage::disk('public')->delete($arsip->file_path);
            $arsip->file_path = null; $arsip->file_name = null; $arsip->file_mime = null; $arsip->file_size = null; $arsip->save();
        }
        return back()->with('success','File dihapus');
    }

    public function download(Arsip $arsip)
    {
        abort_unless($arsip->file_path && Storage::disk('public')->exists($arsip->file_path), 404);
        $name = $arsip->file_name ?: ('arsip-'.$arsip->id);
        return Storage::disk('public')->download($arsip->file_path, $name, [ 'Content-Type' => $arsip->file_mime ?? 'application/octet-stream' ]);
    }
}
