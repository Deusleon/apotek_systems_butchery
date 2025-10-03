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
        if (!Auth()->user()->checkPermission('View Outgoing Stock')) {
            abort(403, 'Access Denied');
        }
        return view('stock_management.out_going_stock.index');
    }

//  public function showOutStock(Request $request)
// {
//     if (! $request->ajax()) {
//         abort(400, 'AJAX only');
//     }

//     $from = \Carbon\Carbon::parse($request->input('date_from'))->startOfDay();
//     $to   = \Carbon\Carbon::parse($request->input('date_to'))->endOfDay();

//     $default_store = current_store_id();
//     $useStoreFilter = !is_all_store();

//     // ----------------------------
//     // 1) Fetch stock_tracking with OUT movements
//     // ----------------------------
//     $stockTracking = StockTracking::whereBetween('updated_at', [$from->toDateTimeString(), $to->toDateTimeString()])
//         ->where('movement', 'OUT')
//         ->when($useStoreFilter, function($q) use ($default_store) {
//             return $q->where('store_id', $default_store);
//         })
//         ->get();

//     if ($stockTracking->isEmpty()) {
//         return response()->json([
//             'detailed' => [],
//             'summary'  => [],
//         ]);
//     }

//     // ----------------------------
//     // 2) Prefetch product details
//     // ----------------------------
//     $productIds = $stockTracking->pluck('product_id')->unique()->all();
//     $products = DB::table('inv_products')
//         ->whereIn('id', $productIds)
//         ->get()
//         ->keyBy('id');

//     // ----------------------------
//     // 3) Aggregate current_stock per product_id (sum across all batches)
//     // ----------------------------
//     $currentStockByProductQuery = DB::table('inv_current_stock')
//         ->select('product_id', DB::raw('SUM(quantity) as total_stock'))
//         ->whereIn('product_id', $productIds);

//     if ($useStoreFilter) {
//         $currentStockByProductQuery->where('store_id', $default_store);
//     }

//     $currentStockByProduct = $currentStockByProductQuery
//         ->groupBy('product_id')
//         ->get()
//         ->keyBy('product_id');

//     // ----------------------------
//     // 4) DETAILED: batch-wise (stock_id)
//     // ----------------------------
//     $stockDetailed = $stockTracking->map(function ($item) use ($currentStockByProduct, $products) {
//     $stockId   = $item->stock_id;
//     $productId = $item->product_id;

//     $currentStockBatchRow = DB::table('inv_current_stock')
//         ->where('id', $stockId)
//         ->first();

//     $currentStockQty = $currentStockBatchRow ? (float) $currentStockBatchRow->quantity : 0;
//     $batchNumber     = $currentStockBatchRow ? $currentStockBatchRow->batch_number : null;

//     $currentStockProduct = isset($currentStockByProduct[$productId]) 
//         ? (float) $currentStockByProduct[$productId]->total_stock 
//         : 0;

//     $product = isset($products[$productId]) ? (array) $products[$productId] : null;

//     return [
//         'stock_id'               => $stockId,
//         'product_id'             => $productId,
//         'batch_number'           => $batchNumber,
//         'product_name'           => $product['name'] ?? null,
//         'out_total'              => (float) $item->quantity,
//         'current_stock_batch'    => $currentStockQty,
//         'current_stock_product'  => $currentStockProduct,
//         'store_id'               => $item->store_id ?? null,
//         'product'                => $product,
//         'movement'               => [
//             'id'         => $item->id,
//             'qty'        => (float) $item->quantity,
//             'date'       => $item->updated_at,
//             'out_mode'   => $item->out_mode,
//             'barcode'    => $item->barcode,
//             'created_by' => $item->created_by ?? $item->updated_by ?? null,
//         ],
//     ];
// })->values();

//     // ----------------------------
//     // 5) SUMMARY: product-wise
//     // ----------------------------
//     $groupedByProduct = $stockTracking->groupBy('product_id')->map(function ($rows) use ($currentStockByProduct, $products) {
//         $productId = $rows->first()->product_id;
//         $product = isset($products[$productId]) ? (array) $products[$productId] : null;

//         return [
//             'product_id'    => $productId,
//             'product_name'  => $product['name'] ?? null,
//             'out_total'     => (float) $rows->sum('quantity'),
//             'current_stock' => isset($currentStockByProduct[$productId])
//                                 ? (float) $currentStockByProduct[$productId]->total_stock
//                                 : 0,
//             'out_movements' => $rows->map(function ($item) {
//                 return [
//                     'stock_id' => $item->stock_id,
//                     'qty'      => (float) $item->quantity,
//                     'date'     => $item->updated_at,
//                     'out_mode' => $item->out_mode,
//                 ];
//             })->values(),
//             'product'       => $product,
//         ];
//     })->values();

