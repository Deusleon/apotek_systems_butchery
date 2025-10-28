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
