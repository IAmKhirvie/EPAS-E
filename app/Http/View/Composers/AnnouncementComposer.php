<?php

namespace App\Http\View\Composers;

use App\Models\Announcement;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AnnouncementComposer
{
    public function compose(View $view)
    {
        if (Auth::check()) {
            // Get recent announcements
            $recentAnnouncements = Announcement::with(['user'])
                ->where(function($query) {
                    $query->whereNull('publish_at')
                        ->orWhere('publish_at', '<=', now());
                })
                ->orderBy('is_pinned', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $view->with([
                'recentAnnouncements' => $recentAnnouncements,
                'recentAnnouncementsCount' => $recentAnnouncements->count(),
            ]);
        } else {
            $view->with([
                'recentAnnouncements' => collect(),
                'recentAnnouncementsCount' => 0,
            ]);
        }
    }
}
