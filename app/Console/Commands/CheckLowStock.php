<?php

namespace App\Console\Commands;

use App\CurrentStock;
use App\Notifications\LowStockAlert;
use App\User;
use Illuminate\Console\Command;

class CheckLowStock extends Command
{
    protected $signature = 'stock:check-low';
    protected $description = 'Check for low stock items and send notifications';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $lowStockItems = CurrentStock::with(['product', 'store'])
            ->whereNotNull('alert_threshold')
            ->whereNotNull('min_stock_level')
            ->whereRaw('quantity <= alert_threshold')
            ->get();

        if ($lowStockItems->isEmpty()) {
            $this->info('No low stock items found.');
            return;
        }

        // Get users with stock management permissions
        $users = User::permission(['manage stock', 'view stock'])->get();

        foreach ($lowStockItems as $stock) {
            foreach ($users as $user) {
                // Only notify users who have access to this store
                if ($user->hasRole('admin') || $user->stores->contains($stock->store_id)) {
                    $user->notify(new LowStockAlert($stock));
                }
            }
            $this->info("Sent low stock alert for {$stock->product->name} at {$stock->store->name}");
        }

        $this->info("Completed checking " . $lowStockItems->count() . " low stock items.");
    }
} 