<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Models\Notification;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // fokus terhadap bagian nontifikasi
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $navbarNotifications = Notification::where('user_id', auth()->id())
                    ->latest()
                    ->take(5) // ambil 5 terbaru
                    ->get();

                $unreadCount = Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->count();

                $view->with([
                    'navbarNotifications' => $navbarNotifications,
                    'unreadCount' => $unreadCount,
                ]);
            }
        });

        // Gate ini hanya akan mengizinkan Super Admin.
        Gate::define('access-superadmin-area', function (User $user) {
            return $user->isSuperAdmin();
        });

        // Gate ini akan mengizinkan Admin DAN Super Admin.
        // (Karena Super Admin juga seorang Admin, kan?)
        Gate::define('access-admin-area', function (User $user) {
            return $user->isSuperAdmin() || $user->isAdmin();
        });

        Gate::define('access-user-area', function (User $user) {
            return !$user->isAdmin() && !$user->isSuperAdmin();
        });
    }
}
