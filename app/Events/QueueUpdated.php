<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $queue;
    public $instance_id;

    /**
     * Create a new event instance.
     */
    public function __construct($message = 'Update', $queue = null, $instance_id = null)
    {
        $this->message = $message;
        $this->queue = $queue;
        $this->instance_id = $instance_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        // [SECURITY PATCH] Hanya broadcast ke channel instansi spesifik
        // untuk mencegah kebocoran data antar instansi (Multi-Tenant)
        $channels = [];

        if ($this->instance_id) {
            $channels[] = new Channel('queues.' . $this->instance_id);
        }

        return $channels;
    }
}