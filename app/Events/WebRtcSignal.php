<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRtcSignal implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $channelName;

    public function __construct(array $data, string $channelName)
    {
        $this->data = $data;
        $this->channelName = $channelName;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel($this->channelName),
        ];
    }

    public function broadcastAs(): string
    {
        return 'signal';
    }
}
