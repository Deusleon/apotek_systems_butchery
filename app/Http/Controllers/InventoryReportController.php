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
use App\SalesDetail;
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
        if (!Auth()->user()->checkPermission('View Inventory Reports')) {
            abort(403, 'Access Denied');
        }
        $products = DB::table('product_ledger')
            ->join('inv_products', 'inv_products.id', '=', 'product_ledger.product_id')
            ->select('product_id', 'product_name', 'brand', 'pack_size', 'sales_uom')
            ->groupby(['product_id', 'product_name'])
            ->orderBy('product_name', 'asc')
            ->get();

        $store = Store::all();
        $category = Category::orderBy('name', 'asc')->get();
        $adj_reasons = AdjustmentReason::orderBy('reason', 'asc')->get();
        $expireSettings = Setting::where('id', 123)->value('value');
        $expireEnabled = $expireSettings === 'YES';

        return view('inventory_reports.index')->with([
            'products' => $products,
            'stores' => $store,
            'categories' => $category,
            'reasons' => $adj_reasons,
            'expireEnabled' => $expireEnabled
        ]);
    }

    protected function reportOption(Request $request)
    {
        if (!Auth()->user()->checkPermission('View Inventory Reports')) {
            abort(403, 'Access Denied');
        }
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
            case 12:
                $request_store = $request->store_name ?? current_store_id();    
                $store_name = Store::where('id', $request_store)
                            ->first();
                $store = $store_name->name ?? current_store()->name;
                //current stock
                if ($request->category_name == null) {
                    $data = $this->currentStockByStoreDeailedReport($request_store, 0);
                    if ($data == []) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $pdf = PDF::loadView( 'inventory_reports.current_stock_by_store_detailed_report_pdf',
                    compact( 'data', 'store', 'pharmacy' ) )
                    ->setPaper( 'a4', '' );
                    return $pdf->stream( 'current_stock_by_store_detailed_report.pdf' );
                } else {
                    $category_name = Category::where('id', $request->category_name)
                                ->first();
                    $category = $category_name->name;
                    $data = $this->currentStockByStoreDeailedReport($request_store, $request->category_name);
                    if ($data == []) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $pdf = PDF::loadView( 'inventory_reports.current_stock_detailed_report_pdf',
                    compact( 'data', 'store', 'category', 'pharmacy' ) )
                    ->setPaper( 'a4', '' );
                    return $pdf->stream( 'current_stock_detailed_report.pdf' );
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
                return $pdf->stream( 'expiry_product_report.pdf' );
            case 13:
                //products expire date
                $data = $this->productsExpireDateReport();
                if ($data->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $pdf = PDF::loadView( 'inventory_reports.product_expire_date_report_pdf',
                compact( 'data',  'pharmacy' ) )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'products_expire_date_report.pdf' );
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
            case 14:
                //outgoing tracking summary report
                $data = $this->outgoingTrackingSummaryReport();
                if ($data->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $pdf = PDF::loadView( 'inventory_reports.outgoing_stocktracking_summary_report_pdf',
                compact( 'data',  'pharmacy' ) )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'outgoing_stocktracking_summary_report.pdf' );
            case 15:
                //outgoing tracking summary report
                $request_store = $request->store_name ?? current_store_id();    
                $store_name = Store::where('id', $request_store)
                            ->first();
                $store = $store_name->name ?? current_store()->name;
                $data = $this->fastMovingReport();
                if ($data->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $pdf = PDF::loadView( 'inventory_reports.fast_moving_report_pdf',
                compact( 'data', 'store', 'pharmacy' ) )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'fast_moving_report.pdf' );
            case 16:
                //outgoing tracking summary report
                $request_store = $request->store_name ?? current_store_id();    
                $store_name = Store::where('id', $request_store)
                            ->first();
                $store = $store_name->name ?? current_store()->name;
                $data = $this->deadStockReport();
                if ($data->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $pdf = PDF::loadView( 'inventory_reports.dead_stock_report_pdf',
                compact( 'data', 'store', 'pharmacy' ) )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'dead_stock_report.pdf' );
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

                    $data = $this->stockIssueReport($dates);
                    if ($data == []) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                $pdf = PDF::loadView( 'inventory_reports.stock_issue_report_pdf',
                compact( 'data', 'pharmacy' ) )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'stock_issue_report.pdf' );
                } else {

                    //stock issue return report
                    if ($request->stock_issue == 2) {
                        $data = $this->stockIssueReturnReport($request->stock_issue, $dates);
                        if ($data->isEmpty()) {
                            return response()->view('error_pages.pdf_zero_data');
                        }
                $pdf = PDF::loadView( 'inventory_reports.issue_return_report_pdf',
                compact( 'data', 'pharmacy' ) )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'issue_return_report.pdf' );
                    } else {
                        $data = $this->stockIssueReturnReport($request->stock_issue, $dates);
                        if ($data->isEmpty()) {
                            return response()->view('error_pages.pdf_zero_data');
                        }
                $pdf = PDF::loadView( 'inventory_reports.issue_issued_report_pdf',
                compact( 'data', 'pharmacy' ) )
                ->setPaper( 'a4', '' );
                return $pdf->stream( 'issue_return_report.pdf' );
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
        if (!Auth()->user()->checkPermission('Current Stock Report')) {
            abort(403, 'Access Denied');
        }
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
    private function currentStockByStoreDeailedReport($store, $category){
        if (!Auth()->user()->checkPermission('Current Stock Report')) {
            abort(403, 'Access Denied');
        }

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
                'inv_current_stock.batch_number',
                'inv_current_stock.expiry_date',
                'inv_current_stock.shelf_number',
                DB::raw('SUM(inv_current_stock.quantity) as total_quantity')
            );

        // Filter by store
        if (!($store == 1 || $store === '1')) {
            $query->where('inv_current_stock.store_id', $store);
        }

        // Filter by category
        if (!($category == 0 || $category === '0')) {
            $query->where('inv_categories.id', $category);
        }

        // Group by product, batch, expiry & shelf (to get real detailed view)
        $current_stocks = $query->groupBy([
                'inv_current_stock.product_id',
                'inv_current_stock.batch_number',
                'inv_current_stock.expiry_date',
                'inv_current_stock.shelf_number',
                'inv_current_stock.store_id',
                'inv_products.name',
                'inv_products.brand',
                'inv_products.pack_size',
                'inv_products.sales_uom',
                'inv_categories.name'
            ])
            ->orderBy('inv_products.name', 'asc')
            ->get();

        $results_data = [];

        foreach ($current_stocks as $current_stock) {
            $results_data[] = [
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
            ];
        }

        return $results_data;
    }
    private function currentStockReport($store, $category)
    {
        if (!Auth()->user()->checkPermission('Current Stock Report')) {
            abort(403, 'Access Denied');
        }
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
        if (!Auth()->user()->checkPermission('Product Details Report')) {
            abort(403, 'Access Denied');
        }
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
    private function productLedgerReport($product_id){
        if (!Auth()->user()->checkPermission('Product Ledger Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $query = DB::table('stock_details')
            ->select('product_id')
            ->groupby(['product_id']);

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
        if (!Auth()->user()->checkPermission('Expired Products Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $query = CurrentStock::where(DB::raw('date(expiry_date)'), '<=', date('Y-m-d'))
            ->orderby('expiry_date', 'DESC');

        if (!is_all_store()) {
            $query->where('store_id', $store_id);
        }
        
        $expired_products = $query->get();
        
        return $expired_products;
    }
    private function productsExpireDateReport()
    {
        if (!Auth()->user()->checkPermission('Products Expiry Date Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $query = CurrentStock::where(DB::raw('date(expiry_date)'), '>', date('Y-m-d'))
            ->orderby('expiry_date', 'DESC');

        if (!is_all_store()) {
            $query->where('store_id', $store_id);
        }
        
        $expire_date = $query->get();
        
        return $expire_date;
    }
    private function outOfStockReport()
    {
        if (!Auth()->user()->checkPermission('Out Of Stock Report')) {
            abort(403, 'Access Denied');
        }
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
        if (!Auth()->user()->checkPermission('Outgoing Tracking Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $query = StockTracking::where('movement', 'OUT')
            ->with(['currentStock.product', 'user']);

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
    private function outgoingTrackingSummaryReport() 
    {
        if (!Auth()->user()->checkPermission('Outgoing Tracking Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $query = StockTracking::where('movement', 'OUT')
            ->with(['currentStock.product', 'user']);

        if (!is_all_store()) {
            $query->where('store_id', $store_id);
        }

        $outgoing = $query->get();

        // group product_id
        $grouped = $outgoing->groupBy(function ($item) {
            return $item->currentStock->product->id;
        });

        // convert grouped data into flat array with summed quantities
        $merged = $grouped->map(function ($rows) {
            $first = $rows->first();
            return (object) [
                'product' => $first->currentStock->product,
                'quantity' => $rows->sum('quantity'),
            ];
        })->values();

        return $merged;
    }
    private function fastMovingReport()
    {
        if (!Auth()->user()->checkPermission('Fast Moving Report')) {
            abort(403, 'Access Denied');
        }

        $store_id = current_store_id();

        // Automatically get range: from 3 months ago to today
        $start_date = now()->subMonths(3)->startOfDay();
        $end_date = now()->endOfDay();

        $query = SalesDetail::join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id')
            ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
            ->join('sales', 'sales.id', '=', 'sales_details.sale_id')
            ->select(
                'inv_products.id as product_id',
                'inv_products.name as product_name',
                'inv_products.brand',
                'inv_products.pack_size',
                'inv_products.sales_uom',
                DB::raw('SUM(sales_details.quantity) as total_sold')
            )
            ->whereBetween('sales.date', [$start_date, $end_date])
            ->groupBy('inv_products.id')
            ->orderByDesc('total_sold');

        if (!is_all_store()) {
            $query->where('inv_current_stock.store_id', $store_id);
        }

        $fast_moving = $query->get();

        // Optional: add rank numbering
        $ranked = $fast_moving->map(function ($item, $index) {
            return [
                'rank'        => $index + 1,
                'product_id'  => $item->product_id,
                'name'        => $item->product_name,
                'brand'       => $item->brand,
                'pack_size'   => $item->pack_size,
                'sales_uom'   => $item->sales_uom,
                'quantity'  => (float) $item->total_sold,
            ];
        });

        return $ranked;
    }
    private function deadStockReport()
    {
        if (!Auth()->user()->checkPermission('Dead Stock Report')) {
            abort(403, 'Access Denied');
        }

        $store_id = current_store_id();

        // Range: 3 months ago -> now
        $three_months_ago = now()->subMonths(3)->startOfDay();
        $today = now()->endOfDay();

        $sold_product_ids = DB::table('sales_details as sd')
            ->join('inv_current_stock as cs', 'cs.id', '=', 'sd.stock_id')
            ->join('sales as s', 's.id', '=', 'sd.sale_id')
            ->when(!is_all_store(), function ($q) use ($store_id) {
                $q->where('cs.store_id', $store_id);
            })
            ->whereBetween('s.date', [$three_months_ago, $today])
            ->distinct()
            ->pluck('cs.product_id')
            ->toArray();

        // 2) Get current stock entries & exclude sold products
        $query = DB::table('inv_current_stock as cs')
            ->join('inv_products as p', 'p.id', '=', 'cs.product_id')
            ->select(
                'p.id as product_id',
                'p.name',
                'p.brand',
                'p.pack_size',
                'p.sales_uom',
                'cs.store_id',
                DB::raw('SUM(cs.quantity) as quantity')
            )
            ->where('cs.quantity', '>', 0)
            // only exclude sold products if we have any sold ids; otherwise keep all (no sold in period)
            ->when(!empty($sold_product_ids), function ($q) use ($sold_product_ids) {
                $q->whereNotIn('cs.product_id', $sold_product_ids);
            })
            ->when(!is_all_store(), function ($q) use ($store_id) {
                $q->where('cs.store_id', $store_id);
            })
            ->groupBy(
                'p.id'
            )
            ->orderBy('p.name', 'asc');

        $dead_stock = $query->get();

        return $dead_stock;
    }
    private function stockAdjustmentReport($dates, $type, $reason)
    {  
        if (!Auth()->user()->checkPermission('Stock Adjustment Report')) {
            abort(403, 'Access Denied');
        }          
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

        $adjustments = $query->orderBy('created_at', 'desc')->get();
        
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
        if (!Auth()->user()->checkPermission('Stock Issue Report')) {
            abort(403, 'Access Denied');
        }
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
        if (!Auth()->user()->checkPermission('Stock Issue Return Report')) {
            abort(403, 'Access Denied');
        }
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
        if (!Auth()->user()->checkPermission('Stock Transfer Report')) {
            abort(403, 'Access Denied');
        }
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
        if (!Auth()->user()->checkPermission('Stock Above Max. Level')) {
            abort(403, 'Access Denied');
        }
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
        if (!Auth()->user()->checkPermission('Stock Below Min. Level')) {
            abort(403, 'Access Denied');
        }
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
