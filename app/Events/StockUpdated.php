<?php

namespace App\Events;

use App\CurrentStock;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StockUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stock;
    public $storeId;

    public function __construct(CurrentStock $stock)
    {
        $this->stock = $stock;
        $this->storeId = $stock->store_id;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('stock.updates.' . $this->storeId);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->stock->id,
            'product_name' => $this->stock->product->name,
            'quantity' => $this->stock->quantity,
            'stock_value' => $this->stock->stock_value,
            'status' => $this->stock->getStockStatus(),
            'store_id' => $this->storeId,
            'updated_at' => now()->toDateTimeString()
        ];
    }
} 