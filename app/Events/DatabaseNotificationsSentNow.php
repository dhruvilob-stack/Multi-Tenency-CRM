<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DatabaseNotificationsSentNow implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        protected Model|Authenticatable $user,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        if (method_exists($this->user, 'receivesBroadcastNotificationsOn')) {
            return new PrivateChannel($this->user->receivesBroadcastNotificationsOn());
        }

        $userClass = str_replace('\\', '.', $this->user::class);

        return new PrivateChannel("{$userClass}.{$this->user->getKey()}");
    }

    public function broadcastAs(): string
    {
        return 'database-notifications.sent';
    }
}
