<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\SanitizesContent;
use App\Http\Requests\StoreForumThreadRequest;
use App\Http\Requests\StoreForumPostRequest;

class ForumController extends Controller
{
    use SanitizesContent;

    /**
     * Display forums homepage with all categories (merged with announcements).
     */
    public function index()
    {
        return view('forums.index');
    }

    /**
     * Display threads in a specific category.
     */
    public function category(ForumCategory $category)
    {
        $user = Auth::user();

        $threads = ForumThread::where('category_id', $category->id)
            ->with(['user', 'lastReplyUser', 'readByUsers'])
            ->withCount('posts')
            ->forUser($user)
            ->published()
            ->recent()
            ->paginate(20);

        // Check if user can post in this category
        $canPost = $category->canUserPost($user);

        return view('forums.category', compact('category', 'threads', 'canPost'));
    }

    /**
     * Display a specific thread with its posts.
     */
    public function thread(ForumThread $thread)
    {
        $user = Auth::user();

        // Check if user can view this thread based on target_roles
        if ($thread->target_roles && $thread->target_roles !== 'all' && !in_array($user->role, explode(',', $thread->target_roles))) {
            abort(403, 'You do not have permission to view this thread.');
        }

        $thread->incrementViews();
        $thread->markAsRead($user->id);

        $thread->load(['category', 'user']);

        $posts = $thread->posts()
            ->with(['user', 'replies.user'])
            ->topLevel()
            ->orderBy('is_best_answer', 'desc')
            ->orderBy('created_at')
            ->paginate(20);

        $isSubscribed = $thread->isSubscribedBy($user);

        return view('forums.thread', compact('thread', 'posts', 'isSubscribed'));
    }

    /**
     * Show form to create a new thread.
     */
    public function createThread(Request $request)
    {
        $user = Auth::user();

        // Filter categories user can post in
        $allCategories = ForumCategory::active()->ordered()->get();
        $categories = $allCategories->filter(fn($cat) => $cat->canUserPost($user));

        $selectedCategory = $request->query('category');
        $isAnnouncement = $request->query('announcement') === '1';

        return view('forums.create-thread', compact('categories', 'selectedCategory', 'isAnnouncement'));
    }

