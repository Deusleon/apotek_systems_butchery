<?php

namespace App\Http\Controllers;

use App\Category;
use App\CommonFunctions;
use App\CurrentStock;
use App\GoodsReceiving;
use App\Invoice;
use App\Order;
use App\OrderDetail;
use App\PriceCategory;
use App\Setting;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use View;

ini_set('max_execution_time', 500);
set_time_limit(500);
ini_set('memory_limit', '512M');

class PurchaseReportController extends Controller
{

    public function index()
    {
        $price_category = PriceCategory::all();

        $category = Category::all();
        $invoices = Invoice::all();
        $orders = Order::all();
        $order_details = OrderDetail::all();
        $current_stock = CurrentStock::all();
        $suppliers = Supplier::all();
        $material_received = GoodsReceiving::all();

        return View::make('purchases_reports.index',
            (compact('order_details', 'suppliers', 'orders', 'price_category',
                'buying_prices', 'current_stock', 'item_stocks', 'invoices', 'pharmacy', 'category',
                'material_received', 'settings')));
    }

    protected function reportOption(Request $request)
    {

        $pharmacy['name'] = Setting::where('id', 100)->value('value');
        $pharmacy['address'] = Setting::where('id', 106)->value('value');
        $pharmacy['phone'] = Setting::where('id', 107)->value('value');


        switch ($request->report_option) {
            case 1:
                $data_og = $this->materialReceivedReport($request->supplier, $request->expire_dates, $request->invoice_no);

                if ($request->supplier != null) {
                    if ($data_og->isEmpty()) {
                        return response()->view('error_pages.pdf_zero_data');
                    }
                    $view = 'purchases_reports.material_received_report_pdf';
                    $output = 'material_received_report.pdf';
                    $report = new InventoryReportController();
                    $report->splitPdf($data_og, $view, $output);
                } else {
                    if ($data_og == []) {
                        return response()->view('error_pages.pdf_zero_data');
                    }

                    $pdf = PDF::loadView('purchases_reports.material_received_all_supplier_report_pdf',
                        compact('data_og', 'pharmacy'));
                    return $pdf->stream('material_received_all_supplier.pdf');
                }


                break;
            case 2:
                $data_og = $this->InvoiceSummaryReport($request->suppliers, $request->expire_date,
                    $request->received_status, $request->period);
                if ($data_og->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $view = 'purchases_reports.invoice_summary_report_pdf';
                $output = 'invoice_summary_report.pdf';
                $report = new InventoryReportController();
                $report->splitPdf($data_og, $view, $output);
                break;
            case 3:
                break;
            case 4:
                $data_og = $this->supplierList();
                $view = 'purchases_reports.supplier_list_pdf';
                $output = 'supplier_list_report.pdf';
                $report = new InventoryReportController();
                $report->splitPdf($data_og, $view, $output);
                break;
            case 5:
                $data_og = $this->supplierPriceComparison();
                $view = 'purchases_reports.supplier_price_comparison_report_pdf';
                $output = 'supplier_price_comparison_report.pdf';
                $report = new InventoryReportController();
                $report->splitPdf($data_og, $view, $output);
            default;
        }
    }

    public function materialReceivedReport($supplier, $date, $invoice_no)
    {

        $dates = explode(" - ", $date);

        if ($invoice_no == null) {
            if ($supplier == null) {
                $datas = GoodsReceiving::whereBetween(DB::raw('date(created_at)'),
                    [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                    ->orderby('created_at', 'DESC')
                    ->get();
            } else {
                $datas = GoodsReceiving::where('supplier_id', $supplier)
                    ->whereBetween(DB::raw('date(created_at)'),
                        [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                    ->orderby('created_at', 'DESC')
                    ->get();
            }

        } else {
            if ($supplier == null) {
                $datas = GoodsReceiving::whereBetween(DB::raw('date(created_at)'),
                    [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                    ->where('invoice_no', '=', $invoice_no)
                    ->orderby('created_at', 'DESC')
                    ->get();
            } else {
                $datas = GoodsReceiving::where('supplier_id', $supplier)
                    ->whereBetween(DB::raw('date(created_at)'),
                        [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                    ->where('invoice_no', '=', $invoice_no)
                    ->orderby('created_at', 'DESC')
                    ->get();
            }

            if ($dates != null) {
                foreach ($datas as $data) {
                    $data->invoice_nos = $data->invoice['invoice_no'];
                }
            }
        }

        foreach ($datas as $d) {
            $d->total_bp = $datas->sum('total_cost');
            $d->total_sp = $datas->sum('total_sell');
            $d->total_p = $datas->sum('item_profit');
            $d->dates = $dates;
            $d->supplier_name = $d->supplier['name'];
        }

        /*push them in an array*/
        $raw_data = array();
        foreach ($datas as $datum) {
            array_push($raw_data, array(
                'code' => $datum->product_id,
                'product_name' => $datum->product['name'],
                'quantity' => $datum->quantity,
                'unit_cost' => $datum->unit_cost,
                'sell_price' => $datum->sell_price,
                'profit' => $datum->item_profit,
                'total_cost' => $datum->total_cost,
                'total_sell' => $datum->total_sell,
                'date' => date('d-m-Y', strtotime($datum->created_at)),
                'supplier' => $datum->supplier['name'],
                'received_by' => $datum->user['name']
            ));
        }

        /*make supplier key*/
        $raw_data_by_key_supplier = array();
        foreach ($raw_data as $raw_datum) {
            if (array_key_exists('supplier', $raw_datum)) {
                $raw_data_by_key_supplier[$raw_datum['supplier']][] = $raw_datum;
            }
        }

        /*sum total cost for the total*/
        $total_cost = 0;
        $grand_total_cost = array();
        $grand_total_cost_key = array();
        foreach ($raw_data_by_key_supplier as $key => $value) {
            foreach ($value as $item) {
                $total_cost = $total_cost + $item['total_cost'];
            }
            array_push($grand_total_cost, array(
                'supplier' => $key,
                'amount' => $total_cost
            ));
        }
        foreach ($grand_total_cost as $raw_datum) {
            if (array_key_exists('supplier', $raw_datum)) {
                $grand_total_cost_key[$raw_datum['supplier']][] = $raw_datum;
            }
        }

        /*sum total sell for the total*/
        $total_sell = 0;
        $grand_total_sell = array();
        $grand_total_sell_key = array();
        foreach ($raw_data_by_key_supplier as $key => $value) {
            foreach ($value as $item) {
                $total_sell = $total_sell + $item['total_sell'];
            }
            array_push($grand_total_sell, array(
                'supplier' => $key,
                'amount' => $total_sell
            ));
        }
        foreach ($grand_total_sell as $raw_datum) {
            if (array_key_exists('supplier', $raw_datum)) {
                $grand_total_sell_key[$raw_datum['supplier']][] = $raw_datum;
            }
        }

        /*sum total profit for the total*/
        $total_profit = 0;
        $grand_total_profit = array();
        $grand_total_profit_key = array();

        $supplier_sum = array();
        $sum_by_key = new CommonFunctions();
        foreach ($raw_data_by_key_supplier as $value) {
            foreach ($value as $item) {

                $index = $sum_by_key->sumByKey($item['supplier'], $supplier_sum, 'supplier');
                if ($index < 0) {
                    $supplier_sum[] = $item;
                } else {
                    $supplier_sum[$index]['total_cost'] += $item['total_cost'];
                    $supplier_sum[$index]['total_sell'] += $item['total_sell'];
                    $supplier_sum[$index]['profit'] += $item['profit'];

                }
            }

        }

        $test = array();
        foreach ($supplier_sum as $raw_datum) {
            if (array_key_exists('supplier', $raw_datum)) {
                $test[$raw_datum['supplier']][] = $raw_datum;
            }
        }


        foreach ($raw_data_by_key_supplier as $key => $value) {
            foreach ($value as $item) {
                $total_profit = $total_profit + $item['profit'];
            }
            array_push($grand_total_profit, array(
                'supplier' => $key,
                'amount' => $total_profit
            ));
        }
        foreach ($grand_total_profit as $raw_datum) {
            if (array_key_exists('supplier', $raw_datum)) {
                $grand_total_profit_key[$raw_datum['supplier']][] = $raw_datum;
            }
        }

        /*what to return to be printed*/
        if ($supplier != null) {
            return $datas;
        } else {
            $to_print = array();
            array_push($to_print, array(
                'data' => $raw_data_by_key_supplier,
                'cost_by_supplier' => $test,
                'total_cost' => $grand_total_cost_key,
                'total_sell' => $grand_total_sell_key,
                'total_profit' => $grand_total_profit_key
            ));

            return $to_print;
        }

    }

    public function InvoiceSummaryReport($supplier, $date, $status, $period)
    {
        $dates = explode(" - ", $date);
        if ($status !== null) {
            $datas = Invoice::where('supplier_id', $supplier)
                ->whereBetween(DB::raw('date(invoice_date)'),
                    [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                ->where('received_status', $status)
                ->orderby('invoice_date', 'DESC')
                ->get();
        } elseif ($period !== null) {
            $datas = Invoice::where('supplier_id', $supplier)
                ->whereBetween(DB::raw('date(invoice_date)'),
                    [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                ->where('grace_period', $period)
                ->orderby('invoice_date', 'DESC')
                ->get();
        } elseif ($period !== null && $status !== null) {
            $datas = Invoice::where('supplier_id', $supplier)
                ->whereBetween(DB::raw('date(invoice_date)'),
                    [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                ->where('received_status', $status)
                ->where('grace_period', $period)
                ->orderby('invoice_date', 'DESC')
                ->get();
        } else {
            $datas = Invoice::where('supplier_id', $supplier)
                ->whereBetween(DB::raw('date(invoice_date)'),
                    [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))])
                ->orderby('invoice_date', 'DESC')
                ->get();
        }

        foreach ($datas as $d) {
            $d->dates = $dates;
        }

        return $datas;
    }

    public function InvoiceDetailsReport()
    {
        $datas = Invoice::all();
        return $datas;
    }

    private function supplierList()
    {
        $suppliers = Supplier::all();
        return $suppliers;
    }

    private function supplierPriceComparison()
    {
        $prices = array();
        $supplier_prices = GoodsReceiving::all();
//        foreach ($supplier_prices as $supplier_price) {
//            array_push($prices, array(
//                'product_name' => $supplier_price->product['name'],
//                'supplier' => $supplier_price->supplier['name'],
//                'buy_price' => $supplier_price->unit_cost
//            ));
//        }
//        dd($prices);
        return $supplier_prices;
    }


}