//     return response()->json([
//         'detailed' => $stockDetailed,
//         'summary'  => $groupedByProduct,
//     ]);
// }

public function showOutStock(Request $request)
{

    $from = \Carbon\Carbon::parse($request->input('date_from'))->startOfDay();
    $to   = \Carbon\Carbon::parse($request->input('date_to'))->endOfDay();

    $default_store = current_store_id();
    $useStoreFilter = !is_all_store();

    // ----------------------------
    // 1) Fetch stock_tracking with OUT movements and eager-load relations
    // ----------------------------
    $stockQuery = StockTracking::with(['user', 'product', 'currentStock'])
        ->whereBetween('updated_at', [$from->toDateTimeString(), $to->toDateTimeString()])
        ->where('movement', 'OUT');

    if ($useStoreFilter) {
        $stockQuery->where('store_id', $default_store);
    }

    $stockQuery->orderByDesc('id');

    $stockTracking = $stockQuery->get();

    if ($stockTracking->isEmpty()) {
        return response()->json([
            'detailed' => [],
            'summary'  => [],
        ]);
    }

    // ----------------------------
    // 2) Aggregate current_stock per product_id (sum across all batches)
    // ----------------------------
    $productIds = $stockTracking->pluck('product_id')->unique()->all();

    $currentStockByProductQuery = DB::table('inv_current_stock')
        ->select('product_id', DB::raw('SUM(quantity) as total_stock'))
        ->whereIn('product_id', $productIds);

    if ($useStoreFilter) {
        $currentStockByProductQuery->where('store_id', $default_store);
    }

    $currentStockByProduct = $currentStockByProductQuery
        ->groupBy('product_id')
        ->get()
        ->keyBy('product_id'); // keyed by product_id for quick lookup

    // ----------------------------
    // 3) DETAILED: batch-wise (stock_id) using relationships
    // ----------------------------
    $stockDetailed = $stockTracking->map(function ($item) use ($currentStockByProduct) {
        $stockId   = $item->stock_id;
        $productId = $item->product_id;

        // Prefer using eager-loaded currentStock relation if available
        $currentStockBatchRow = $item->currentStock ?? null;

        // fallback: if relation empty, try DB lookup (very rare because of eager load)
        if (! $currentStockBatchRow) {
            $currentStockBatchRow = DB::table('inv_current_stock')
                ->where('id', $stockId)
                ->first();
        }

        $currentStockQty = $currentStockBatchRow ? (float) ($currentStockBatchRow->quantity ?? 0) : 0;
        $batchNumber     = $currentStockBatchRow ? ($currentStockBatchRow->batch_number ?? null) : null;

        $currentStockProduct = isset($currentStockByProduct[$productId])
            ? (float) $currentStockByProduct[$productId]->total_stock
            : 0;

        // product from relation (eager loaded)
        $product = $item->product ? (array) $item->product->toArray() : null;

        // created_by info: use relation user() which maps created_by
        $createdById = $item->created_by ?? $item->updated_by ?? null;
        $createdByName = $item->user ? ($item->user->name ?? null) : null;

        return [
            'stock_id'               => $stockId,
            'product_id'             => $productId,
            'batch_number'           => $batchNumber,
            'product_name'           => $product['name'] ?? null,
            'out_total'              => (float) $item->quantity,
            'current_stock_batch'    => $currentStockQty,
            'current_stock_product'  => $currentStockProduct,
            'store_id'               => $item->store_id ?? null,
            'product'                => $product,
            'movement'               => [[
                'id'              => $item->id,
                'qty'             => (float) $item->quantity,
                'date'            => $item->updated_at,
                'out_mode'        => $item->out_mode,
                'barcode'         => $item->barcode ?? null,
                'created_by'      => $createdByName,
            ]],
        ];
    })->values();

    // ----------------------------
    // 4) SUMMARY: product-wise (aggregate from detailed)
    // ----------------------------
    $groupedByProduct = $stockDetailed->groupBy('product_id')->map(function ($rows, $productId) {
        return [
            'product_id'    => $productId,
            'product_name'  => $rows->first()['product_name'] ?? null,
            'out_total'     => (float) $rows->sum('out_total'),
            'current_stock' => $rows->first()['current_stock_product'] ?? 0,
            'out_movements' => $rows->map(function ($r) {
                return [
                    'stock_id' => $r['stock_id'],
                    'qty'      => (float) $r['out_total'],
                    'date'     => $r['movement']['date'] ?? null,
                    'out_mode' => $r['movement']['out_mode'] ?? null,
                ];
            })->values(),
            'product'       => $rows->first()['product'] ?? null,
        ];
    })->values();

    return response()->json([
        'detailed' => $stockDetailed,
        'summary'  => $groupedByProduct,
    ]);
}

}
