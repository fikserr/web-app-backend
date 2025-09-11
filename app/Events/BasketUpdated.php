<?php

namespace App\Events;

use App\Models\Basket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BasketUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $item;
    public $userId;
    public $removed;

    /**
     * Yangi event yaratish
     */
    public function __construct($item, $userId, $removed = false)
    {
        $this->item    = $item;
        $this->userId  = $userId;
        $this->removed = $removed;
    }

    /**
     * Qaysi kanalga broadcast qilamiz
     */
    public function broadcastOn()
    {
        // har bir user uchun private channel
        return new PrivateChannel('basket.' . $this->userId);
    }

    /**
     * Event nomi (frontendda ushlash uchun)
     */
    public function broadcastAs()
    {
        return 'BasketUpdated';
    }

    /**
     * Eventga yuboriladigan data
     */
    public function broadcastWith()
    {
        return [
            'item'    => $this->item,
            'userId'  => $this->userId,
            'removed' => $this->removed,
        ];
    }
}
