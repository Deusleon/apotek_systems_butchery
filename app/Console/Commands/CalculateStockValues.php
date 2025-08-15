<?php

namespace App\Console\Commands;

use App\CurrentStock;
use App\PriceList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateStockValues extends Command
{
    protected $signature = 'stock:calculate-values';
    protected $description = 'Calculate and update stock values for all current stock';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting stock value calculation...');
        
        try {
            DB::beginTransaction();

            $stocks = CurrentStock::with(['priceList' => function($query) {
                $query->where('status', 1);
            }])->get();

            $count = 0;
            foreach ($stocks as $stock) {
                $price = $stock->priceList->first() ? $stock->priceList->first()->price : $stock->unit_cost;
                $value = $stock->quantity * $price;
                
                $stock->stock_value = $value;
                $stock->last_calculated_at = now();
                $stock->save();
                
                $count++;
            }

            DB::commit();
            $this->info("Successfully updated {$count} stock values");
            Log::info("Stock value calculation completed", ['updated_records' => $count]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error calculating stock values: ' . $e->getMessage());
            Log::error('Stock value calculation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 