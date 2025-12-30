<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumTopic;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumPostController extends Controller
{
    public function store(Request $request, ForumTopic $forum)
    {
        $data = $request->validate(['content' => ['required','string']]);
        $p = ForumPost::create([
            'topic_id' => $forum->id,
            'user_id' => Auth::id(),
            'content' => $data['content'],
        ]);
        $forum->increment('posts_count');
        $forum->last_post_at = now();
        $forum->save();
        return back()->with('success','Balasan ditambahkan');
    }

    public function update(Request $request, ForumTopic $forum, ForumPost $post)
    {
        $data = $request->validate(['content' => ['required','string']]);
        $post->update(['content' => $data['content']]);
        return back()->with('success','Post diperbarui');
    }

    public function destroy(ForumTopic $forum, ForumPost $post)
    {
        $post->delete();
        $forum->decrement('posts_count');
        return back()->with('success','Post dihapus');
    }

    public function markSolution(ForumTopic $forum, ForumPost $post)
    {
        // Clear sebelumnya
        ForumPost::where('topic_id',$forum->id)->update(['is_solution'=>false]);
        // Tandai solusi
        $post->is_solution = true; $post->save();
        $forum->is_solved = true; $forum->solved_post_id = $post->id; $forum->save();
        return back()->with('success','Ditandai sebagai solusi');
    }
}
