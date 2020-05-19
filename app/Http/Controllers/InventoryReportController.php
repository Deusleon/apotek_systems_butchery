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
use App\Store;
use FPDF;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;
use PDFMerger;

ini_set('max_execution_time', 500);
set_time_limit(500);
ini_set('memory_limit', '512M');

class InventoryReportController extends Controller
{

    public function index()
    {

        $products = DB::table('product_ledger')
            ->select('product_id', 'product_name')
            ->groupby('product_id', 'product_name')
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

        switch ($request->report_option) {
            case 1:
                //current stock by store
                if ($request->category_name == null) {
                    $data_og = $this->currentStockByStoreReport($request->store_name);
                    if ($data_og == []) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $view = 'inventory_reports.current_stock_by_store_report_pdf';
                    $output = 'current_stock_by_store_report.pdf';
                    $this->splitPdf($data_og, $view, $output);
                    break;
                } else {
                    //current stock
                    $data_og = $this->currentStockReport($request->category_name);
                    if ($data_og == []) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $view = 'inventory_reports.current_stock_report_pdf';
                    $output = 'current_stock_report.pdf';
                    $this->splitPdf($data_og, $view, $output);
                    break;
                }
            case 2:
                //product detail
                $data_og = $this->productDetailReport($request->category_name_detail);
                if ($data_og == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                if ($request->category_name_detail != null) {
                    $view = 'inventory_reports.product_detail_report_pdf';
                    $output = 'product_details_report.pdf';
                } else {
                    $view = 'inventory_reports.product_detail1_report_pdf';
                    $output = 'product_details_report.pdf';
                }

                $this->splitPdf($data_og, $view, $output);
                break;
            case 3:
                //product ledger
                $data_og = $this->productLedgerReport($request->product);
                $view = 'inventory_reports.product_ledger_report_pdf';
                $output = 'product_ledger_report.pdf';
                $this->splitPdf($data_og, $view, $output);
                break;
            case 4:
                //expired product
                $data_og = $this->expiredProductReport();
                if ($data_og->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $view = 'inventory_reports.expiry_product_report_pdf';
                $output = 'expiry_product.pdf';
                $this->splitPdf($data_og, $view, $output);
                break;
            case 5:
                //out of stock
                $data_og = $this->outOfStockReport();
                if ($data_og->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $view = 'inventory_reports.outofstock_report_pdf';
                $output = 'outofstock_report.pdf';
                $this->splitPdf($data_og, $view, $output);
                break;
            case 6:
                //outgoing tracking report
                $data_og = $this->outgoingTrackingReport();
                if ($data_og->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $view = 'inventory_reports.outgoing_stocktracking_report_pdf';
                $output = 'outgoing_stocktracking_report.pdf';
                $this->splitPdf($data_og, $view, $output);
                break;
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

                $this->splitPdf($data_og, $view, $output);
                break;
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
                    $data_og = $this->stockTransferReport($dates);
                    if ($data_og->isEmpty()) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $view = 'inventory_reports.stock_transfer_report_pdf';
                    $output = 'stock_transfer_report.pdf';
                    $this->splitPdf($data_og, $view, $output);
                    break;
                } else {
                    $data_og = $this->stockTransferStatusReport($request->stock_transfer, $dates);
                    if ($data_og->isEmpty()) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $view = 'inventory_reports.stock_transfer_status_report_pdf';
                    $output = 'stock_transfer_status_report.pdf';
                    $this->splitPdf($data_og, $view, $output);
                    break;
                }
            case 10:
                $data_og = $this->stockMaxLevel();
                if ($data_og == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $view = 'inventory_reports.stock_max_level_pdf';
                $output = 'stock_max_level.pdf';
                $this->splitPdf($data_og, $view, $output);
                break;
            case 11:
                $data_og = $this->stockMinLevel();
                if ($data_og == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $view = 'inventory_reports.stock_min_level_pdf';
                $output = 'stock_min_level.pdf';
                $this->splitPdf($data_og, $view, $output);
                break;

            default:
        }
    }

    private function currentStockByStoreReport($store)
    {
        $current_stocks = CurrentStock::where('store_id', $store)->get();
        $stores = Store::all();
        $ungrouped_result = array();
        $grouped_result = array();


        foreach ($current_stocks as $current_stock) {
            array_push($ungrouped_result, array(
                'product_id' => $current_stock->product['id'],
                'store' => $current_stock->store['name'],
                'name' => $current_stock->product['name'],
                'expiry_date' => $current_stock->expiry_date,
                'quantity' => $current_stock->quantity,
                'batch_number' => $current_stock->batch_number,
                'shelf_no' => $current_stock->shelf_number
            ));
        }

//        foreach ($ungrouped_result as $val) {
//            if (array_key_exists('store', $val)) {
//                $grouped_result[$val['store']][] = $val;
//            }
//        }

        return $ungrouped_result;
    }

    public function splitPdf($data_og, $view, $output_file)
    {

        $pharmacy['name'] = Setting::where('id', 100)->value('value');
        $pharmacy['address'] = Setting::where('id', 106)->value('value');
        $pharmacy['phone'] = Setting::where('id', 107)->value('value');
        $pharmacy['email'] = Setting::where('id', 108)->value('value');
        $pharmacy['website'] = Setting::where('id', 109)->value('value');
        $pharmacy['logo'] = Setting::where('id', 105)->value('value');
        $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');

        $pdfs = new PDFMerger();
//        $pharmacy = GeneralSetting::all();
        $count = 0;

        if ($data_og instanceof Collection) {
            /*if collection then chunk*/
            $datas = $data_og->chunk(500);
            ob_end_clean();
            (new FPDF)->AddPage();
        } else {
            /*chunk the array*/
            $datas = array_chunk($data_og, 500);
            ob_end_clean();
            (new FPDF)->AddPage();
        }

        foreach ($datas as $data) {
            $count = $count + 1;
            $pdf = PDF::loadView($view,
                compact('data', 'pharmacy'));
            $output = $pdf->output();
            Storage::put('pdfs/pdf' . $count . '.pdf', $output);

        }


        /*load files to merge*/
        $pdf_files = Storage::files('pdfs');
        if (sizeof($pdf_files) != 0) {
            foreach ($pdf_files as $pdf_file) {
                $pdfs->addPDF(Storage::path($pdf_file), 'all');

            }
            $pdfs->merge('stream', $output_file);
            Storage::delete(Storage::files('pdfs'));

        }


    }

    private function currentStockReport($category)
    {
        $current_stocks = CurrentStock::all();
        $categories = Category::where('id', $category)->get();
        $ungrouped_result = array();
        $grouped_result = array();


        foreach ($current_stocks as $current_stock) {
            foreach ($categories as $category) {
                if ($category->id == $current_stock->product['category_id']) {
                    array_push($ungrouped_result, array(
                        'product_id' => $current_stock->product['id'],
                        'category' => $category->name,
                        'name' => $current_stock->product['name'],
                        'expiry_date' => $current_stock->expiry_date,
                        'quantity' => $current_stock->quantity,
                        'batch_number' => $current_stock->batch_number,
                        'shelf_no' => $current_stock->shelf_number
                    ));
                }
            }
        }

//        foreach ($ungrouped_result as $val) {
//            if (array_key_exists('category', $val)) {
//                $grouped_result[$val['category']][] = $val;
//            }
//        }

        return $ungrouped_result;

    }

    private function productDetailReport($category)
    {
        if ($category != null) {
            $products = Product::where('category_id', $category)->get();
        } else {
            $products = Product::all();
        }
        $ungrouped_result = array();
        $grouped_result = array();

        foreach ($products as $product) {
            array_push($ungrouped_result, array(
                'product_id' => $product->id,
                'name' => $product->name,
                'category' => $product->category['name'],
                'generic_name' => $product->generic_name,
                'indication' => $product->indication
            ));
        }

//        foreach ($ungrouped_result as $val) {
//            if (array_key_exists('category', $val)) {
//                $grouped_result[$val['category']][] = $val;
//            }
//        }

        return $ungrouped_result;

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

//        foreach ($ungrouped_result as $val) {
//            if (array_key_exists('name', $val)) {
//                $grouped_result[$val['name']][] = $val;
//            }
//        }

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

}
