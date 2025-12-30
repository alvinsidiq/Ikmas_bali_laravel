<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumTopicController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $kat = $request->get('kat');
        $status = $request->get('status'); // open|closed|pinned|solved|null

        $topics = ForumTopic::query()
            ->when($q, fn($qr)=>$qr->where('judul','like',"%$q%")
                                   ->orWhere('body','like',"%$q%"))
            ->when($kat, fn($qr)=>$qr->where('kategori',$kat))
            ->when($status === 'open', fn($qr)=>$qr->where('is_open',true))
            ->when($status === 'closed', fn($qr)=>$qr->where('is_open',false))
            ->when($status === 'pinned', fn($qr)=>$qr->where('is_pinned',true))
            ->when($status === 'solved', fn($qr)=>$qr->where('is_solved',true))
            ->latest('is_pinned')
            ->latest('last_post_at')
            ->latest('id')
            ->paginate(12)->withQueryString();

        return view('admin.forum.index', compact('topics','q','kat','status'));
    }

    public function create(){ return view('admin.forum.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul' => ['required','string','max:255'],
            'kategori' => ['nullable','string','max:100'],
            'banner_url' => ['nullable','url','max:500'],
            'body' => ['nullable','string'],
            'is_pinned' => ['nullable','boolean'],
        ]);

        $t = new ForumTopic($data);
        $t->author_id = Auth::id();
        $t->is_open = true;
        $t->last_post_at = now();
        $t->save();

        // Jika ada body pembuka, simpan juga sebagai post pertama oleh admin
        if (!empty($data['body'])) {
            ForumPost::create([
                'topic_id' => $t->id,
                'user_id' => Auth::id(),
                'content' => $data['body'],
            ]);
            $t->increment('posts_count');
        }

        return redirect()->route('admin.forum.index')->with('success','Topik forum dibuat');
    }

    public function show(ForumTopic $forum)
    {
        $forum->load(['posts.user']);
        return view('admin.forum.show', ['topic' => $forum]);
    }

    public function edit(ForumTopic $forum)
    { return view('admin.forum.edit', ['topic' => $forum]); }

    public function update(Request $request, ForumTopic $forum)
    {
        $data = $request->validate([
            'judul' => ['required','string','max:255'],
            'kategori' => ['nullable','string','max:100'],
            'banner_url' => ['nullable','url','max:500'],
            'body' => ['nullable','string'],
            'is_pinned' => ['nullable','boolean'],
        ]);
        $forum->fill($data);
        $forum->save();
        return redirect()->route('admin.forum.index')->with('success','Topik diperbarui');
    }

    public function destroy(ForumTopic $forum)
    {
        // Menghapus topik sekaligus posts (via FK cascade)
        $forum->delete();
        return redirect()->route('admin.forum.index')->with('success','Topik dihapus');
    }

    // Aksi khusus
    public function toggleOpen(ForumTopic $forum)
    {
        $forum->is_open = !$forum->is_open; $forum->save();
        return back()->with('success', 'Status open/closed diperbarui');
    }

    public function togglePin(ForumTopic $forum)
    {
        $forum->is_pinned = !$forum->is_pinned; $forum->save();
        return back()->with('success', 'Status pin diperbarui');
    }

    public function unmarkSolved(ForumTopic $forum)
    {
        $forum->is_solved = false; $forum->solved_post_id = null; $forum->save();
        ForumPost::where('topic_id',$forum->id)->update(['is_solution'=>false]);
        return back()->with('success','Solved dihapus');
    }
}
