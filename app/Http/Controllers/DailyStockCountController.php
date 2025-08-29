<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\Sale;
use App\SalesDetail;
use App\Setting;
use App\Store;
use App\Exports\DailyStockCountExport;
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

        $today = date('Y-m-d');
        $to_index = $this->summation($today);

        return view('stock_management.daily_stock_count.index')->with([
            'products' => array_values($to_index),
            'today' => $today
        ]);

    }

    public function summation($specific_date)
    {
        /*get default store*/
        $default_store_id = current_store_id();

        $sales_per_date = Sale::where(DB::raw('date(date)'), $specific_date)->get();

        if (is_all_store()){            
            $current_stocks = CurrentStock::select(DB::raw('product_id'),
                DB::raw('sum(quantity) as quantity_on_hand'), 'id')
                ->groupby('product_id')
                ->get();
        }else{
            $current_stocks = CurrentStock::select(DB::raw('product_id'),
                DB::raw('sum(quantity) as quantity_on_hand'), 'id')
                ->where('store_id', $default_store_id)
                ->groupby('product_id')
                ->get();
        }

        $products = array();
        $dailyStockCount = array();

        /*sale per day*/
        foreach ($sales_per_date as $sale_per_date) {
            /*check for that sale id*/
            if (is_all_store()) {
                $sale_per_date_details = SalesDetail::select('sales_details.quantity', 'stock_id')
                    ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id')
                    ->where('sale_id', $sale_per_date->id)
                    ->get();
            }else{
                $sale_per_date_details = SalesDetail::select('sales_details.quantity', 'stock_id')
                    ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id')
                    ->where('store_id', $default_store_id)
                    ->where('sale_id', $sale_per_date->id)
                    ->get();
            }
            foreach ($sale_per_date_details as $sale_per_date_detail) {
                array_push($products, array(
                    'product_id' => $sale_per_date_detail->currentStock['product_id'],
                    'product_name' => $sale_per_date_detail->currentStock['product']['name'] ?? 'N/A',
                    'brand' => $sale_per_date_detail->currentStock['product']['brand'] ?? 'N/A',
                    'pack_size' => $sale_per_date_detail->currentStock['product']['pack_size'] ?? 'N/A',
                    'quantity_sold' => $sale_per_date_detail->quantity,
                ));

            }
        }

        //loop the results to sum
        foreach ($products as $ar) {
            foreach ($ar as $k => $v) {
                if (array_key_exists($v, $dailyStockCount)) {
                    $dailyStockCount[$v]['quantity_sold'] = $dailyStockCount[$v]['quantity_sold'] + $ar['quantity_sold'];
                    foreach ($current_stocks as $value) {
                        if ($dailyStockCount[$v]['product_id'] == $value->product_id) {
                            $dailyStockCount[$v]['quantity_on_hand'] = $value->quantity_on_hand;
                        }
                    }
                } else if ($k == 'product_id') {
                    $dailyStockCount[$v] = $ar;
                    foreach ($current_stocks as $value) {
                        if ($dailyStockCount[$v]['product_id'] == $value->product_id) {
                            $dailyStockCount[$v]['quantity_on_hand'] = $value->quantity_on_hand;
                        }
                    }
                }
            }
        }

        return $dailyStockCount;

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
                    // Find the current stock entry for the product in the user's store
                    // For simplicity, we are assuming one current stock entry per product per store.
                    // In a more complex scenario, you might need to adjust specific batches.
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
