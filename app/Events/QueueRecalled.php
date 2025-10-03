<?php

namespace App\Events;

use App\Models\Queue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueRecalled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Queue $queue) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('queues'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->queue->id,
            'code' => $this->queue->code,
            'destination_name' => $this->queue->destination?->name ?? 'your destination',
            'service_name' => $this->queue->service->name,
            'service' => $this->queue->service->name, // Keep for backward compatibility
            'service_id' => $this->queue->service_id,
            'destination_id' => $this->queue->destination_id,
            'status' => $this->queue->status,
            'called_at' => $this->queue->called_at?->toISOString(),
        ];
    }
}
