<?php

namespace App\Filament\Concerns;

use App\Support\NotificationSectionManager;

trait MarksNotificationSectionRead
{
    protected function markNotificationSectionAsRead(string $section): void
    {
        NotificationSectionManager::markSectionAsRead($section);
    }
}
