<?php

namespace App\Http\Controllers;

use App\AdjustmentReason;
use App\Category;
use App\CurrentStock;
use App\IssueReturn;
use App\Product;
use App\Setting;
use App\StockAdjustment;
use App\StockIssue;
use App\StockTracking;
use App\StockTransfer;
use App\StockCountSchedule;
use App\Store;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade as PDF;

ini_set('max_execution_time', 500);
set_time_limit(500);
ini_set('memory_limit', '512M');

class InventoryReportController extends Controller
{

    public function index()
    {

        $products = DB::table('product_ledger')
            ->join('inv_products', 'inv_products.id', '=', 'product_ledger.product_id')
            ->select('product_id', 'product_name', 'brand', 'pack_size', 'sales_uom')
            ->groupby(['product_id', 'product_name'])
            ->get();

        $store = Store::all();
        $category = Category::all();
        $adj_reasons = AdjustmentReason::all();

        return view('inventory_reports.index')->with([
            'products' => $products,
            'stores' => $store,
            'categories' => $category,
            'reasons' => $adj_reasons
        ]);
    }

    protected function reportOption(Request $request)
    {
    $pharmacy['name'] = Setting::where('id', 100)->value('value');
    $pharmacy['address'] = Setting::where('id', 106)->value('value');
    $pharmacy['phone'] = Setting::where('id', 107)->value('value');
    $pharmacy['email'] = Setting::where('id', 108)->value('value');
    $pharmacy['website'] = Setting::where('id', 109)->value('value');
    $pharmacy['logo'] = Setting::where('id', 105)->value('value');
    $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');

        switch ($request->report_option) {
            case 1:
                
                $request_store = $request->store_name ?? current_store_id();    
                $store_name = Store::where('id', $request_store)
                            ->first();
                $store = $store_name->name ?? current_store()->name;
                //current stock
                if ($request->category_name == null) {
                    $data = $this->currentStockByStoreReport($request_store);
                    if ($data == []) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $pdf = PDF::loadView( 'inventory_reports.current_stock_by_store_report_pdf',
                    compact( 'data', 'store', 'pharmacy' ) )
                    ->setPaper( 'a4', '' );
                    return $pdf->stream( 'current_stock_by_store_report.pdf' );
                } else {
                    $category_name = Category::where('id', $request->category_name)
                                ->first();
                    $category = $category_name->name;
                    $data = $this->currentStockReport($request_store, $request->category_name);
                    if ($data == []) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $pdf = PDF::loadView( 'inventory_reports.current_stock_report_pdf',
                    compact( 'data', 'store', 'category', 'pharmacy' ) )
                    ->setPaper( 'a4', '' );
                    return $pdf->stream( 'current_stock_report.pdf' );
                }
            case 2:
                $data = $this->productDetailReport($request->category_name_detail);
                if ($data == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                if ($request->category_name_detail != null) {
                    $pdf = PDF::loadView( 'inventory_reports.product_detail_report_pdf',
                    compact( 'data',  'pharmacy' ) )
                    ->setPaper( 'a4', '' );
                    return $pdf->stream( 'product_details_report.pdf' );
                } else {
                    $pdf = PDF::loadView( 'inventory_reports.product_detail1_report_pdf',
                    compact( 'data',  'pharmacy' ) )
                    ->setPaper( 'a4', '' );
                    return $pdf->stream( 'product_details_report.pdf' );
                }
            case 3:
                //product ledger
                $data = $this->productLedgerReport($request->product);
                if ($data == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $pdf = PDF::loadView( 'inventory_reports.product_ledger_report_pdf',
                compact( 'data',  'pharmacy' ) )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'product_ledger_report.pdf' );
            case 4:
                //expired product
                $data = $this->expiredProductReport();
                if ($data->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $pdf = PDF::loadView( 'inventory_reports.expiry_product_report_pdf',
                compact( 'data',  'pharmacy' ) )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'expiry_product.pdf' );
            case 5:
                //out of stock
                $data = $this->outOfStockReport();
                if ($data->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $pdf = PDF::loadView( 'inventory_reports.outofstock_report_pdf',
                compact( 'data',  'pharmacy' ) )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'outofstock_report.pdf' );
            case 6:
                //outgoing tracking report
                $data = $this->outgoingTrackingReport();
                if ($data->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $pdf = PDF::loadView( 'inventory_reports.outgoing_stocktracking_report_pdf',
                compact( 'data',  'pharmacy' ) )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'outgoing_stocktracking_report.pdf' );
            case 7:
                //stock adjustment report
                $dates = explode(" - ", $request->adjustment_date);
                $type = $request->stock_adjustment;
                $data = $this->stockAdjustmentReport($dates, $request->stock_adjustment, $request->stock_adjustment_reason);
                if ($data == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                if ($request->stock_adjustment_reason != null) {
                $pdf = PDF::loadView( 'inventory_reports.stock_adjustment_reason_report_pdf',
                compact( 'data',  'type', 'pharmacy' ) )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'stock_adjustment_reason_report.pdf' );
                } else {
                $pdf = PDF::loadView( 'inventory_reports.stock_adjustment_report_pdf',
                compact( 'data', 'type', 'pharmacy' ) )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'stock_adjustment_report.pdf' );
                }
            case 8:
                //stock issue report
                $dates = explode(" - ", $request->issue_date);
                if ($request->stock_issue == null) {

                    $data_og = $this->stockIssueReport($dates);
                    if ($data_og == []) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $view = 'inventory_reports.stock_issue_report_pdf';
                    $output = 'stock_issue_report.pdf';
                    $this->splitPdf($data_og, $view, $output);
                    break;
                } else {

                    //stock issue return report
                    if ($request->stock_issue == 2) {
                        $data_og = $this->stockIssueReturnReport($request->stock_issue, $dates);
                        if ($data_og->isEmpty()) {
                            return response()->view('error_pages.pdf_zero_data');
                        }
                        $view = 'inventory_reports.issue_return_report_pdf';
                        $output = 'issue_return_report.pdf';
                        $this->splitPdf($data_og, $view, $output);
                        break;
                    } else {
                        $data_og = $this->stockIssueReturnReport($request->stock_issue, $dates);
                        if ($data_og->isEmpty()) {
                            return response()->view('error_pages.pdf_zero_data');
                        }
                        $view = 'inventory_reports.issue_issued_report_pdf';
                        $output = 'issue_return_report.pdf';
                        $this->splitPdf($data_og, $view, $output);
                        break;
                    }
                }
            case 9:
                //stock transfer
                $dates = explode(" - ", $request->transfer_date);
                if ($request->stock_transfer == null) {
                    $data = $this->stockTransferReport($dates);
                    if ($data->isEmpty()) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $pdf = PDF::loadView( 'inventory_reports.stock_transfer_report_pdf',
                    compact( 'data', 'pharmacy' ) )
                    ->setPaper( 'a4', 'landscape' );
                    return $pdf->stream( 'stock_transfer_report.pdf' );
                } else {
                    $data = $this->stockTransferStatusReport($request->stock_transfer, $dates);
                    if ($data->isEmpty()) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $pdf = PDF::loadView( 'inventory_reports.stock_transfer_status_report_pdf',
                    compact( 'data', 'pharmacy' ) )
                    ->setPaper( 'a4', 'landscape' );
                    return $pdf->stream( 'stock_transfer_status_report.pdf' );
                }
            case 10:
                $data = $this->stockMaxLevel();
                if ($data == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                    $pdf = PDF::loadView( 'inventory_reports.stock_max_level_pdf',
                    compact( 'data', 'pharmacy' ) )
                    ->setPaper( 'a4', '' );
                    return $pdf->stream( 'stock_max_level.pdf' );
            case 11:
                $data = $this->stockMinLevel();
                if ($data == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                    $pdf = PDF::loadView( 'inventory_reports.stock_min_level_pdf',
                    compact( 'data', 'pharmacy' ) )
                    ->setPaper( 'a4', '' );
                    return $pdf->stream( 'stock_min_level.pdf' );
            default:
        }
    }

    private function currentStockByStoreReport($store)
    {
        $query = CurrentStock::with(['product', 'store'])
            ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
            ->join('inv_categories', 'inv_categories.id', '=', 'inv_products.category_id')
            ->select(
                'inv_current_stock.product_id',
                'inv_current_stock.store_id',
                'inv_products.name',
                'inv_products.brand',
                'inv_products.pack_size',
                'inv_products.sales_uom',
                'inv_categories.name as category',
                DB::raw('SUM(inv_current_stock.quantity) as total_quantity'),
            );

        if (!($store == 1 || $store === '1')) {
            $query->where('inv_current_stock.store_id', $store);
        }

        $current_stocks = $query->groupBy(
            ['inv_current_stock.product_id',
            ]
        )
        ->orderBy('inv_current_stock.product_id', 'asc')
        ->get();

        $results_data = [];

        foreach ($current_stocks as $current_stock) {
            array_push($results_data, [
                'product_id'   => $current_stock->product_id,
                'store'        => $current_stock->store->name ?? '',
                'name'         => $current_stock->name ?? '',
                'brand'        => $current_stock->brand ?? '',
                'pack_size'    => $current_stock->pack_size ?? '',
                'sales_uom'    => $current_stock->sales_uom ?? '',
                'category'     => $current_stock->category ?? '',
                'expiry_date'  => $current_stock->expiry_date,
                'quantity'     => $current_stock->total_quantity,
                'batch_number' => $current_stock->batch_number,
                'shelf_no'     => $current_stock->shelf_number
            ]);
        }

        return $results_data;
    }
    
    private function currentStockReport($store, $category)
    {
        $query = CurrentStock::join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
            ->join('inv_categories', 'inv_categories.id', '=', 'inv_products.category_id')
            ->select(
                'inv_current_stock.product_id',
                'inv_current_stock.store_id',
                'inv_products.name',
                'inv_products.brand',
                'inv_products.pack_size',
                'inv_products.sales_uom',
                'inv_categories.id as category_id',
                'inv_categories.name as category',
                DB::raw('SUM(inv_current_stock.quantity) as total_quantity')
            );

        if (!($store == 1 || $store === '1')) {
            $query->where('inv_current_stock.store_id', $store);
        }

        if (!($category == 0 || $category === '0')) {
            $query->where('inv_categories.id', $category);
        }

        $current_stocks = $query->groupBy(
            'inv_current_stock.product_id'
        )
        ->orderBy('inv_current_stock.product_id', 'asc')
        ->get();

        $results_data = [];

        foreach ($current_stocks as $current_stock) {
            array_push($results_data, [
                'product_id'   => $current_stock->product_id,
                'category'     => $current_stock->category,
                'name'         => $current_stock->name ?? '',
                'brand'        => $current_stock->brand ?? '',
                'pack_size'    => $current_stock->pack_size ?? '',
                'sales_uom'    => $current_stock->sales_uom ?? '',
                'expiry_date'  => $current_stock->expiry_date,
                'quantity'     => $current_stock->total_quantity,
                'batch_number' => $current_stock->batch_number,
                'shelf_no'     => $current_stock->shelf_number
            ]);
        }

        return $results_data;
    }
    private function productDetailReport($category)
    {
        $store_id = current_store_id();
        if (!is_all_store()) {
            if ($category != null) {
                $products = Product::join('inv_current_stock', 'inv_current_stock.product_id', '=', 'inv_products.id')
                            ->where('category_id', $category)
                            ->where('inv_current_stock.store_id', $store_id)
                            ->get();
            } else {
                $products = Product::join('inv_current_stock', 'inv_current_stock.product_id', '=', 'inv_products.id')
                            ->where('inv_current_stock.store_id', $store_id)
                            ->get();
            }
        }else{
            if ($category != null) {
                $products = Product::where('category_id', $category)
                            ->get();
            } else {
                $products = Product::all();
            }
        }
        $results_data = array();

        foreach ($products as $product) {
            array_push($results_data, array(
                'product_id' => $product->id,
                'name' => $product->name,
                'brand' => $product->brand,
                'pack_size' => $product->pack_size,
                'sales_uom' => $product->sales_uom,
                'category' => $product->category->name ?? ''
            ));
        }

        return $results_data;
    }

    private function productLedgerReport($product_id)
    {
        $store_id = current_store_id();
        $query = DB::table('stock_details')
            ->select('product_id')
            ->groupby('product_id');

        if (!is_all_store()) {
            $query->where('store_id', $store_id);
        }

        $current_stock = $query->get();

        $query2 = DB::table('product_ledger')
            ->join('inv_products', 'inv_products.id', '=', 'product_ledger.product_id')
            ->select('product_id', 'inv_products.name as product_name', 'inv_products.brand', 'inv_products.pack_size', 'inv_products.sales_uom', 'received', 'outgoing', 'method', 'date')
            ->where('product_id', '=', $product_id);
        
         if (!is_all_store()) {
            $query2->where('store_id', $store_id);
        }

        $product_ledger = $query2->get();

        $result = $this->sumProductFilterTotal($product_ledger, $current_stock);

        return $result;

    }

    protected function sumProductFilterTotal($ledger, $current_stock)
    {
        $total = 0;
        $toMainView = [];

        //check if the ledger has data
        if (empty($ledger)) {
            return [[
                'date' => '-',
                'name' => '-',
                'method' => '-',
                'received' => '-',
                'outgoing' => '-',
                'balance' => '-'
            ]];
        }

        // Group kwa date + method
        $grouped = [];
        foreach ($ledger as $key) {
            $groupKey = date('Y-m-d', strtotime($key->date)) . '_' . $key->method;

            if (!isset($grouped[$groupKey])) {
                $grouped[$groupKey] = [
                    'date' => date('Y-m-d', strtotime($key->date)),
                    'name' => $key->product_name . ' ' . ($key->brand ?? '') . ' ' . ($key->pack_size ?? '') . ($key->sales_uom ?? ''),
                    'method' => $key->method,
                    'received' => 0,
                    'outgoing' => 0,
                ];
            }

            $grouped[$groupKey]['received'] += $key->received;
            $grouped[$groupKey]['outgoing'] += $key->outgoing;
        }

        // Sort kwa tarehe ili balance ipangike vizuri
        usort($grouped, function ($a, $b) {
            return strtotime($a['date']) <=> strtotime($b['date']);
        });

        // Hesabu balance na tengeneza view
        foreach ($grouped as $row) {
            $total = $total + $row['received'] + $row['outgoing'];

            $toMainView[] = [
                'date' => $row['date'],
                'name' => $row['name'],
                'method' => $row['method'],
                'received' => $row['received'],
                'outgoing' => abs($row['outgoing']),
                'balance' => $total
            ];
        }

        return $toMainView;
    }

    private function expiredProductReport()
    {
        $store_id = current_store_id();
        $query = CurrentStock::where(DB::raw('date(expiry_date)'), '<', date('Y-m-d'))
            ->orderby('expiry_date', 'DESC');

        if (!is_all_store()) {
            $query->where('store_id', $store_id);
        }
        
        $expired_products = $query->get();
        
        return $expired_products;
    }

    private function outOfStockReport()
    {
        $store_id = current_store_id();
        $query = CurrentStock::where('quantity', 0)
            ->groupby('product_id');
        
        if(!is_all_store()) {
            $query->where('store_id', $store_id);
        }

        $out_of_stock = $query->get();

        return $out_of_stock;
    }

    private function outgoingTrackingReport() 
    {
        $store_id = current_store_id();
        $query = StockTracking::where('movement', 'OUT')
            ->with(['currentStock.product', 'user']); // hakikisha unaload relationships

        if (!is_all_store()) {
            $query->where('store_id', $store_id);
        }

        $outgoing = $query->get();

        // group by date, product_id, out_mode, user_id
        $grouped = $outgoing->groupBy(function ($item) {
            return date('Y-m-d', strtotime($item->updated_at)) . '_' .
                $item->currentStock->product->id . '_' .
                $item->out_mode . '_' .
                $item->user->id;
        });

        // convert grouped data into flat array with summed quantities
        $merged = $grouped->map(function ($rows) {
            $first = $rows->first();
            return (object) [
                'date' => date('Y-m-d', strtotime($first->updated_at)),
                'product' => $first->currentStock->product,
                'out_mode' => $first->out_mode,
                'user' => $first->user,
                'quantity' => $rows->sum('quantity'),
            ];
        })->values();

        return $merged;
    }

    private function stockAdjustmentReport($dates, $type, $reason)
    {            
        $start = date('Y-m-d', strtotime($dates[0]));
        $end = date('Y-m-d', strtotime($dates[1]));

        $query = StockAdjustment::with(['currentStock.product', 'user'])
            ->whereBetween(DB::raw('date(created_at)'), [$start, $end]);

        if (!is_all_store()) {
            $query->whereHas('currentStock', function ($q) {
                $q->where('store_id', current_store_id());
            });
        }
        
        if ($type) {
            $query->where('type', $type);
        }

        if ($reason) {
            $query->where('reason', $reason);
        }

        $adjustments = $query->get();
        
        $to_pdf = array();
        $total = 0;

        foreach ($adjustments as $adjustment) {
            $current_stock = CurrentStock::find($adjustment->stock_id);
            $sub_total = floatval($adjustment->quantity) *
                floatval(preg_replace('/[^\d.]/', '', $current_stock['unit_cost']));
            $total = $total + $sub_total;
            array_push($to_pdf, array(
                'product_id' => $adjustment->currentStock['product']['id'],
                'name' => ($adjustment->currentStock['product']['name'].' ' ?? '').
                    ($adjustment->currentStock['product']['brand'].' ' ?? '').
                    ($adjustment->currentStock['product']['pack_size'] ?? '').
                    ($adjustment->currentStock['product']['sales_uom'] ?? ''),
                'unit_cost' => $current_stock['unit_cost'],
                'quantity' => $adjustment->quantity,
                'type' => $adjustment->type,
                'reason' => $adjustment->reason,
                'adjusted_by' => $adjustment->user['name'],
                'date' => date('Y-m-d', strtotime($adjustment->created_at)),
                'sub_total' => $sub_total,
                'total' => $total,
                'dates' => $dates
            ));
        }
        return $to_pdf;
    }
    private function stockIssueReport($issue_date)
    {
        $to_pdf = array();
        $total_bp = 0;
        $total_sp = 0;

        $stock_issue = StockIssue::whereBetween(DB::raw('date(created_at)'),
            [date('Y-m-d', strtotime($issue_date[0])), date('Y-m-d', strtotime($issue_date[1]))])
            ->get();

        foreach ($stock_issue as $issue) {

            $buy_price_sub_total = floatval($issue->quantity) *
                floatval(preg_replace('/[^\d.]/', '', $issue->unit_cost));
            $total_bp = $total_bp + $buy_price_sub_total;

            $sell_price_sub_total = floatval($issue->quantity) *
                floatval(preg_replace('/[^\d.]/', '', $issue->sales_price));
            $total_sp = $total_sp + $sell_price_sub_total;

            array_push($to_pdf, array(
                'product_id' => $issue->currentStock['product']['id'],
                'name' => $issue->currentStock['product']['name'],
                'buy_price' => $issue->unit_cost,
                'sell_price' => $issue->sales_price,
                'issue_qty' => $issue->quantity,
                'sub_total' => $issue->sub_total,
                'issue_no' => $issue->issue_no,
                'issued_by' => $issue->user['name'],
                'issued_date' => date('Y-m-d', strtotime($issue->created_at)),
                'issued_to' => $issue->issueLocation['name'],
                'buy_price_sb' => $buy_price_sub_total,
                'sell_price_sb' => $sell_price_sub_total,
                'total_bp' => $total_bp,
                'total_sp' => $total_sp,
                'dates' => $issue_date
            ));
        }

        return $to_pdf;
    }

    private function stockIssueReturnReport($status, $dates)
    {
        if ($status == 2) {
            $issue_return = IssueReturn::all();
            return $issue_return;
        } else {
            $issues = StockIssue::leftJoin('inv_issue_returns', function ($join) {
                $join->on('inv_stock_issues.id', '=', 'inv_issue_returns.issue_id');
            })->where('status', $status)->get();
            return $issues;
        }

    }

    private function stockTransferReport($dates)
    {
        $store_id = current_store_id();
        $query = StockTransfer::whereBetween(DB::raw('date(created_at)'),
            [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))]);
            
        if (!is_all_store()) {
            $query->where(function ($q) use ($store_id) {
                $q->where('from_store', $store_id)
                ->orWhere('to_store', $store_id);
            });
        }

        $transfers = $query->get();
        foreach ($transfers as $transfer) {
            $transfer->from = $dates[0];
            $transfer->to = $dates[1];
        }

        return $transfers;
    }

