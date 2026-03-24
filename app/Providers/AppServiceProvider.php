<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Http\View\Composers\AnnouncementComposer;
use App\Http\View\Composers\TrashComposer;
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
        // Auto-detect HTTPS based on environment:
        // 1. FORCE_HTTPS=true in .env - always use HTTPS
        // 2. Production environment - always use HTTPS
        // 3. Cloudflare tunnel detected (X-Forwarded-Proto or trycloudflare.com host)
        // 4. Any reverse proxy with X-Forwarded-Proto: https
        //
        // For local development without tunnel: set FORCE_HTTPS=false (default)
        // For Cloudflare tunnel: auto-detected, no config needed
        // For production: set FORCE_HTTPS=true or APP_ENV=production

        $forceHttps = config('app.force_https', false);
        $isProduction = config('app.env') === 'production';
        $isBehindHttpsProxy = isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';
        $isCloudflare = isset($_SERVER['HTTP_CF_VISITOR']) || str_contains($_SERVER['HTTP_HOST'] ?? '', 'trycloudflare.com');

        if ($forceHttps || $isProduction || $isBehindHttpsProxy || $isCloudflare) {
            URL::forceScheme('https');
        }

        // Use Bootstrap 5 pagination
        Paginator::useBootstrapFive();

        View::composer('*', AnnouncementComposer::class);
        View::composer('partials.sidebar', TrashComposer::class);

        // Register authorization policies
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Module::class, ModulePolicy::class);
        Gate::policy(Homework::class, HomeworkPolicy::class);
        Gate::policy(Announcement::class, AnnouncementPolicy::class);
        Gate::policy(ForumThread::class, ForumThreadPolicy::class);
    }
}