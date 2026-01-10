<?php
namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Http\Requests\Anggota\StoreForumTopicRequest;
use App\Http\Requests\Anggota\UpdateForumTopicRequest;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use App\Support\MediaPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumTopicController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $kat = $request->get('kat');
        $status = $request->get('status'); // open|closed|pinned|solved|null
        $mine = $request->boolean('mine');

        $topics = ForumTopic::query()
            ->when($q, fn($qr)=>$qr->where('judul','like',"%$q%")
                                   ->orWhere('body','like',"%$q%"))
            ->when($kat, fn($qr)=>$qr->where('kategori',$kat))
            ->when($status === 'open', fn($qr)=>$qr->where('is_open',true))
            ->when($status === 'closed', fn($qr)=>$qr->where('is_open',false))
            ->when($status === 'pinned', fn($qr)=>$qr->where('is_pinned',true))
            ->when($status === 'solved', fn($qr)=>$qr->where('is_solved',true))
            ->when($mine, fn($qr)=>$qr->where('author_id', Auth::id()))
            ->latest('is_pinned')
            ->latest('last_post_at')
            ->latest('id')
            ->paginate(12)->withQueryString();

        return view('anggota.forum.index', compact('topics','q','kat','status','mine'));
    }

    public function create(){ return view('anggota.forum.create'); }

    public function store(StoreForumTopicRequest $request)
    {
        $data = $request->validated();
        $banner = $request->file('banner');
        unset($data['banner']);
        $t = new ForumTopic($data);
        $t->author_id = Auth::id();
        $t->is_open = true; $t->is_pinned = false; $t->last_post_at = now();
        if ($banner) {
            $t->banner_url = $banner->store('forum/banners','public');
        }
        $t->save();

        if (!empty($data['body'])) {
            ForumPost::create([
                'topic_id' => $t->id,
                'user_id' => Auth::id(),
                'content' => $data['body'],
            ]);
            $t->increment('posts_count');
        }

        return redirect()->route('anggota.forum.show',$t)->with('success','Topik dibuat');
    }

    public function show(ForumTopic $forum)
    {
        $forum->load(['posts.user']);
        return view('anggota.forum.show', ['topic' => $forum]);
    }

    public function edit(ForumTopic $forum)
    {
        abort_unless($forum->author_id === Auth::id(), 403);
        return view('anggota.forum.edit', ['topic' => $forum]);
    }

    public function update(UpdateForumTopicRequest $request, ForumTopic $forum)
    {
        abort_unless($forum->author_id === Auth::id(), 403);
        $data = $request->validated();
        $banner = $request->file('banner');
        unset($data['banner']);
        $forum->fill($data);
        if ($banner) {
            MediaPath::deleteIfLocal($forum->banner_url);
            $forum->banner_url = $banner->store('forum/banners','public');
        }
        $forum->save();
        return redirect()->route('anggota.forum.show',$forum)->with('success','Topik diperbarui');
    }

    public function destroy(ForumTopic $forum)
    {
        abort_unless($forum->author_id === Auth::id(), 403);
        $forum->delete();
        return redirect()->route('anggota.forum.index')->with('success','Topik dihapus');
    }

    public function toggleOpenSelf(ForumTopic $forum)
    {
        abort_unless($forum->author_id === Auth::id(), 403);
        $forum->is_open = !$forum->is_open; $forum->save();
        return back()->with('success','Status open/closed diperbarui');
    }
}
