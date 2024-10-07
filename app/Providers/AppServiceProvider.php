<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

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
        View::composer('layouts.app', function ($view) {
            $user = auth()->user(); // Fetch the currently authenticated user
            $sidebar = '';

            switch ($user->role_id) {
                case '1':
                    $sidebar = 'super_admin.partials.sidebar';
                    break;
                case '2':
                    $sidebar = 'admin.partials.sidebar';
                    break;
                case '3':
                    $sidebar = 'faculty.partials.sidebar';
                    break;
                case '4':
                    $sidebar = 'company.partials.sidebar';
                    break;
                case '5':
                    $sidebar = 'student.partials.sidebar';
                    break;
            }
            // Fetch unread messages count and latest unread messages if the user is authenticated
            $unreadMessagesCount = 0;
            $unreadMessages = [];

            if ($user) {
                $unreadMessagesCount = Message::where('recipient_id', $user->id)
                    ->where('status', 'unread')
                    ->count();

                $unreadMessages = Message::where('recipient_id', $user->id)
                    ->where('status', 'unread')
                    ->orderBy('created_at', 'desc')
                    ->take(5) // Get the 5 most recent unread messages
                    ->get();
            }

            $view->with('sidebar', $sidebar)
                 ->with('unreadMessagesCount', $unreadMessagesCount)
                 ->with('unreadMessages', $unreadMessages);
        });
    }
}
