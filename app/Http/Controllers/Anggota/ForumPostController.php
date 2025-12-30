<?php
namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Http\Requests\Anggota\StoreForumPostRequest;
use App\Http\Requests\Anggota\UpdateForumPostRequest;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use Illuminate\Support\Facades\Auth;

class ForumPostController extends Controller
{
    public function store(StoreForumPostRequest $request, ForumTopic $forum)
    {
        if (!$forum->is_open) return back()->with('error','Topik ditutup.');
        $data = $request->validated();
        $p = ForumPost::create([
            'topic_id' => $forum->id,
            'user_id' => Auth::id(),
            'content' => $data['content'],
        ]);
        $forum->increment('posts_count');
        $forum->last_post_at = now(); $forum->save();
        return back()->with('success','Balasan terkirim');
    }

    public function update(UpdateForumPostRequest $request, ForumTopic $forum, ForumPost $post)
    {
        abort_unless($post->topic_id === $forum->id, 404);
        abort_unless($post->user_id === Auth::id(), 403);
        $post->update(['content' => $request->validated()['content']]);
        return back()->with('success','Post diperbarui');
    }

    public function destroy(ForumTopic $forum, ForumPost $post)
    {
        abort_unless($post->topic_id === $forum->id, 404);
        abort_unless($post->user_id === Auth::id(), 403);
        $post->delete();
        $forum->decrement('posts_count');
        return back()->with('success','Post dihapus');
    }

    public function markSolution(ForumTopic $forum, ForumPost $post)
    {
        abort_unless($post->topic_id === $forum->id, 404);
        // Hanya pemilik topik
        abort_unless($forum->author_id === Auth::id(), 403);
        // Reset & set solusi
        ForumPost::where('topic_id',$forum->id)->update(['is_solution'=>false]);
        $post->is_solution = true; $post->save();
        $forum->is_solved = true; $forum->solved_post_id = $post->id; $forum->save();
        return back()->with('success','Ditandai sebagai solusi');
    }
}

