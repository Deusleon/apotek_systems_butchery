<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixDataRelationships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create backup tables first
        DB::statement('CREATE TABLE inv_current_stock_backup AS SELECT * FROM inv_current_stock');
        DB::statement('CREATE TABLE sales_details_backup AS SELECT * FROM sales_details');
        DB::statement('CREATE TABLE sales_prices_backup AS SELECT * FROM sales_prices');

        // Step 1: Create mapping for orphaned stock records
        // Find stocks with invalid product_ids and map them to valid products
        $orphanedStocks = DB::table('inv_current_stock')
            ->whereNotIn('product_id', DB::table('inv_products')->pluck('id'))
            ->get();

        $productMapping = [];
        $validProducts = DB::table('inv_products')
            ->where('status', 1)
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        $mappingIndex = 0;
        foreach ($orphanedStocks as $stock) {
            // Map to a valid product ID in sequence
            $productMapping[$stock->product_id] = $validProducts[$mappingIndex % count($validProducts)];
            $mappingIndex++;
        }

        // Step 2: Update CurrentStock product_ids
        foreach ($productMapping as $oldId => $newId) {
            DB::table('inv_current_stock')
                ->where('product_id', $oldId)
                ->update(['product_id' => $newId]);
        }

        // Step 3: Fix SalesDetail stock_id references
        $invalidSalesDetails = DB::table('sales_details')
            ->whereNotIn('stock_id', DB::table('inv_current_stock')->pluck('id'))
            ->get();

        // Map invalid stock_ids to valid ones based on product_id
        foreach ($invalidSalesDetails as $saleDetail) {
            $validStock = DB::table('inv_current_stock')
                ->where('product_id', $saleDetail->product_id ?? 100000) // fallback to first product
                ->first();

            if ($validStock) {
                DB::table('sales_details')
                    ->where('id', $saleDetail->id)
                    ->update(['stock_id' => $validStock->id]);
            }
        }

        // Step 4: Populate missing price data
        $zeroPrices = DB::table('sales_prices')
            ->where('price', 0)
            ->get();

        foreach ($zeroPrices as $priceRecord) {
            $stock = DB::table('inv_current_stock')->find($priceRecord->stock_id);
            if ($stock && $stock->unit_cost > 0) {
                // Set price to unit_cost + 10% markup as default
                $defaultPrice = $stock->unit_cost * 1.1;
                DB::table('sales_prices')
                    ->where('id', $priceRecord->id)
                    ->update(['price' => $defaultPrice]);
            }
        }

        // Step 5: Ensure all required relationships exist
        // Create any missing price records for stocks without prices
        $stocksWithoutPrices = DB::table('inv_current_stock')
            ->leftJoin('sales_prices', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
            ->whereNull('sales_prices.id')
            ->select('inv_current_stock.*')
            ->get();

        foreach ($stocksWithoutPrices as $stock) {
            $priceCategoryId = 1; // Default price category
            $price = $stock->unit_cost > 0 ? $stock->unit_cost * 1.1 : 1000; // Default markup

            DB::table('sales_prices')->insert([
                'stock_id' => $stock->id,
                'price_category_id' => $priceCategoryId,
                'price' => $price,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restore from backups
        DB::statement('DROP TABLE IF EXISTS inv_current_stock');
        DB::statement('ALTER TABLE inv_current_stock_backup RENAME TO inv_current_stock');

        DB::statement('DROP TABLE IF EXISTS sales_details');
        DB::statement('ALTER TABLE sales_details_backup RENAME TO sales_details');

        DB::statement('DROP TABLE IF EXISTS sales_prices');
        DB::statement('ALTER TABLE sales_prices_backup RENAME TO sales_prices');

        // Drop backup tables
        DB::statement('DROP TABLE IF EXISTS inv_current_stock_backup');
        DB::statement('DROP TABLE IF EXISTS sales_details_backup');
        DB::statement('DROP TABLE IF EXISTS sales_prices_backup');
    }
}
