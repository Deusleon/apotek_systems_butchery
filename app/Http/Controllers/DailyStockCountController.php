<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\Sale;
use App\SalesDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $sales_per_date = Sale::where(DB::raw('date(date)'), $specific_date)->get();

        $current_stocks = CurrentStock::select(DB::raw('product_id'),
            DB::raw('sum(quantity) as quantity_on_hand'))
            ->groupby('product_id')
            ->get();

        $products = array();
        $dailyStockCount = array();

        /*sale per day*/
        foreach ($sales_per_date as $sale_per_date) {
            /*check for that sale id*/
            $sale_per_date_details = SalesDetail::where('sale_id', $sale_per_date->id)->get();
            foreach ($sale_per_date_details as $sale_per_date_detail) {
                array_push($products, array(
                    'product_id' => $sale_per_date_detail->currentStock['product_id'],
                    'product_name' => $sale_per_date_detail->currentStock['product']['name'],
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

//        $pdf = PDF::loadView('stock_management.daily_stock_count.daily_stock_count',
//            compact('new_data'));
//
//        return $pdf->stream('daily_stock_count.pdf');

    }


}
