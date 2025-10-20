<?php

namespace App\Notifications;

use App\CurrentStock;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockNotification extends Notification
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
        // Log::info("[StockNotification.php] StockNotification created with store_id: {$this->store_id}");
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
        $out_of_stock_count = $this->getOutOfStockCount();
        $expired_count = $this->getExpiredCount();

        return (new MailMessage)
            ->subject('Stock Status Alert')
            ->line('Stock status update:')
            ->line('Out of stock items: ' . $out_of_stock_count)
            ->line('Expired items: ' . $expired_count)
            ->action('View Dashboard', url('/home'))
            ->line('Please review your inventory.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $out_of_stock_count = $this->getOutOfStockCount();
        $expired_count = $this->getExpiredCount();
        $below_min_level_count = $this->getBelowMinLevelCount();
        $expiring_soon_count = $this->getExpiringSoonCount();

        // Log::info('[StockNotification.php] Out of stock count: ' . $out_of_stock_count);
        // Log::info('[StockNotification.php] Expired count: ' . $expired_count);
        // Log::info('[StockNotification.php] Below min level count: ' . $below_min_level_count);
        // Log::info('[StockNotification.php] Expiring soon count: ' . $expiring_soon_count);

        return [
            'out_of_stock_count' => $out_of_stock_count,
            'expired_count' => $expired_count,
            'below_min_level_count' => $below_min_level_count,
            'expiring_soon_count' => $expiring_soon_count,
            'store_id' => $this->store_id
        ];
    }

    /**
     * Get out of stock count
     *
     * @return int
     */
    private function getOutOfStockCount()
    {
        $query = CurrentStock::where('quantity', 0);

        if ($this->store_id && $this->store_id !== 1) {
            $query->where('store_id', $this->store_id);
        } else {
            // For store 1 (ALL), group by product_id to avoid duplicates
            $query->groupby('product_id');
        }

        $count = $query->get()->count();
        // Log::info("[StockNotification.php] Out of stock count for store {$this->store_id}: {$count}");
        return $count;
    }

    /**
     * Get expired items count
     *
     * @return int
     */
    private function getExpiredCount()
    {
        $query = CurrentStock::where('quantity', '>', 0)->whereRaw('expiry_date <= date(now())');

        if ($this->store_id && $this->store_id !== 1) {
            $query->where('store_id', $this->store_id);
        } else {
            // For store 1 (ALL), we don't need to filter by store
            // as expired items should be counted across all stores
        }

        $count = $query->count();
        // Log::info("[StockNotification.php] Expired count for store {$this->store_id}: {$count}");
        return $count;
    }

    /**
     * Get below minimum level count
     *
     * @return int
     */
    private function getBelowMinLevelCount()
{
    $query = DB::table('inv_current_stock as cs')
        ->join('inv_products as p', 'p.id', '=', 'cs.product_id')
        ->select(
            'p.id as product_id',
            'p.name as product_name',
            'p.brand',
            'p.pack_size',
            'p.sales_uom',
            'p.min_quantinty',
            DB::raw('SUM(cs.quantity) as available_qty')
        )
        ->whereNotNull('p.min_quantinty')
        ->where('p.min_quantinty', '>', 0)
        ->groupBy('p.id', 'p.name', 'p.brand', 'p.pack_size', 'p.sales_uom', 'p.min_quantinty')
        ->havingRaw('SUM(cs.quantity) < p.min_quantinty AND SUM(cs.quantity) > 0');

    if ($this->store_id && $this->store_id !== 1) {
        $query->where('cs.store_id', $this->store_id);
    }

    $count = $query->count();

    // Log::info("[StockNotification.php] Below min level count for store {$this->store_id}: {$count}");

    return $count;
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
        // Log::info("[StockNotification.php] Expiring soon count for store {$this->store_id}: {$count}");
        return $count;
    }
}
