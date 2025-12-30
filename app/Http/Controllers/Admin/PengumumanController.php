<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePengumumanRequest;
use App\Http\Requests\Admin\UpdatePengumumanRequest;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Support\MediaPath;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $status = $request->get('status'); // published|draft|null
        $pinned = $request->get('pinned'); // 1|null
        $kat = $request->get('kat'); // kategori

        $items = Pengumuman::query()
            ->when($q, function($qr) use ($q){
                $qr->where('judul','like',"%$q%")
                   ->orWhere('kategori','like',"%$q%")
                   ->orWhere('isi','like',"%$q%");
            })
            ->when($status === 'published', fn($qr)=>$qr->where('is_published',true))
            ->when($status === 'draft', fn($qr)=>$qr->where('is_published',false))
            ->when($pinned === '1', fn($qr)=>$qr->where('is_pinned',true))
            ->when($kat, fn($qr)=>$qr->where('kategori',$kat))
            ->latest('published_at')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.pengumuman.index', compact('items','q','status','pinned','kat'));
    }

    public function create()
    { return view('admin.pengumuman.create'); }

    public function store(StorePengumumanRequest $request)
    {
        $data = $request->validated();
        $m = new Pengumuman();
        $m->fill($data);
        $m->author_id = Auth::id();
        if ($request->boolean('is_published')) { $m->is_published = true; $m->published_at = now(); }
        $m->is_pinned = $request->boolean('is_pinned');
        if ($request->hasFile('cover')) { $m->cover_path = $request->file('cover')->store('pengumuman/covers','public'); }
        $m->save();

        return redirect()->route('admin.pengumuman.index')->with('success','Pengumuman berhasil dibuat');
    }

    public function show(Pengumuman $pengumuman)
    { return view('admin.pengumuman.show', compact('pengumuman')); }

    public function edit(Pengumuman $pengumuman)
    { return view('admin.pengumuman.edit', compact('pengumuman')); }

    public function update(UpdatePengumumanRequest $request, Pengumuman $pengumuman)
    {
        $data = $request->validated();
        $pengumuman->fill($data);
        $newPub = $request->boolean('is_published');
        if ($newPub && !$pengumuman->is_published) { $pengumuman->is_published = true; $pengumuman->published_at = now(); }
        if (!$newPub && $pengumuman->is_published) { $pengumuman->is_published = false; $pengumuman->published_at = null; }
        $pengumuman->is_pinned = $request->boolean('is_pinned');
        if ($request->hasFile('cover')) {
            MediaPath::deleteIfLocal($pengumuman->cover_path);
            $pengumuman->cover_path = $request->file('cover')->store('pengumuman/covers','public');
        }
        $pengumuman->save();

        return redirect()->route('admin.pengumuman.index')->with('success','Pengumuman diperbarui');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        MediaPath::deleteIfLocal($pengumuman->cover_path);
        $pengumuman->delete();
        return redirect()->route('admin.pengumuman.index')->with('success','Pengumuman dihapus');
    }

    public function togglePublish(Pengumuman $pengumuman)
    {
        $pengumuman->is_published = !$pengumuman->is_published;
        $pengumuman->published_at = $pengumuman->is_published ? now() : null;
        $pengumuman->save();
        return redirect()->back()->with('success','Status publish diperbarui');
    }

    public function togglePin(Pengumuman $pengumuman)
    {
        $pengumuman->is_pinned = !$pengumuman->is_pinned;
        $pengumuman->save();
        return redirect()->back()->with('success','Status pin diperbarui');
    }

    public function removeCover(Pengumuman $pengumuman)
    {
        MediaPath::deleteIfLocal($pengumuman->cover_path);
        $pengumuman->cover_path = null; $pengumuman->save();
        return redirect()->back()->with('success','Cover dihapus');
    }

    public function sendEmail(Pengumuman $pengumuman)
    {
        if (!$pengumuman->is_published) {
            return back()->with('error','Pengumuman harus dipublish sebelum dikirim.');
        }

        $sent = 0;
        $subject = '[Pengumuman] '.$pengumuman->judul;
        $url = route('anggota.pengumuman.show', $pengumuman->slug);
        $body = "Halo anggota,\n\nAda pengumuman baru:\n{$pengumuman->judul}\n\n".strip_tags($pengumuman->isi)."\n\nLihat selengkapnya: {$url}";

        User::role('anggota')
            ->select('id','email','name')
            ->whereNotNull('email')
            ->chunkById(100, function($users) use ($subject, $body, &$sent){
                $emails = $users->pluck('email')->filter()->values()->all();
                if (empty($emails)) { return; }
                $to = array_shift($emails);
                Mail::mailer('smtp')->raw($body, function($m) use ($subject, $to, $emails){
                    $m->to($to);
                    if (!empty($emails)) { $m->bcc($emails); }
                    $m->subject($subject);
                });
                $sent += 1 + count($emails);
            });

        return back()->with('success',"Email pengumuman dikirim ke {$sent} anggota.");
    }
}
