<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\Sale;
use App\SalesDetail;
use App\Setting;
use App\Store;
use App\StockTracking;
use App\StockAdjustmentLog;
use App\Exports\DailyStockCountExport;
use App\Http\Controllers\StockAdjustmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

ini_set('max_execution_time', 500);
set_time_limit(500);
ini_set('memory_limit', '512M');

class DailyStockCountController extends Controller
{

    public function index()
    {
        if (!Auth()->user()->checkPermission('View Stock Count')) {
            abort(403, 'Access Denied');
        }

        return view('stock_management.daily_stock_count.index');

    }

  public function stockTaking()
{
    if (!Auth()->user()->checkPermission('View Stock Taking')) {
        abort(403, 'Access Denied');
    }

    $store_id = current_store_id();

    // Fetch all products with current stock (join to stock or inventory table)
    $query = DB::table('inv_products as p')
        ->leftJoin('inv_current_stock as s', 'p.id', '=', 's.product_id')
        ->select(
            'p.id',
            'p.name',
            'p.brand',
            'p.pack_size',
            'p.sales_uom',
            DB::raw('COALESCE(SUM(s.quantity), 0) as current_stock')
        )
        ->groupBy('p.id')
        ->orderBy('p.name');

        if (!is_all_store()) {
            $query->where('s.store_id', $store_id);
        }
        $products = $query->get();

    return view('stock_management.stock_taking.index', compact('products'));
}


public function processStockTaking(Request $request)
{
    $data = $request->validate([
        'items' => 'required|array',
        'items.*.product_id' => 'required|integer',
        'items.*.qoh' => 'required|numeric',
        'items.*.physical' => 'nullable|numeric',
    ]);
    $data['date'] = now();

    DB::beginTransaction();
    try {
        $adjustmentController = new StockAdjustmentController();
        $storeId = current_store_id();

        foreach ($data['items'] as $item) {
            $physical = $item['physical'] ?? $item['qoh'];
            $diff = $physical - $item['qoh'];

            if ($diff != 0) {
                // tafuta batch moja ya product hii
                $stock = CurrentStock::where('product_id', $item['product_id'])
                    ->where('store_id', $storeId)
                    ->first();

                if ($stock) {
                    // prepare fake request for adjustment controller
                    $adjRequest = new Request([
                        'stock_id' => $stock->id,
                        'product_id' => $item['product_id'],
                        'current_stock' => $item['qoh'],
                        'new_quantity' => $physical,
                        'reason' => 'Stock Taking Adjustment',
                        'from_type' => 'summary',
                    ]);

                    // call adjustment controller directly
                    $response = $adjustmentController->store($adjRequest);
                }
            }
        }

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Stock taking adjustments processed successfully']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
    public function fetchSalesWithStock(Request $request)
    {
        if (! $request->ajax()) {
            abort(400, 'AJAX only');
        }

        $date = $request->date;
        $default_store = current_store_id();

        // 1) Fetch sale_ids per date
        $saleIds = DB::table('sales')
            ->whereDate('date', $date)
            ->pluck('id');

        if ($saleIds->isEmpty()) {
            return response()->json(['items' => []]);
        }

        // 2) Fetch & aggregate sales_details (sum for every stock_id)
        $salesDetails = DB::table('sales_details')
            ->select('stock_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereIn('sale_id', $saleIds)
            ->groupBy('stock_id')
            ->get();

        if ($salesDetails->isEmpty()) {
            return response()->json(['items' => []]);
        }

        $stockIds = $salesDetails->pluck('stock_id')->unique();

        // 3) Fetch current stock rows
        $stockRows = DB::table('inv_current_stock')
            ->select('id as stock_id', 'product_id', 'store_id', 'quantity')
            ->whereIn('id', $stockIds)
            ->when(!is_all_store(), function ($q) use ($default_store) {
                $q->where('store_id', $default_store);
            })
            ->get();

        if ($stockRows->isEmpty()) {
            return response()->json(['items' => []]);
        }

        // 4) Aggregate sales per product_id
        $salesPerProduct = $salesDetails->map(function ($row) use ($stockRows) {
            $stock = $stockRows->firstWhere('stock_id', $row->stock_id);
            return $stock ? [
                'product_id' => $stock->product_id,
                'store_id'   => $stock->store_id,
                'sold'       => (float) $row->total_sold,
            ] : null;
        })->filter()
        ->groupBy('product_id')
        ->map(function ($group) {
            return [
                'product_id' => $group->first()['product_id'],
                'store_id'   => $group->first()['store_id'],
                'total_sold' => collect($group)->sum('sold'),
            ];
        });

        // 5) Fetch total stock per product_id (sum for all batches)
        $currentStock = DB::table('inv_current_stock')
            ->select('product_id', DB::raw('SUM(quantity) as total_stock'))
            ->when(!is_all_store(), function ($q) use ($default_store) {
                $q->where('store_id', $default_store);
            })
            ->whereIn('product_id', $salesPerProduct->pluck('product_id'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // 6) Fetch product details
        $products = DB::table('inv_products')
            ->whereIn('id', $salesPerProduct->pluck('product_id'))
            ->get()
            ->keyBy('id');

        // 7) Combine final items
        $items = $salesPerProduct->map(function ($row) use ($currentStock, $products) {
            $product = $products->get($row['product_id']);
            $stock   = $currentStock->get($row['product_id']);

            return [
                'product_id'    => $row['product_id'],
                'product'       => $product ? (array) $product : null,
                'total_sold'    => (float) $row['total_sold'],
                'current_stock' => $stock ? (float) $stock->total_stock : 0,
                'store_id'      => $row['store_id'],
            ];
        })->values();

        return response()->json(['items' => $items]);
    }
    public function showDailyStockFilter(Request $request)
    {

        if ($request->ajax()) {

            $data = $this->summation($request->date);

            //array_values remove named key
            return array_values($data);

        }

    }
    public function generateDailyStockCountPDF(Request $request)
    {

        $data = $this->summation($request->sale_date);
        $new_data = array_values($data);

        $view = 'stock_management.daily_stock_count.daily_stock_count';
        $output = 'daily_stock_count.pdf';
        $report_pdf = new InventoryReportController();
        $report_pdf->splitPdf($new_data, $view, $output);

    }
    public function processStockCountAdjustment(Request $request)
    {
        $request->validate([
            'adjustments' => 'required|array',
            'adjustments.*.product_id' => 'required|exists:inv_products,id',
            'adjustments.*.physical_stock' => 'required|numeric|min:0',
            'adjustments.*.qoh' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $store_id = Auth::user()->store->id ?? 1;
            $adjustmentReason = 'Stock Count Adjustment';

            foreach ($request->adjustments as $adjustmentData) {
                $productId = $adjustmentData['product_id'];
                $physicalStock = $adjustmentData['physical_stock'];
                $qoh = $adjustmentData['qoh'];

                $difference = $physicalStock - $qoh;

                if ($difference != 0) {
                    $currentStock = CurrentStock::where('product_id', $productId)
                                                ->where('store_id', $store_id)
                                                ->first();

                    if ($currentStock) {
                        $previousQuantity = $currentStock->quantity;
                        $newQuantity = $currentStock->quantity + $difference;

                        // Update current stock quantity
                        $currentStock->quantity = $newQuantity;
                        $currentStock->save();

                        // Log the adjustment
                        StockAdjustmentLog::create([
                            'current_stock_id' => $currentStock->id,
                            'user_id' => Auth::id(),
                            'store_id' => $store_id,
                            'previous_quantity' => $previousQuantity,
                            'new_quantity' => $newQuantity,
                            'adjustment_quantity' => abs($difference),
                            'adjustment_type' => $difference > 0 ? 'increase' : 'decrease',
                            'reason' => $adjustmentReason,
                            'notes' => 'Daily Stock Count Adjustment',
                            'reference_number' => 'DSC-ADJ-' . time()
                        ]);

                        // Also update StockTracking
                        StockTracking::create([
                            'product_id' => $productId,
                            'store_id' => $store_id,
                            'quantity' => $difference,
                            'tracking_type' => 'stock_count_adjustment',
                            'tracking_id' => $currentStock->id, // Consider linking to StockAdjustmentLog ID if more appropriate
                            'user_id' => Auth::id(),
                            'description' => 'Stock count adjustment from physical count'
                        ]);

                    } else {
                        // Handle case where current stock entry doesn't exist for the product in this store
                        // This might mean adding a new stock entry if physical stock is > 0 and no record exists.
                        // For now, we will just log a warning.
                        Log::warning('No existing stock record for product ID ' . $productId . ' in store ' . $store_id . '. Cannot adjust stock.');
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Stock adjustments processed successfully.']);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error processing stock count adjustment: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return response()->json(['success' => false, 'message' => 'Error processing stock adjustments: ' . $e->getMessage()], 500);
        }
    }
    public function exportDailyStockCount(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $data = $this->summation($date);

        // The data needs to be formatted as an array of arrays for the export class
        $export_data = [];
        foreach ($data as $item) {
            $export_data[] = [
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'brand' => $item['brand'] ?? 'N/A',
                'pack_size' => $item['pack_size'] ?? 'N/A',
                'quantity_sold' => $item['quantity_sold'],
                'quantity_on_hand' => $item['quantity_on_hand'],
                'physical_stock' => '', // Placeholder, as this is entered on the client side
                'difference' => '',    // Placeholder
            ];
        }

        return Excel::download(new DailyStockCountExport($export_data), 'daily_stock_count_' . $date . '.xlsx');
    }

}
