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
                $store_name = Store::where('id', $request->store_name)
                            ->first();
                $store = $store_name->name;
                //current stock
                if ($request->category_name == null) {
                    $data = $this->currentStockByStoreReport($request->store_name);
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
                    $data = $this->currentStockReport($request->store_name, $request->category_name);
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
                $data_og = $this->stockAdjustmentReport($dates, $request->stock_adjustment, $request->stock_adjustment_reason);
                if ($data_og == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                if ($request->stock_adjustment_reason != null) {
                    $view = 'inventory_reports.stock_adjustment_reason_report_pdf';
                    $output = 'stock_adjustment_reason_report.pdf';
                } else {
                    $view = 'inventory_reports.stock_adjustment_report_pdf';
                    $output = 'stock_adjustment_report.pdf';
                }

                return $this->splitPdf($data_og, $view, $output);
                
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
                    return $this->splitPdf($data_og, $view, $output);

                    } else {

                    //stock issue return report
                    if ($request->stock_issue == 2) {
                        $data_og = $this->stockIssueReturnReport($request->stock_issue, $dates);
                        if ($data_og->isEmpty()) {
                            return response()->view('error_pages.pdf_zero_data');
                        }
                        $view = 'inventory_reports.issue_return_report_pdf';
                        $output = 'issue_return_report.pdf';
                        return $this->splitPdf($data_og, $view, $output);
    
                        } else {
                        $data_og = $this->stockIssueReturnReport($request->stock_issue, $dates);
                        if ($data_og->isEmpty()) {
                            return response()->view('error_pages.pdf_zero_data');
                        }
                        $view = 'inventory_reports.issue_issued_report_pdf';
                        $output = 'issue_return_report.pdf';
                        return $this->splitPdf($data_og, $view, $output);

                    }
                }
            case 9:
                //stock transfer
                $dates = explode(" - ", $request->transfer_date);
                if ($request->stock_transfer == null) {
                    $data_og = $this->stockTransferReport($dates);
                    if ($data_og->isEmpty()) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $view = 'inventory_reports.stock_transfer_report_pdf';
                    $output = 'stock_transfer_report.pdf';
                    return $this->splitPdf($data_og, $view, $output);
                    
                } else {
                    $data_og = $this->stockTransferStatusReport($request->stock_transfer, $dates);
                    if ($data_og->isEmpty()) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $view = 'inventory_reports.stock_transfer_status_report_pdf';
                    $output = 'stock_transfer_status_report.pdf';
                    return $this->splitPdf($data_og, $view, $output);
                    
                }
            case 10:
                $data_og = $this->stockMaxLevel();
                if ($data_og == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $view = 'inventory_reports.stock_max_level_pdf';
                $output = 'stock_max_level.pdf';
                return $this->splitPdf($data_og, $view, $output);
                
            case 11:
                $data_og = $this->stockMinLevel();
                if ($data_og == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $view = 'inventory_reports.stock_min_level_pdf';
                $output = 'stock_min_level.pdf';
                return $this->splitPdf($data_og, $view, $output);

            default:
        }
    }

    // private function currentStockByStoreReport($store)
    // {
    //     $query = CurrentStock::with(['product', 'store'])
    //             ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
    //             ->join('inv_categories', 'inv_categories.id', '=', 'inv_products.category_id')
    //             ->select('inv_current_stock.*', 'inv_products.*','inv_categories.name as category');

    //     if (!($store == 1 || $store === '1')) {
    //         $query->where('store_id', $store);
    //     }
        
    //     $current_stocks = $query->orderBy('product_id', 'asc')->get();
    //     $results_data = array();
    //     // dd($current_stocks);

    //     foreach ($current_stocks as $current_stock) {
    //         array_push($results_data, array(
    //             'stock_id' => $current_stock->id,
    //             'product_id' => $current_stock->product->id ?? '',
    //             'store' => $current_stock->store->name ?? '',
    //             'name' => $current_stock->product->name ?? '',
    //             'brand' => $current_stock->product->brand ?? '',
    //             'pack_size' => $current_stock->product->pack_size ?? '',
    //             'sales_uom' => $current_stock->product->sales_uom ?? '',
    //             'category' => $current_stock->category ?? '',
    //             'expiry_date' => $current_stock->expiry_date,
    //             'quantity' => $current_stock->quantity,
    //             'batch_number' => $current_stock->batch_number,
    //             'shelf_no' => $current_stock->shelf_number
    //         ));
    //     }

    //     return $results_data;
    // }

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
    
    // private function currentStockReport($store, $category)
    // {
    //     $query = CurrentStock::with(['product', 'store']);

    //     if (!($store == 1 || $store === '1')) {
    //         $query->where('store_id', $store);
    //     }
        
    //     $current_stocks = $query->orderBy('product_id', 'asc')->get();
    //     $categories = Category::where('id', $category)->get();
    //     $results_data = array();

    //     foreach ($current_stocks as $current_stock) {
    //         foreach ($categories as $category) {
    //             if ($category->id == $current_stock->product->category_id ?? '') {
    //                 array_push($results_data, array(
    //                     'stock_id' => $current_stock->id,
    //                     'product_id' => $current_stock->product->id ?? '',
    //                     'category' => $category->name,
    //                     'name' => $current_stock->product->name ?? '',
    //                     'brand' => $current_stock->product->brand ?? '',
    //                     'pack_size' => $current_stock->product->pack_size ?? '',
    //                     'sales_uom' => $current_stock->product->sales_uom ?? '',
    //                     'expiry_date' => $current_stock->expiry_date,
    //                     'quantity' => $current_stock->quantity,
    //                     'batch_number' => $current_stock->batch_number,
    //                     'shelf_no' => $current_stock->shelf_number
    //                 ));
    //             }
    //         }
    //     }

    //     return $results_data;

    // }
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
                $products = Product::join('inv_current_stock', 'inv_current_stock.product_id', '=', 'products.id')
                            ->where('category_id', $category)
                            ->where('inv_current_stock.store_id', $store_id)
                            ->get();
            } else {
                $products = Product::join('inv_current_stock', 'inv_current_stock.product_id', '=', 'products.id')
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
        $current_stock = DB::table('stock_details')
            ->select('product_id')
            ->groupby('product_id')
            ->get();
        $grouped_result = array();

        $product_ledger = DB::table('product_ledger')
            ->select('product_id', 'product_name', 'received', 'outgoing', 'method', 'date')
            ->where('product_id', '=', $product_id)
            ->get();

        $ungrouped_result = $this->sumProductFilterTotal($product_ledger, $current_stock);

        return $ungrouped_result;

    }

    protected function sumProductFilterTotal($ledger, $current_stock)
    {
        $total = 0;
        $toMainView = array();

        //check if the ledger has data
        if (!isset($ledger[0])) {
            //data not found empty search
            array_push($toMainView, array(
                'date' => '-',
                'name' => '-',
                'method' => '-',
                'received' => '-',
                'outgoing' => '-',
                'balance' => '-'
            ));
        }


        //loop and perform addition on ins and outs to get the balance
        foreach ($current_stock as $value) {

            foreach ($ledger as $key) {


                if ($value->product_id == $key->product_id) {

                    $total = $total + $key->received + $key->outgoing; // 0 + -20 + 0

                    if ($key->date == null) {

                        array_push($toMainView, array(
                            'date' => date('Y-m-d', strtotime($key->date)),
                            'name' => $key->product_name,
                            'method' => $key->method,
                            'received' => $key->received,
                            'outgoing' => $key->outgoing,
                            'balance' => $total
                        ));

                    } else {

                        array_push($toMainView, array(
                            'date' => date('Y-m-d', strtotime($key->date)),
                            'name' => $key->product_name,
                            'method' => $key->method,
                            'received' => $key->received,
                            'outgoing' => $key->outgoing,
                            'balance' => $total
                        ));

                    }

                }

            }

        }

        return $toMainView;

    }

    private function expiredProductReport()
    {

        $expired_products = CurrentStock::where(DB::raw('date(expiry_date)'), '<', date('Y-m-d'))
            ->orderby('expiry_date', 'DESC')
            ->get();

        return $expired_products;
    }

    private function outOfStockReport()
    {
        $out_of_stock = CurrentStock::where('quantity', 0)
            ->groupby('product_id')
            ->get();

        return $out_of_stock;
    }

    private function outgoingTrackingReport()
    {
        $outgoing = StockTracking::where('movement', 'OUT')->get();
        return $outgoing;
    }

    private function stockAdjustmentReport($dates, $type, $reason)
    {

        if ($reason != null) {
            $adjustments = StockAdjustment::whereBetween(DB::raw('date(created_at)'),
                [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                ->where('type', $type)
                ->where('reason', $reason)
                ->get();
        } else {
            $adjustments = StockAdjustment::whereBetween(DB::raw('date(created_at)'),
                [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                ->where('type', $type)
                ->get();
        }

        $to_pdf = array();
        $total = 0;

        foreach ($adjustments as $adjustment) {
            $current_stock = CurrentStock::find($adjustment->stock_id);
            $sub_total = floatval($adjustment->quantity) *
                floatval(preg_replace('/[^\d.]/', '', $current_stock['unit_cost']));
            $total = $total + $sub_total;
            array_push($to_pdf, array(
                'product_id' => $adjustment->currentStock['product']['id'],
                'name' => $adjustment->currentStock['product']['name'],
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
//        max(array_column($to_pdf, 'total'));
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
        $transfers = StockTransfer::whereBetween(DB::raw('date(created_at)'),
            [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
            ->get();

        foreach ($transfers as $transfer) {
            $transfer->from = $dates[0];
            $transfer->to = $dates[1];
        }

        return $transfers;
    }

    private function stockTransferStatusReport($status, $dates)
    {
        $transfers = StockTransfer::whereBetween(DB::raw('date(created_at)'),
            [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
            ->where('status', $status)
            ->get();

        foreach ($transfers as $transfer) {
            $transfer->from = $dates[0];
            $transfer->to = $dates[1];
        }

        return $transfers;
    }

    private function stockMaxLevel()
    {
        $stock_max = [];

        $stocks = CurrentStock::select('product_id', DB::raw('sum(quantity) as qty'))
            ->groupby('product_id')
            ->get();

        foreach ($stocks as $stock) {
            $product = Product::select('id', 'name', 'max_quantinty')
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

        $stocks = CurrentStock::select('product_id', DB::raw('sum(quantity) as qty'))
            ->groupby('product_id')
            ->get();

        foreach ($stocks as $stock) {
            $product = Product::select('id', 'name', 'min_quantinty')
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


        /**
     * Unified PDF generator used by multiple report cases.
     *
     * @param mixed  $data_og  Array|Collection|Eloquent collection of data to pass to blade
     * @param string $view     Blade view path (e.g. 'inventory_reports.stock_issue_report_pdf')
     * @param string $output   Filename to stream (e.g. 'stock_issue_report.pdf')
     * @param array|null $pharmacy Optional pharmacy meta (if null, method will build from settings)
     * @return \Illuminate\Http\Response
     */
    public function splitPdf($data_og, $view, $output, $pharmacy = null)
    {
        // Build pharmacy metadata if not supplied
        if ($pharmacy === null) {
            $pharmacy = [];
            $pharmacy['name'] = Setting::where('id', 100)->value('value');
            $pharmacy['address'] = Setting::where('id', 106)->value('value');
            $pharmacy['phone'] = Setting::where('id', 107)->value('value');
            $pharmacy['email'] = Setting::where('id', 108)->value('value');
            $pharmacy['website'] = Setting::where('id', 109)->value('value');
            $pharmacy['logo'] = Setting::where('id', 105)->value('value');
            $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');
        }

        // Normalize data variables for blade templates (some blades expect 'data', others 'data_og')
        $params = [
            'pharmacy' => $pharmacy,
            'data'     => $data_og,
            'data_og'  => $data_og,
        ];

        // If the dataset is empty return the pdf_zero_data view (keeps behaviour consistent)
        // Works for arrays and Eloquent Collections
        if ((is_array($data_og) && count($data_og) === 0)
            || ($data_og instanceof \Illuminate\Support\Collection && $data_og->isEmpty())
            || ($data_og instanceof \Illuminate\Database\Eloquent\Collection && $data_og->isEmpty())
        ) {
            return response()->view('error_pages.pdf_zero_data');
        }

        // Generate and stream pdf
        $pdf = PDF::loadView($view, $params)->setPaper('a4', '');
        return $pdf->stream($output);
    }


}
