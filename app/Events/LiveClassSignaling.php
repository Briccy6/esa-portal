<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveClassSignaling implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roomId;
    public $data;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($roomId, $data, $userId = null)
    {
        $this->roomId = $roomId;
        $this->data = $data;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new PrivateChannel('live-class.' . $this->roomId);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'live-class.signaling';
    }
}
