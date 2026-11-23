<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

class SystemDataChanged implements ShouldBroadcastNow, ShouldDispatchAfterCommit
{
    public function broadcastOn(): array
    {
        return [new PrivateChannel('system.updates')];
    }

    public function broadcastAs(): string
    {
        return 'system.data.changed';
    }
}
