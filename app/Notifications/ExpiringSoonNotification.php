<?php

namespace App\Notifications;

use App\CurrentStock;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ExpiringSoonNotification extends Notification
{
    use Queueable;

    protected $store_id;

    /**
     * Create a new notification instance.
     *
     * @param int|null $store_id
     * @return void
     */
    public function __construct($store_id = null)
    {
        $this->store_id = $store_id;
        // Log::info("[ExpiringSoonNotification.php] ExpiringSoonNotification created with store_id: {$this->store_id}");
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $expiring_soon_count = $this->getExpiringSoonCount();

        return (new MailMessage)
            ->subject('Items Expiring Soon Alert')
            ->line('Items expiring in the next 3 months:')
            ->line('Items expiring soon: ' . $expiring_soon_count)
            ->action('View Dashboard', url('/home'))
            ->line('Please review your inventory and plan accordingly.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $expiring_soon_count = $this->getExpiringSoonCount();

        // Log::info('Items expiring soon count: ' . $expiring_soon_count);

        return [
            'expiring_soon_count' => $expiring_soon_count,
            'store_id' => $this->store_id
        ];
    }

    /**
     * Get items expiring in 3 months count
     *
     * @return int
     */
    private function getExpiringSoonCount()
    {
        $query = CurrentStock::where('quantity', '>', 0)
            ->whereBetween('expiry_date', [
                now()->startOfDay(),
                now()->addMonths(3)->endOfDay()
            ]);

        if ($this->store_id && $this->store_id !== 1) {
            $query->where('store_id', $this->store_id);
        }

        $count = $query->count();
        // Log::info("[ExpiringSoonNotification.php] Expiring soon count for store {$this->store_id}: {$count}");
        return $count;
    }
}