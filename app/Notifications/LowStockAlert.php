<?php

namespace App\Notifications;

use App\CurrentStock;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LowStockAlert extends Notification
{
    use Queueable;

    protected $stock;

    public function __construct(CurrentStock $stock)
    {
        $this->stock = $stock;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $url = route('current-stock.show', $this->stock->id);

        return (new MailMessage)
            ->subject('Low Stock Alert - ' . $this->stock->product->name)
            ->line('The following product is running low on stock:')
            ->line('Product: ' . $this->stock->product->name)
            ->line('Current Quantity: ' . $this->stock->quantity)
            ->line('Minimum Level: ' . $this->stock->min_stock_level)
            ->line('Alert Threshold: ' . $this->stock->alert_threshold)
            ->action('View Product', $url)
            ->line('Please take necessary action to replenish the stock.');
    }

    public function toArray($notifiable)
    {
        return [
            'stock_id' => $this->stock->id,
            'product_name' => $this->stock->product->name,
            'current_quantity' => $this->stock->quantity,
            'min_stock_level' => $this->stock->min_stock_level,
            'alert_threshold' => $this->stock->alert_threshold,
            'store_id' => $this->stock->store_id,
            'store_name' => $this->stock->store->name
        ];
    }
} 