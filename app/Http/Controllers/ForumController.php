<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumPost;
use App\Models\ForumPostVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::where('is_active', true)
            ->withCount('threads')
            ->orderBy('order')
            ->get();

        $recentThreads = ForumThread::with(['user:id,first_name,last_name,profile_image', 'category:id,name,color'])
            ->latest('last_reply_at')
            ->limit(10)
            ->get();

        return view('forums.index', compact('categories', 'recentThreads'));
    }

    public function category(ForumCategory $category)
    {
        $threads = $category->threads()
            ->with(['user:id,first_name,last_name,profile_image'])
            ->withCount('posts')
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_reply_at')
            ->paginate(15);

        return view('forums.category', compact('category', 'threads'));
    }

    public function show(ForumThread $thread)
    {
        $thread->incrementViews();
        $thread->load(['user:id,first_name,last_name,profile_image,role', 'category:id,name,color']);

        $posts = $thread->posts()
            ->with([
                'user:id,first_name,last_name,profile_image,role',
                'replies.user:id,first_name,last_name,profile_image,role',
            ])
            ->whereNull('parent_id')
            ->orderByDesc('is_best_answer')
            ->orderByDesc('votes_count')
            ->orderBy('created_at')
            ->paginate(20);

        return view('forums.show', compact('thread', 'posts'));
    }

    public function createThread(ForumCategory $category)
    {
        return view('forums.create-thread', compact('category'));
    }

    public function storeThread(Request $request, ForumCategory $category)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:10',
        ]);

        $thread = ForumThread::create([
            'category_id' => $category->id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'body' => $request->body,
            'last_reply_at' => now(),
        ]);

        return redirect()->route('forums.show', $thread)
            ->with('success', 'Thread created successfully!');
    }

    public function reply(Request $request, ForumThread $thread)
    {
        if ($thread->is_locked) {
            return back()->with('error', 'This thread is locked.');
        }

        $request->validate([
            'body' => 'required|string|min:3',
            'parent_id' => 'nullable|exists:forum_posts,id',
        ]);

        $post = ForumPost::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'body' => $request->body,
        ]);

        $thread->update([
            'replies_count' => $thread->posts()->count(),
            'last_reply_at' => now(),
        ]);

        return back()->with('success', 'Reply posted!');
    }

    public function vote(Request $request, ForumPost $post)
    {
        $request->validate([
            'value' => 'required|in:1,-1',
        ]);

        $userId = Auth::id();
        $value = (int) $request->value;

        $existing = ForumPostVote::where('post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        DB::transaction(function () use ($post, $userId, $value, $existing) {
            if ($existing) {
                if ($existing->value === $value) {
                    // Remove vote (toggle off)
                    $existing->delete();
                } else {
                    // Change vote direction
                    $existing->update(['value' => $value]);
                }
            } else {
                ForumPostVote::create([
                    'post_id' => $post->id,
                    'user_id' => $userId,
                    'value' => $value,
                ]);
            }

            // Recalculate votes_count
            $post->votes_count = $post->votes()->sum('value');
            $post->save();
        });

        if ($request->expectsJson()) {
            return response()->json([
                'votes_count' => $post->votes_count,
                'user_vote' => $post->getUserVote($userId),
            ]);
        }

        return back();
    }

    public function markBestAnswer(ForumThread $thread, ForumPost $post)
    {
        $user = Auth::user();

        // Only thread author or admin/instructor can mark best answer
        if ($user->id !== $thread->user_id && !in_array($user->role, ['admin', 'instructor'])) {
            abort(403);
        }

        DB::transaction(function () use ($thread, $post) {
            // Remove existing best answer
            $thread->posts()->where('is_best_answer', true)->update(['is_best_answer' => false]);
            // Set new best answer
            $post->update(['is_best_answer' => true]);
        });

        return back()->with('success', 'Best answer marked!');
    }

    public function pinThread(ForumThread $thread)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'instructor'])) {
            abort(403);
        }

        $thread->update(['is_pinned' => !$thread->is_pinned]);
        return back()->with('success', $thread->is_pinned ? 'Thread pinned.' : 'Thread unpinned.');
    }

    public function lockThread(ForumThread $thread)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'instructor'])) {
            abort(403);
        }

        $thread->update(['is_locked' => !$thread->is_locked]);
        return back()->with('success', $thread->is_locked ? 'Thread locked.' : 'Thread unlocked.');
    }

    public function deleteThread(ForumThread $thread)
    {
        $user = Auth::user();
        if ($user->id !== $thread->user_id && !in_array($user->role, ['admin', 'instructor'])) {
            abort(403);
        }

        $thread->delete();
        return redirect()->route('forums.index')->with('success', 'Thread deleted.');
    }
}
