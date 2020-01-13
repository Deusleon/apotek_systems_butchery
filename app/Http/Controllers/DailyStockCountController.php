<?php

namespace App\Http\Controllers;

use App\SalesDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

		$sales = SalesDetail::all();

        $current_stocks = DB::table('inv_current_stock as g')
            ->select(DB::raw('g.product_id'),
                DB::raw('sum(g.quantity) as quantity_on_hand'))
            ->join(DB::raw('(
            SELECT 
            `product_name`,
            `product_id`
            FROM
                `stock_details`
            GROUP BY `product_id`) as g1'),
                function ($join) {
                    $join->on('g1.product_id', '=', 'g.product_id');
                })
            ->groupBy(DB::raw('g1.product_id'))
            ->get();


		$products = array();
		$dailyStockCount = array();


		//pass the sale and extract data for the date
		foreach($sales as $sale){
			//get only the date
            $date = date('Y-m-d', strtotime($sale->sale['date']));

			//the date exists
			if ($date == $specific_date) {

				array_push($products, array(
					'product_id' => $sale->currentStock['product_id'],
					'product_name' => $sale->currentStock->product['name'],
					'quantity_sold' => $sale->quantity,
				));

			}

		}


		//loop the results to sum
		foreach($products as $ar)
		{

		    foreach($ar as $k => $v)
		    {

		        if(array_key_exists($v, $dailyStockCount)){
		            $dailyStockCount[$v]['quantity_sold'] = $dailyStockCount[$v]['quantity_sold'] + $ar['quantity_sold'];
		            // $dailyStockCount[$v]['quantity_on_hand'] = 1323;

                    foreach ($current_stocks as $value) {

                        if ($dailyStockCount[$v]['product_id'] == $value->product_id) {

				            $dailyStockCount[$v]['quantity_on_hand'] = $value->quantity_on_hand;

                        }

                    }
		        }
		        else if($k == 'product_id'){
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
