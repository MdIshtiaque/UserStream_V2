<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class UpdateDataset implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $type; // "single" for one user, "batch" for multiple users

    public function __construct($data, $type = "batch")
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function broadcastOn()
    {
        return new Channel('dataset');
    }

    public function broadcastWith()
    {
        try {
            return [
                'data' => $this->data,
                'type' => $this->type
            ];
        } catch (\Exception $e) {
            Log::error("Failed to broadcast data: " . $e->getMessage());
            return [];
        }
    }
}
