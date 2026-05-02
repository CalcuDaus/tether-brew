<?php

namespace App\Providers;

use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Share unread chat count with sidebar layout
        View::composer('layouts.app', function ($view) {
            $unreadChatCount = 0;
            $user = Auth::user();
            if ($user && $user->isRider()) {
                $unreadChatCount = Conversation::where('rider_id', $user->id)
                    ->withCount(['messages as unread' => function ($q) use ($user) {
                        $q->where('sender_id', '!=', $user->id)->where('is_read', false);
                    }])
                    ->get()
                    ->sum('unread');
            }
            $view->with('unreadChatCount', $unreadChatCount);
        });
    }
}