    /**
     * Store a new thread.
     */
    public function storeThread(StoreForumThreadRequest $request)
    {
        $user = Auth::user();

        $validated = $request->validated();

        // Check if user can post in this category
        $category = ForumCategory::findOrFail($request->category_id);
        if (!$category->canUserPost($user)) {
            abort(403, 'You cannot post in this category.');
        }

        try {
            $threadData = [
                'category_id' => $request->category_id,
                'user_id' => $user->id,
                'title' => $this->stripHtml($request->title),
                'body' => $this->sanitizeContent($request->body),
            ];

            // Add announcement fields if this is an announcement category and user is admin/instructor
            if ($category->is_announcement_category && Roles::canManageStudents($user->role)) {
                $threadData['is_announcement'] = true;
                $threadData['is_urgent'] = $request->boolean('is_urgent');
                $threadData['is_pinned'] = $request->boolean('is_pinned');
                $threadData['target_roles'] = $request->input('target_roles', 'all');
                $threadData['deadline'] = $request->deadline;
                $threadData['publish_at'] = $request->publish_at;
            }

            // Allow admins/instructors to pin any thread
            if (Roles::canManageStudents($user->role) && $request->has('is_pinned')) {
                $threadData['is_pinned'] = $request->boolean('is_pinned');
            }

            $thread = ForumThread::create($threadData);

            // Auto-subscribe the author
            $thread->subscribers()->attach($user->id);

            return redirect()->route('forums.thread', $thread)
                ->with('success', $category->is_announcement_category ? 'Announcement posted successfully.' : 'Thread created successfully.');
        } catch (\Exception $e) {
            Log::error('Forum thread creation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return back()->withInput()->with('error', 'Failed to create thread. Please try again.');
        }
    }

    /**
     * Store a reply to a thread.
     */
    public function storePost(StoreForumPostRequest $request, ForumThread $thread)
    {
        if ($thread->is_locked) {
            return back()->with('error', 'This thread is locked.');
        }

        $validated = $request->validated();

        try {
            $post = ForumPost::create([
                'thread_id' => $thread->id,
                'user_id' => Auth::id(),
                'parent_id' => $request->parent_id,
                'body' => $this->sanitizeContent($request->body),
            ]);

            // TODO: Send notifications to subscribers

            return back()->with('success', 'Reply posted successfully.');
        } catch (\Exception $e) {
            Log::error('Forum post creation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return back()->withInput()->with('error', 'Failed to post reply. Please try again.');
        }
    }

    /**
     * Update a post.
     */
    public function updatePost(Request $request, ForumPost $post)
    {
        if ($post->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'body' => 'required|string|min:3',
        ]);

        try {
            $post->update([
                'body' => $this->sanitizeContent($request->body),
            ]);

            return back()->with('success', 'Post updated successfully.');
        } catch (\Exception $e) {
            Log::error('Forum post update failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return back()->withInput()->with('error', 'Failed to update post. Please try again.');
        }
    }

    /**
     * Delete a post.
     */
    public function deletePost(ForumPost $post)
    {
        if ($post->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        try {
            $post->delete();

            return back()->with('success', 'Post deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Forum post deletion failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return back()->with('error', 'Failed to delete post. Please try again.');
        }
    }

    /**
     * Vote on a post.
     */
    public function votePost(Request $request, ForumPost $post)
    {
        $request->validate([
            'vote_type' => 'required|in:up,down',
        ]);

        $post->vote(Auth::user(), $request->vote_type);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'upvotes' => $post->fresh()->upvotes_count,
                'downvotes' => $post->fresh()->downvotes_count,
                'score' => $post->fresh()->score,
            ]);
        }

        return back();
    }

    /**
     * Mark a post as the best answer.
     */
    public function markAsBestAnswer(ForumPost $post)
    {
        $thread = $post->thread;

        // Only thread author or admin can mark best answer
        if ($thread->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $post->markAsBestAnswer();

        return back()->with('success', 'Best answer marked successfully.');
    }

    /**
     * Subscribe to a thread.
     */
    public function subscribe(ForumThread $thread)
    {
        $thread->subscribers()->syncWithoutDetaching([Auth::id()]);

        return back()->with('success', 'Subscribed to thread.');
    }

    /**
     * Unsubscribe from a thread.
     */
    public function unsubscribe(ForumThread $thread)
    {
        $thread->subscribers()->detach(Auth::id());

        return back()->with('success', 'Unsubscribed from thread.');
    }

    /**
     * Lock/unlock a thread (admin only).
     */
    public function toggleLock(ForumThread $thread)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isInstructor()) {
            abort(403, 'Unauthorized');
        }

        try {
            $thread->update(['is_locked' => !$thread->is_locked]);

            $status = $thread->is_locked ? 'locked' : 'unlocked';
            return back()->with('success', "Thread {$status} successfully.");
        } catch (\Exception $e) {
            Log::error('Forum thread lock toggle failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return back()->with('error', 'Failed to update thread lock status. Please try again.');
        }
    }

    /**
     * Pin/unpin a thread (admin only).
     */
    public function togglePin(ForumThread $thread)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isInstructor()) {
            abort(403, 'Unauthorized');
        }

        try {
            $thread->update(['is_pinned' => !$thread->is_pinned]);

            $status = $thread->is_pinned ? 'pinned' : 'unpinned';
            return back()->with('success', "Thread {$status} successfully.");
        } catch (\Exception $e) {
            Log::error('Forum thread pin toggle failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return back()->with('error', 'Failed to update thread pin status. Please try again.');
        }
    }

    /**
     * Search threads.
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        $query = $request->input('q');

        $threads = ForumThread::where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('body', 'like', "%{$query}%");
            })
            ->forUser($user)
            ->published()
            ->with(['user', 'category'])
            ->recent()
            ->paginate(20);

        return view('forums.search', compact('threads', 'query'));
    }

    /**
     * Get recent announcements for navbar (API).
     */
    public function getRecentAnnouncements()
    {
        $user = Auth::user();

        $announcements = ForumThread::with(['user', 'category', 'readByUsers'])
            ->fromAnnouncementCategories()
            ->forUser($user)
            ->published()
            ->orderBy('is_urgent', 'desc')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($thread) use ($user) {
                return [
                    'id' => $thread->id,
                    'title' => $thread->title,
                    'content' => \Illuminate\Support\Str::limit(strip_tags($thread->body), 100),
                    'is_urgent' => $thread->is_urgent,
                    'is_pinned' => $thread->is_pinned,
                    'is_read' => $thread->isReadByUser($user),
                    'author' => $thread->user->full_name,
                    'category' => $thread->category->name,
                    'created_at' => $thread->created_at->diffForHumans(),
                    'url' => route('forums.thread', $thread),
                ];
            });

        return response()->json($announcements);
    }

    /**
     * Get unread announcements count for navbar (API).
     */
    public function getUnreadCount()
    {
        $user = Auth::user();

        $count = ForumThread::fromAnnouncementCategories()
            ->forUser($user)
            ->published()
            ->whereDoesntHave('readByUsers', fn($q) => $q->where('user_id', $user->id))
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Mark an announcement as read (API).
     */
    public function markAsRead(ForumThread $thread)
    {
        $thread->markAsRead(Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Marked as read'
        ]);
    }
}
