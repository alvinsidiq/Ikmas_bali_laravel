<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKegiatanRequest;
use App\Http\Requests\Admin\UpdateKegiatanRequest;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Support\MediaPath;

class KegiatanController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $status = $request->get('status'); // published|unpublished|null
        $w = $request->get('w'); // upcoming|past|null

        $items = Kegiatan::query()
            ->when($q, function($qr) use ($q){
                $qr->where('judul','like',"%$q%")
                   ->orWhere('lokasi','like',"%$q%")
                   ->orWhere('deskripsi','like',"%$q%");
            })
            ->when($status, function($qr) use ($status){
                $qr->where('is_published', $status === 'published');
            })
            ->when($w === 'upcoming', fn($qr)=>$qr->upcoming())
            ->when($w === 'past', fn($qr)=>$qr->past())
            ->latest('waktu_mulai')
            ->paginate(12)
            ->withQueryString();

        return view('admin.kegiatan.index', compact('items','q','status','w'));
    }

    public function create()
    { return view('admin.kegiatan.create'); }

    public function store(StoreKegiatanRequest $request)
    {
        $data = $request->validated();
        $k = new Kegiatan();
        $k->fill($data);
        $k->created_by = Auth::id();
        if ($request->boolean('is_published')) {
            $k->is_published = true;
            $k->published_at = now();
        }
        if ($request->hasFile('poster')) {
            $k->poster_path = $request->file('poster')->store('kegiatan/posters','public');
        }
        $k->save();
        return redirect()->route('admin.kegiatan.index')->with('success','Kegiatan berhasil dibuat');
    }

    public function show(Kegiatan $kegiatan)
    { return view('admin.kegiatan.show', compact('kegiatan')); }

    public function edit(Kegiatan $kegiatan)
    { return view('admin.kegiatan.edit', compact('kegiatan')); }

    public function update(UpdateKegiatanRequest $request, Kegiatan $kegiatan)
    {
        $data = $request->validated();
        $kegiatan->fill($data);
        if ($request->boolean('is_published') && !$kegiatan->is_published) {
            $kegiatan->is_published = true; $kegiatan->published_at = now();
        }
        if (!$request->boolean('is_published') && $kegiatan->is_published) {
            $kegiatan->is_published = false; $kegiatan->published_at = null;
        }
        if ($request->hasFile('poster')) {
            MediaPath::deleteIfLocal($kegiatan->poster_path);
            $kegiatan->poster_path = $request->file('poster')->store('kegiatan/posters','public');
        }
        $kegiatan->save();
        return redirect()->route('admin.kegiatan.index')->with('success','Kegiatan diperbarui');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        MediaPath::deleteIfLocal($kegiatan->poster_path);
        $kegiatan->delete();
        return redirect()->route('admin.kegiatan.index')->with('success','Kegiatan dihapus');
    }

    public function togglePublish(Kegiatan $kegiatan)
    {
        $kegiatan->is_published = !$kegiatan->is_published;
        $kegiatan->published_at = $kegiatan->is_published ? now() : null;
        $kegiatan->save();
        return redirect()->back()->with('success','Status publish diperbarui');
    }

    public function removePoster(Kegiatan $kegiatan)
    {
        MediaPath::deleteIfLocal($kegiatan->poster_path);
        $kegiatan->poster_path = null; $kegiatan->save();
        return redirect()->back()->with('success','Poster dihapus');
    }
}
