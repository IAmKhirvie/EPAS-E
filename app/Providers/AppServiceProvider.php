<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Http\View\Composers\AnnouncementComposer;
use App\Models\Course;
use App\Models\Module;
use App\Models\Homework;
use App\Models\Announcement;
use App\Models\ForumThread;
use App\Policies\CoursePolicy;
use App\Policies\ModulePolicy;
use App\Policies\HomeworkPolicy;
use App\Policies\AnnouncementPolicy;
use App\Policies\ForumThreadPolicy;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->singleton(\App\Services\PHPMailerService::class, function ($app) {
            return new \App\Services\PHPMailerService();
        });
    }

    public function boot(): void
    {
        // Force HTTPS scheme if:
        // - FORCE_HTTPS is enabled
        // - APP is in production
        // - OR the current request is already secure (Cloudflare tunnel, etc.)
        if (config('app.force_https', false) || config('app.env') === 'production' || request()->secure()) {
            URL::forceScheme('https');
        }

        // Use Bootstrap 5 pagination
        Paginator::useBootstrapFive();

        View::composer('*', AnnouncementComposer::class);

        // Register authorization policies
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Module::class, ModulePolicy::class);
        Gate::policy(Homework::class, HomeworkPolicy::class);
        Gate::policy(Announcement::class, AnnouncementPolicy::class);
        Gate::policy(ForumThread::class, ForumThreadPolicy::class);
    }
}