<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class NotificationSectionManager
{
    public static function unreadCount(string $section): int
    {
        $user = Auth::user();

        if (! $user) {
            return 0;
        }

        return $user->unreadNotifications()
            ->where('data->viewData->section', $section)
            ->count();
    }

    public static function markSectionAsRead(string $section): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $user->unreadNotifications()
            ->where('data->viewData->section', $section)
            ->update(['read_at' => now()]);
    }
}
