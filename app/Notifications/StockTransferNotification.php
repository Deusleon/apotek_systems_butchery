<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StockTransferNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transfer;
    protected $status;
    protected $action;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($transfer, $status, $action)
    {
        $this->transfer = $transfer;
        $this->status = $status;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Stock Transfer ' . $this->getStatusText())
            ->greeting('Hello ' . $notifiable->name);

        switch ($this->action) {
            case 'created':
                $message->line('A new stock transfer has been created.')
                    ->line('Transfer Number: ' . $this->transfer->transfer_no)
                    ->line('From Store: ' . $this->transfer->fromStore->name)
                    ->line('To Store: ' . $this->transfer->toStore->name)
                    ->action('View Transfer', route('stock-transfer.show', $this->transfer->id));
                break;

            case 'status_change':
                $message->line('A stock transfer status has been updated.')
                    ->line('Transfer Number: ' . $this->transfer->transfer_no)
                    ->line('New Status: ' . $this->getStatusText())
                    ->action('View Transfer', route('stock-transfer.show', $this->transfer->id));
                break;

            case 'needs_approval':
                $message->line('A stock transfer requires your approval.')
                    ->line('Transfer Number: ' . $this->transfer->transfer_no)
                    ->line('From Store: ' . $this->transfer->fromStore->name)
                    ->line('To Store: ' . $this->transfer->toStore->name)
                    ->action('Review Transfer', route('stock-transfer.show', $this->transfer->id));
                break;

            case 'acknowledged':
                $message->line('A stock transfer has been acknowledged by the receiving store.')
                    ->line('Transfer Number: ' . $this->transfer->transfer_no)
                    ->line('From Store: ' . $this->transfer->fromStore->name)
                    ->line('To Store: ' . $this->transfer->toStore->name)
                    ->action('View Transfer', route('stock-transfer.show', $this->transfer->id));
                break;
        }

        return $message->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'transfer_id' => $this->transfer->id,
            'transfer_no' => $this->transfer->transfer_no,
            'status' => $this->status,
            'action' => $this->action,
            'from_store' => $this->transfer->fromStore->name,
            'to_store' => $this->transfer->toStore->name
        ];
    }

    /**
     * Get human-readable status text
     *
     * @return string
     */
    protected function getStatusText()
    {
        $statuses = [
            'created' => 'Created',
            'assigned' => 'Assigned',
            'approved' => 'Approved',
            'in_transit' => 'In Transit',
            'acknowledged' => 'Acknowledged',
            'completed' => 'Completed'
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }
} 