    private function stockTransferStatusReport($status, $dates)
    {

        $store_id = current_store_id();
        if ($status === '1' || $status === 1) {
        $query = StockTransfer::whereBetween(DB::raw('date(created_at)'),
            [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'acknowledged')
            ->where('status', '!=', 'completed');
        }else if ($status === '2' || $status === 2) {
        $query = StockTransfer::whereBetween(DB::raw('date(created_at)'),
            [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
            ->where('status', '=', 'completed');
        }
        if (!is_all_store()) {
            $query->where(function ($q) use ($store_id) {
                $q->where('from_store', $store_id)
                    ->orWhere('to_store', $store_id);
            });
        }

        $transfers = $query->get();
        foreach ($transfers as $transfer) {
            $transfer->from = $dates[0];
            $transfer->to = $dates[1];
        }

        return $transfers;
    }

    private function stockMaxLevel()
    {
        $stock_max = [];
        $store_id = current_store_id();
        $query = CurrentStock::select('product_id', DB::raw('sum(quantity) as qty'))
            ->groupby('product_id');

        if (!is_all_store()) {
            $query->where('store_id', $store_id);
        }

        $stocks = $query->get();

        foreach ($stocks as $stock) {
            $product = Product::select('id', 'name', 'brand', 'pack_size', 'sales_uom', 'max_quantinty')
                ->where('id', $stock->product_id)
                ->where('max_quantinty', '<', $stock->qty)
                ->first();
            if ($product) {
                $product->qty = $stock->qty;
                $stock_max[] = $product;
            }

        }
        return $stock_max;
    }

    private function stockMinLevel()
    {
        $stock_max = [];
        $store_id = current_store_id();

        $query = CurrentStock::select('product_id', DB::raw('sum(quantity) as qty'))
            ->groupby('product_id');
        
        if (!is_all_store()) {
            $query->where('store_id', $store_id);
        }

        $stocks = $query->get();
        foreach ($stocks as $stock) {
            $product = Product::select('id', 'name', 'brand', 'pack_size', 'sales_uom', 'min_quantinty')
                ->where('id', $stock->product_id)
                ->where('min_quantinty', '>', $stock->qty)
                ->first();
            if ($product) {
                $product->qty = $stock->qty;
                $stock_max[] = $product;
            }

        }

        return $stock_max;

    }

    public function stockDiscrepancyReport()
    {
        $auditLogs = DB::table('stock_adjustment_logs')
            ->join('inv_products', 'stock_adjustment_logs.product_id', '=', 'inv_products.id')
            ->join('users', 'stock_adjustment_logs.created_by', '=', 'users.id')
            ->join('inv_stores', 'stock_adjustment_logs.store_id', '=', 'inv_stores.id')
            ->select(
                'stock_adjustment_logs.created_at as date',
                'inv_products.name as product_name',
                'stock_adjustment_logs.quantity_before_adjustment',
                'stock_adjustment_logs.adjustment_quantity',
                'stock_adjustment_logs.quantity_after_adjustment',
                'stock_adjustment_logs.adjustment_type',
                'stock_adjustment_logs.reason',
                'stock_adjustment_logs.notes',
                'users.name as created_by',
                'inv_stores.name as store_name'
            )
            ->where('stock_adjustment_logs.source', 'Daily Stock Count')
            ->orderBy('stock_adjustment_logs.created_at', 'desc')
            ->get();

        return view('inventory_reports.stock_discrepancy', compact('auditLogs'));
    }

    public function stockCountAnalytics()
    {
        // Total number of scheduled stock counts
        $totalSchedules = StockCountSchedule::count();

        // Breakdown of schedules by status
        $schedulesByStatus = StockCountSchedule::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $pendingSchedules = $schedulesByStatus->get('pending', 0);
        $completedSchedules = $schedulesByStatus->get('completed', 0);
        $cancelledSchedules = $schedulesByStatus->get('cancelled', 0);

        // Total number of stock adjustments related to stock counts
        $totalAdjustments = DB::table('stock_adjustment_logs')
            ->where('source', 'Daily Stock Count')
            ->count();

        // Breakdown of adjustments by type (increase/decrease)
        $adjustmentsByType = DB::table('stock_adjustment_logs')
            ->select('adjustment_type', DB::raw('SUM(adjustment_quantity) as total_quantity'))
            ->where('source', 'Daily Stock Count')
            ->groupBy('adjustment_type')
            ->pluck('total_quantity', 'adjustment_type');

        $increaseAdjustments = $adjustmentsByType->get('increase', 0);
        $decreaseAdjustments = $adjustmentsByType->get('decrease', 0);

        // You can add more complex analytics here, e.g., trends over time, top adjusted products, etc.

        return view('inventory_reports.stock_count_analytics', compact(
            'totalSchedules',
            'pendingSchedules',
            'completedSchedules',
            'cancelledSchedules',
            'totalAdjustments',
            'increaseAdjustments',
            'decreaseAdjustments'
        ));
    }

}
