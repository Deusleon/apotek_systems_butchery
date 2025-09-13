<?php

namespace App\Http\Controllers;

use App\Setting;
use App\StockTracking;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OutGoingStockController extends Controller {

    public function index(){
        return view('stock_management.out_going_stock.index');
    }

   public function showOutStock(Request $request)
{
    if (! $request->ajax()) {
        abort(400, 'AJAX only');
    }

    $from = \Carbon\Carbon::parse($request->input('date_from'))->startOfDay();
    $to   = \Carbon\Carbon::parse($request->input('date_to'))->endOfDay();

    $default_store = current_store_id();
    $useStoreFilter = ! is_all_store();

    // ----------------------------
    // 1) Fetch stock_tracking with OUT movements
    // ----------------------------
    $stockTracking = StockTracking::whereBetween('updated_at', [$from->toDateTimeString(), $to->toDateTimeString()])
        ->where('movement', 'OUT')
        ->when($useStoreFilter, function($q) use ($default_store) {
            return $q->where('store_id', $default_store);
        })
        ->get();

    if ($stockTracking->isEmpty()) {
        return response()->json([
            'detailed' => [],
            'summary'  => [],
        ]);
    }

    // ----------------------------
    // 2) Prefetch product details
    // ----------------------------
    $productIds = $stockTracking->pluck('product_id')->unique()->all();
    $products = DB::table('inv_products')
        ->whereIn('id', $productIds)
        ->get()
        ->keyBy('id');

    // ----------------------------
    // 3) Aggregate current_stock per product_id (sum across all batches)
    // ----------------------------
    $currentStockByProductQuery = DB::table('inv_current_stock')
        ->select('product_id', DB::raw('SUM(quantity) as total_stock'))
        ->whereIn('product_id', $productIds);

    if ($useStoreFilter) {
        $currentStockByProductQuery->where('store_id', $default_store);
    }

    $currentStockByProduct = $currentStockByProductQuery
        ->groupBy('product_id')
        ->get()
        ->keyBy('product_id');

    // ----------------------------
    // 4) DETAILED: batch-wise (stock_id)
    // ----------------------------
    $groupedByStock = $stockTracking->groupBy('stock_id')->map(function ($rows) use ($currentStockByProduct, $products) {
        $stockId   = $rows->first()->stock_id;
        $productId = $rows->first()->product_id;

        $currentStockBatchRow = DB::table('inv_current_stock')
            ->where('id', $stockId)
            ->first();

        $currentStockQty = $currentStockBatchRow ? (float) $currentStockBatchRow->quantity : 0;
        $batchNumber     = $currentStockBatchRow ? $currentStockBatchRow->batch_number : null;

        // sum all batches of this product
        $currentStockProduct = isset($currentStockByProduct[$productId]) 
            ? (float) $currentStockByProduct[$productId]->total_stock 
            : 0;

        $product = isset($products[$productId]) ? (array) $products[$productId] : null;

        return [
            'stock_id'               => $stockId,
            'product_id'             => $productId,
            'batch_number'           => $batchNumber,
            'product_name'           => $product['name'] ?? null,
            'out_total'              => (float) $rows->sum('quantity'),
            'current_stock_batch'    => $currentStockQty,
            'current_stock_product'  => $currentStockProduct,
            'store_id'               => $rows->first()->store_id ?? null,
            'product'                => $product,
            'out_movements'          => $rows->map(function ($item) {
                return [
                    'id'       => $item->id,
                    'qty'      => (float) $item->quantity,
                    'date'     => $item->updated_at,
                    'out_mode' => $item->out_mode,
                    'barcode'  => $item->barcode,
                ];
            })->values(),
        ];
    })->values();

    // ----------------------------
    // 5) SUMMARY: product-wise
    // ----------------------------
    $groupedByProduct = $stockTracking->groupBy('product_id')->map(function ($rows) use ($currentStockByProduct, $products) {
        $productId = $rows->first()->product_id;
        $product = isset($products[$productId]) ? (array) $products[$productId] : null;

        return [
            'product_id'    => $productId,
            'product_name'  => $product['name'] ?? null,
            'out_total'     => (float) $rows->sum('quantity'),
            'current_stock' => isset($currentStockByProduct[$productId])
                                ? (float) $currentStockByProduct[$productId]->total_stock
                                : 0,
            'out_movements' => $rows->map(function ($item) {
                return [
                    'stock_id' => $item->stock_id,
                    'qty'      => (float) $item->quantity,
                    'date'     => $item->updated_at,
                    'out_mode' => $item->out_mode,
                ];
            })->values(),
            'product'       => $product,
        ];
    })->values();

    return response()->json([
        'detailed' => $groupedByStock,
        'summary'  => $groupedByProduct,
    ]);
}



//     public function showOutStock(Request $request)
// {
//     if ($request->ajax()) {
//         $default_store = current_store_id();
//         $date_from = date('Y-m-d', strtotime($request->date_from));
//         $date_to = date('Y-m-d', strtotime($request->date));

//         // Step 1: Fetch distinct product IDs from stock tracking table
//         $productIdsQuery = DB::table('stock_trackings')
//             ->select('product_id')
//             ->distinct()
//             ->whereBetween('updated_at', [$date_from, $date_to]);
        
//         // Apply store filter if not all stores
//         if (!is_all_store()) {
//             $productIdsQuery->where('store_id', $default_store);
//         }
        
//         $productIds = $productIdsQuery->pluck('product_id');

//         // Step 2: Get movement totals for each product
//         $movementTotalsQuery = DB::table('stock_trackings')
//             ->select(
//                 'product_id',
//                 DB::raw('SUM(CASE WHEN movement = "IN" THEN quantity ELSE 0 END) as total_in'),
//                 DB::raw('SUM(CASE WHEN movement = "OUT" THEN quantity ELSE 0 END) as total_out')
//             )
//             ->whereIn('product_id', $productIds)
//             ->whereBetween('updated_at', [$date_from, $date_to])
//             ->groupBy('product_id');

//         if (!is_all_store()) {
//             $movementTotalsQuery->where('store_id', $default_store);
//         }

//         $movementTotals = $movementTotalsQuery->get()->keyBy('product_id');

//         // Step 3: Get current stock for each product
//         $currentStockQuery = DB::table('inv_current_stock')
//             ->select('product_id', DB::raw('SUM(quantity) as current_stock'))
//             ->whereIn('product_id', $productIds)
//             ->groupBy('product_id');

//         if (!is_all_store()) {
//             $currentStockQuery->where('store_id', $default_store);
//         }

//         $currentStocks = $currentStockQuery->get()->keyBy('product_id');

//         // Step 4: Get product details
//         $products = DB::table('products')
//             ->select('id', 'name', 'sku', 'description') // Add other fields as needed
//             ->whereIn('id', $productIds)
//             ->get()
//             ->keyBy('id');

//         // Step 5: Combine all data
//         $result = [];
//         foreach ($productIds as $productId) {
//             $movement = $movementTotals->get($productId);
//             $currentStock = $currentStocks->get($productId);
//             $product = $products->get($productId);

//             $result[] = [
//                 'product_id' => $productId,
//                 'product_name' => $product->name ?? null,
//                 'product_sku' => $product->sku ?? null,
//                 'product_description' => $product->description ?? null,
//                 'total_in' => $movement->total_in ?? 0,
//                 'total_out' => $movement->total_out ?? 0,
//                 'current_stock' => $currentStock->current_stock ?? 0,
//                 'net_movement' => ($movement->total_in ?? 0) - ($movement->total_out ?? 0),
//             ];
//         }

//         return response()->json([
//             'success' => true,
//             'data' => $result,
//             'summary' => [
//                 'total_products' => count($result),
//                 'date_range' => [
//                     'from' => $date_from,
//                     'to' => $date_to
//                 ],
//                 'store_id' => !is_all_store() ? $default_store : 'all_stores'
//             ]
//         ]);
//     }

//     return response()->json(['success' => false, 'message' => 'Invalid request']);
// }

    public function showOutStockOld( Request $request ) {

        if ( $request->ajax() ) {

            /*get default store*/
            $default_store = current_store_id();

            if ( is_all_store() ) {
                //return all
                $stock_tracking = StockTracking::whereBetween( 'updated_at', [ date( 'Y-m-d', strtotime( $request->date_from ) )
                , date( 'Y-m-d', strtotime( $request->date ) ) ] )
                ->where( 'movement', 'OUT' )
                ->get();
            } else {
                $stock_tracking = StockTracking::whereBetween( 'updated_at', [ date( 'Y-m-d', strtotime( $request->date_from ) )
                , date( 'Y-m-d', strtotime( $request->date ) ) ] )
                ->where( 'store_id', $default_store )
                ->where( 'movement', 'OUT' )
                ->get();
            }
            Log::info( 'Stock Tracking: ', $stock_tracking );
            //return product object
            foreach ( $stock_tracking as $tracking ) {
                $tracking->currentStock->product;
                $tracking->user;
                $tracking->date = date( 'd-m-Y', strtotime( $tracking->updated_at ) );
            }

            return json_decode( $stock_tracking, true );

        }

    }

}
