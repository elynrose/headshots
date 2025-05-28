<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Generate;

class GenerateStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $generate;

    public function __construct(Generate $generate)
    {
        $this->generate = $generate;
    }

    public function broadcastOn()
    {
        return new Channel('generate-status');
    }

    public function broadcastAs()
    {
        return 'status-updated';
    }

    public function broadcastWith()
    {
        return [
            'generate_id' => $this->generate->id,
            'status' => $this->generate->status,
            'type' => $this->generate->content_type,
            'image_url' => $this->generate->image_url,
            'video_url' => $this->generate->video_url,
            'queue_position' => $this->generate->queue_position
        ];
    }
} 