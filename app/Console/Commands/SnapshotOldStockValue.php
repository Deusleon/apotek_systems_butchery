<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\PriceCategory;
use App\InvOldStockValue;

class SnapshotOldStockValue extends Command
{
    protected $signature = 'snapshot:oldstock';
    protected $description = 'Take daily snapshot of inv_current_stock into inv_old_stock_values';

    public function handle()
    {
        Log::info('SnapshotOldStockValue command started at: ' . Carbon::now()->toDateTimeString());
        $this->info('Starting old stock snapshot...');
        DB::beginTransaction();

        try {
            // snapshot_date is today (so running at 00:00 yields "today" snapshot)
            $snapshotDate = Carbon::now()->toDateString();
            Log::info('Snapshot date set to: ' . $snapshotDate);
            $storeId = function_exists('current_store_id') ? current_store_id() : null;
            $isAllStore = function_exists('is_all_store') ? is_all_store() : false;
            Log::info('Store ID: ' . ($storeId ?? 'null') . ', Is All Store: ' . ($isAllStore ? 'true' : 'false'));

            $priceCategories = PriceCategory::all(); // assumes you have PriceCategory model
            if ($priceCategories->isEmpty()) {
                $this->info('No price categories found; taking snapshot with NULL price_category_id');
                $priceCategoryIds = [null];
            } else {
                $priceCategoryIds = $priceCategories->pluck('id')->toArray();
            }

            foreach ($priceCategoryIds as $priceCategoryId) {

                // Remove existing snapshot rows for this date & price_category to avoid duplicates
                // $deleteQuery = DB::table('inv_old_stock_values')->where('snapshot_date', $snapshotDate);
                // if (!is_null($priceCategoryId)) {
                //     $deleteQuery->where('price_category_id', $priceCategoryId);
                // } else {
                //     $deleteQuery->whereNull('price_category_id');
                // }
                // if (!$isAllStore && $storeId) {
                //     $deleteQuery->where('store_id', $storeId);
                // }
                // $deleteQuery->delete();

                // Subquery: latest stock id & unit_cost per product + store
                $latestStock = DB::table('inv_current_stock as ics1')
                    ->select('ics1.product_id', 'ics1.store_id', 'ics1.id as latest_stock_id', 'ics1.unit_cost')
                    ->whereRaw('ics1.id = (
                        SELECT ics2.id
                        FROM inv_current_stock as ics2
                        WHERE ics2.product_id = ics1.product_id
                          AND ics2.store_id = ics1.store_id
                        ORDER BY ics2.created_at DESC, ics2.id DESC
                        LIMIT 1
                    )');

                // Subquery: latest price per stock_id for the given price category
                $latestPrice = DB::table('sales_prices as sp1')
                    ->select('sp1.stock_id', 'sp1.price')
                    ->whereRaw(($priceCategoryId ? 'sp1.price_category_id = ' . (int)$priceCategoryId : 'sp1.price_category_id IS NULL'))
                    ->whereRaw('sp1.id = (
                        SELECT sp2.id
                        FROM sales_prices as sp2
                        WHERE sp2.stock_id = sp1.stock_id
                          ' . ($priceCategoryId ? ' AND sp2.price_category_id = ' . (int)$priceCategoryId : ' AND sp2.price_category_id IS NULL') . '
                        ORDER BY sp2.created_at DESC, sp2.id DESC
                        LIMIT 1
                    )');

                // Main aggregation: sum quantities per product + store, taking latest unit_cost and latest selling price (via latest_stock.latest_stock_id)
                $stocksQuery = DB::table('inv_current_stock as ics')
                    ->join('inv_products as p', 'ics.product_id', '=', 'p.id')
                    ->joinSub($latestStock, 'latest_stock', function ($join) {
                        $join->on('ics.product_id', '=', 'latest_stock.product_id')
                             ->on('ics.store_id', '=', 'latest_stock.store_id');
                    })
                    ->leftJoinSub($latestPrice, 'latest_price', function ($join) {
                        $join->on('latest_stock.latest_stock_id', '=', 'latest_price.stock_id');
                    })
                    ->select(
                        'ics.product_id',
                        'ics.store_id',
                        'p.name as product_name',
                        'latest_stock.unit_cost',
                        DB::raw('SUM(ics.quantity) as quantity'),
                        DB::raw('COALESCE(latest_price.price, 0) as selling_price')
                    )
                    ->groupBy('ics.product_id', 'ics.store_id', 'latest_stock.unit_cost', 'p.name');

                if (!$isAllStore && $storeId) {
                    $stocksQuery->where('ics.store_id', $storeId);
                }

                $stocks = $stocksQuery->get();

                // Prepare bulk insert
                $inserts = [];
                $now = Carbon::now()->toDateTimeString();
                foreach ($stocks as $s) {
                    $inserts[] = [
                        'product_id' => $s->product_id,
                        'store_id' => $s->store_id,
                        'price_category_id' => $priceCategoryId,
                        'quantity' => (float)$s->quantity,
                        'buy_price' => (float)$s->unit_cost, // unit_cost from latest_stock
                        'sell_price' => $s->selling_price !== null ? (float)$s->selling_price : null,
                        'snapshot_date' => $snapshotDate,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if (!empty($inserts)) {
                    // insert in chunks
                    $chunks = array_chunk($inserts, 500);
                    foreach ($chunks as $chunk) {
                        DB::table('inv_old_stock_values')->insert($chunk);
                    }
                }
            }

            DB::commit();
            Log::info('Snapshot committed successfully for date: ' . $snapshotDate);
            $this->info('Old stock snapshot created for date: ' . $snapshotDate);
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating old stock snapshot: ' . $e->getMessage() . ' at line ' . $e->getLine() . ' in ' . $e->getFile());
            $this->error('Snapshot failed: ' . $e->getMessage());
            return 1;
        }
    }
}
