<?php

namespace App\Http\Controllers;

use App\CommonFunctions;
use App\CurrentStock;
use App\Expense;
use App\PriceCategory;
use App\PriceList;
use App\Sale;
use App\SalesDetail;
use App\Setting;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

ini_set('max_execution_time', 500);
set_time_limit(500);
ini_set('memory_limit', '512M');

class AccountingReportController extends Controller
{
    public function index()
    {
        $price_categories = PriceCategory::all();
        $stores = Store::all();
        return view('accounting_reports.index', compact('price_categories', 'stores'));
    }

    protected function reportOption(Request $request)
    {

        $pharmacy['name'] = Setting::where('id', 100)->value('value');
        $pharmacy['address'] = Setting::where('id', 106)->value('value');

        switch ($request->report_option) {
            case 1:
                $dates = explode(" - ", $request->date_range);
                $data_og = $this->currentStockValue($dates, $request->price_category_id, $request->store_id);
                if ($data_og == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $view = 'accounting_reports.current_stock_value_report_pdf';
                $output = 'current_stock_value_report.pdf';
                $pdf_print = new InventoryReportController();
                $pdf_print->splitPdf($data_og, $view, $output);
                break;
            case 2:
                $dates = explode(" - ", $request->date_range);
                $data = $this->grossProfitDetail($dates);
                if ($data == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $pdf = PDF::loadView('accounting_reports.gross_profit_detail_report_pdf',
                    compact('data', 'pharmacy'));
                return $pdf->stream('gross_profit_detail_report.pdf');
                break;
            case 3:
                $dates = explode(" - ", $request->date_range);
                $data = $this->grossProfitSummary($dates);
                if ($data == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $pdf = PDF::loadView('accounting_reports.gross_profit_summary_report_pdf',
                    compact('data', 'pharmacy'));
                return $pdf->stream('gross_profit_summary_report.pdf');
                break;

            case 4:
                $dates = explode(" - ", $request->date_range);
                $data_og = $this->expenseReport($dates);
                if ($data_og->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $view = 'accounting_reports.expense_report_pdf';
                $output = 'expense_report.pdf';
                $pdf_print = new InventoryReportController();
                $pdf_print->splitPdf($data_og, $view, $output);
                break;
            case 5:
                $dates = explode(" - ", $request->date_range);
                $data_og = $this->incomeStatementReport($dates);
                if ($data_og->isEmpty()) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $view = 'accounting_reports.income_statement_report_pdf';
                $output = 'income_statement_report.pdf';
                $pdf_print = new InventoryReportController();
                $pdf_print->splitPdf($data_og, $view, $output);
                break;
            case 6:

                if ($request->expire_date_range != null) {
                    $dates = explode(" - ", $request->expire_date_range);
                } else {
                    $dates = [];
                }
                $data_og = $this->costOfExpiredProduct($dates, $request->price_category_id_expire);
                if ($data_og == []) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $view = 'accounting_reports.expired_products_cost_report_pdf';
                $output = 'expired_products_cost_report.pdf';
                $pdf_print = new InventoryReportController();
                $pdf_print->splitPdf($data_og, $view, $output);
                break;
            default:
        }
    }

    private function expenseReport($date)
    {
        $total = 0;
        $date[0] = date('Y-m-d', strtotime($date[0]));
        $date[1] = date('Y-m-d', strtotime($date[1]));

        //by default return todays month expenses
        $expense = Expense::whereBetween(DB::raw('date(created_at)'), [$date[0], $date[1]])
            ->orderby('id', 'DESC')
            ->get();
        foreach ($expense as $item) {
            $total = $total + $item->amount;
            $item->total = $total;
            $item->from = $date[0];
            $item->to = $date[1];
        }

        return $expense;

    }

    private function incomeStatementReport($date)
    {
        $date[0] = date('Y-m-d', strtotime($date[0]));
        $date[1] = date('Y-m-d', strtotime($date[1]));

        $total_sell = 0;
        $total_buy = 0;

        $sale_detail = SalesDetail::select('stock_id', 'amount')
            ->whereNotIn('sale_id', DB::table('sales_credits')->pluck('sale_id'))
            ->join('sales', 'sales.id', '=', 'sales_details.sale_id')
            ->whereBetween(DB::raw('date(date)'), [$date[0], $date[1]])
            ->get();

        foreach ($sale_detail as $detail) {
            $total_sell = $total_sell + $detail->amount;
            $total_buy = $total_buy + $detail->currentStock['unit_cost'];

            $detail->total_sell = $total_sell;
            $detail->total_buy = $total_buy;
            $detail->from = $date[0];
            $detail->to = $date[1];

        }

        return $sale_detail;

    }

    private function currentStockValue($dates, $price_category_id, $store_id)
    {
        $category_total_cost = array();
        $products = PriceList::where('price_category_id', $price_category_id)
            ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
            ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
            ->where('store_id', '=', $store_id)
            ->Where('inv_products.status', '1')
            ->select('inv_products.id as id', 'name')
            ->groupBy('product_id')
            ->get();

        foreach ($products as $product) {
            $data = PriceList::select('stock_id', 'price')->where('price_category_id', $price_category_id)
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->orderBy('stock_id', 'desc')
                ->where('product_id', $product->id)
                ->first('price');

            array_push($category_total_cost, array(
                'category_name' => $data->currentStock['product']['category']['name'],
                'buy_price' => $data->currentStock['unit_cost'],
                'sell_price' => $data->price,
                'store' => $data->currentStock['store']['name']
            ));
        }

        $sum_by_category = array();
        $sum_by_key = new CommonFunctions();
        foreach ($category_total_cost as $value) {
            $index = $sum_by_key->sumByKey($value['category_name'], $sum_by_category, 'category_name');
            if ($index < 0) {
                $sum_by_category[] = $value;
            } else {
                $sum_by_category[$index]['buy_price'] += $value['buy_price'];
                $sum_by_category[$index]['sell_price'] += $value['sell_price'];
            }
        }

        $total_buy = 0;
        $total_sell = 0;
        $to_print = array();
        foreach ($sum_by_category as $item) {
            $total_buy = $total_buy + $item['buy_price'];
            $total_sell = $total_sell + $item['sell_price'];
            array_push($to_print, array(
                'category_name' => $item['category_name'],
                'buy_price' => $item['buy_price'],
                'sell_price' => $item['sell_price'],
                'store' => $item['store'],
                'grand_total_buy' => $total_buy,
                'grand_total_sell' => $total_sell
            ));
        }

        return $to_print;
    }

    private function costOfExpiredProduct(array $dates, $price_category_id_expire)
    {
        if (sizeof($dates) != 0) {
            $date[0] = date('Y-m-d', strtotime($dates[0]));
            $date[1] = date('Y-m-d', strtotime($dates[1]));
        }

        $max_prices = array();

        $total_buy = 0;
        $total_sell = 0;
        $products = PriceList::where('price_category_id', $price_category_id_expire)
            ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
            ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
            ->where('quantity', '>', 0)
            ->Where('inv_products.status', '1')
            ->select('inv_products.id as id', 'name')
            ->groupBy('product_id')
            ->get();

        foreach ($products as $product) {
            if (sizeof($dates) == 0) {
                /*from today backward*/
                $data = PriceList::select('stock_id', 'price', 'expiry_date')->where('price_category_id', $price_category_id_expire)
                    ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                    ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                    ->orderBy('stock_id', 'desc')
                    ->where(DB::raw('date(expiry_date)'), '<=', date('Y-m-d'))
                    ->where('product_id', $product->id)
                    ->first('price');

                $quantity = CurrentStock::where('product_id', $product->id)
                    ->where(DB::raw('date(expiry_date)'), '<=', date('Y-m-d'))
                    ->sum('quantity');

            } else {
                $data = PriceList::select('stock_id', 'price', 'expiry_date')->where('price_category_id', $price_category_id_expire)
                    ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                    ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                    ->orderBy('stock_id', 'desc')
                    ->whereBetween(DB::raw('date(expiry_date)'), [$date[0], $date[1]])
                    ->where('product_id', $product->id)
                    ->first('price');

                $quantity = CurrentStock::where('product_id', $product->id)
                    ->whereBetween(DB::raw('date(expiry_date)'), [$date[0], $date[1]])
                    ->sum('quantity');

            }


            if ($data != null) {
                $total_buy = $total_buy + ($data->currentStock['unit_cost'] * $quantity);
                $total_sell = $total_sell + ($data->price * $quantity);

                array_push($max_prices, array(
                    'name' => $data->currentStock['product']['name'],
                    'cost_buy_price' => $data->currentStock['unit_cost'] * $quantity,
                    'cost_sell_price' => $data->price * $quantity,
                    'quantity' => $quantity,
                    'batch_number' => $data->currentStock['batch_number'],
                    'expire_date' => $data->currentStock['expiry_date'],
                    'total_buy' => $total_buy,
                    'total_sell' => $total_sell
                ));
            }

        }

        return $max_prices;

    }

    private function grossProfitSummary(array $dates)
    {
        $date[0] = date('Y-m-d', strtotime($dates[0]));
        $date[1] = date('Y-m-d', strtotime($dates[1]));

        /*sale date only*/
        $user_sales_date = Sale::select(DB::Raw("date(date) as date"))
            ->whereBetween(DB::Raw("date(date)"), [$date[0], $date[1]])
            ->orderby('id', 'asc')->groupby(DB::Raw("date(date)"))->get();

        /*get only date for comparison and mapping*/
        $dates_only = array();
        foreach ($user_sales_date as $dates) {
            array_push($dates_only, $dates->date);
        }

        /*total sold items amount*/
        $sale_detail = DB::table('sales_details')
            ->select(
                DB::raw('sum(amount) as amount'),
                DB::raw('sum(vat) as vat'),
                DB::raw('sum(price) as price'),
                DB::raw('sum(discount) as discount'),
                DB::raw('date(date) as dates'))
            ->join('sales', 'sales.id', '=', 'sales_details.sale_id')
            ->whereBetween(DB::raw('date(date)'), [$date[0], $date[1]])
//            ->where('sales_details.status', '!=', 3)
            ->join('users', 'users.id', '=', 'sales.created_by')
            ->whereNotIn('sale_id', DB::table('sales_credits')->pluck('sale_id'))
            ->groupby(DB::Raw('date(date)'))
            ->get();

        /*put total sell amount into array*/
        $total_sell_amount = array();
        foreach ($sale_detail as $item) {
            $value = $item->amount - $item->discount;
            if (intVal($item->vat) === 0) {
                $vat_percent = 0;
            } else {
                $vat_percent = $item->vat / $item->price;
            }
            $sub_total = ($value / (1 + $vat_percent));

            array_push($total_sell_amount, array(
                'amount' => $item->amount,
                'vat' => $item->vat,
                'price' => $item->price,
                'discount' => $item->discount,
                'date' => $item->dates,
                'total_sell' => $sub_total
            ));
        }

        /*sold items only*/
        $sold_items = DB::table('sales_details')
            ->select(DB::raw('sales_details.id as sales_details_id'),
                DB::raw('sales.id as sale_id'),
                DB::raw('stock_id as stock_id'),
                DB::raw('quantity'),
                DB::raw('price_category_id'),
                DB::raw('date(date) as dates'))
            ->join('sales', 'sales.id', '=', 'sales_details.sale_id')
            ->whereBetween(DB::raw('date(date)'), [$date[0], $date[1]])
//            ->where('sales_details.status', '!=', 3)
            ->join('users', 'users.id', '=', 'sales.created_by')
            ->whereNotIn('sale_id', DB::table('sales_credits')->pluck('sale_id'))
            ->get();

        /*both price and sell price*/
        $raw_prices_data = array();
        foreach ($sold_items as $detail) {
            /*get the product*/
            $product = PriceList::where('price_category_id', $detail->price_category_id)
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->where('stock_id', $detail->stock_id)
                ->Where('inv_products.status', '1')
                ->orderBy('stock_id', 'desc')
                ->select('inv_products.id as id', 'name', 'price', 'stock_id')
                ->first('price');


            array_push($raw_prices_data, array(
                'stock_id' => $product->stock_id,
                'sales_details_id' => $detail->sales_details_id,
                'sale_id' => $detail->sale_id,
                'quantity' => $detail->quantity,
                'buy_price' => $product->currentStock['unit_cost'],
                'total_buy' => $product->currentStock['unit_cost'] * $detail->quantity,
                'date' => $detail->dates
            ));

        }

        /*total sell make date key*/
        $total_sell_by_key_date = array();
        foreach ($total_sell_amount as $details) {
            if (array_key_exists('date', $details)) {
                $total_sell_by_key_date[$details['date']][] = $details;
            }
        }

        /*buy price sum by key*/
        $total_buy_amount = array();
        $sum_by_key = new CommonFunctions();
        foreach ($raw_prices_data as $value) {
            $index = $sum_by_key->sumByKey($value['date'], $total_buy_amount, 'date');
            if ($index < 0) {
                $total_buy_amount[] = $value;
            } else {
                $total_buy_amount[$index]['total_buy'] += $value['total_buy'];
            }
        }

        /*total buy make date key*/
        $total_buy_by_key_date = array();
        foreach ($total_buy_amount as $buy) {
            if (array_key_exists('date', $buy)) {
                $total_buy_by_key_date[$buy['date']][] = $buy;
            }
        }

        /*sum total sell for the total*/
        $grand_total_sell = 0;
        foreach ($total_sell_by_key_date as $key => $value) {
            foreach ($value as $item) {
                $grand_total_sell = $grand_total_sell + $item['total_sell'];
            }
        }

        /*sum total buy for the total*/
        $grand_total_buy = 0;
        foreach ($total_buy_by_key_date as $key => $value) {
            foreach ($value as $item) {
                $grand_total_buy = $grand_total_buy + $item['total_buy'];
            }
        }

        $to_print = array();
        array_push($to_print, array(
            'dates' => $dates_only,
            'total_buy' => $total_buy_by_key_date,
            'total_sell' => $total_sell_by_key_date,
            'grand_total_buy' => $grand_total_buy,
            'grand_total_sell' => $grand_total_sell,
            'from' => $date[0],
            'to' => $date[1]
        ));

        return $to_print;

    }

    private function grossProfitDetail(array $dates)
    {
        $date[0] = date('Y-m-d', strtotime($dates[0]));
        $date[1] = date('Y-m-d', strtotime($dates[1]));

        /*sold items only*/
        $sold_items = DB::table('sales_details')
            ->select(DB::raw('sales_details.id as sales_details_id'),
                DB::raw('sales.id as sale_id'),
                DB::raw('stock_id as stock_id'),
                DB::raw('quantity'),
                DB::raw('amount'),
                DB::raw('vat'),
                DB::raw('price'),
                DB::raw('discount'),
                DB::raw('price_category_id'),
                DB::raw('date(date) as dates'))
            ->join('sales', 'sales.id', '=', 'sales_details.sale_id')
            ->whereBetween(DB::raw('date(date)'), [$date[0], $date[1]])
//            ->where('sales_details.status', '!=', 3)
            ->join('users', 'users.id', '=', 'sales.created_by')
            ->whereNotIn('sale_id', DB::table('sales_credits')->pluck('sale_id'))
            ->get();

        /*both price and sell price*/
        $raw_prices_data = array();
        foreach ($sold_items as $detail) {
            $value = $detail->amount - $detail->discount;
            if (intVal($detail->vat) === 0) {
                $vat_percent = 0;
            } else {
                $vat_percent = $detail->vat / $detail->price;
            }
            $sub_total = ($value / (1 + $vat_percent));

            /*get the product*/
            $product = PriceList::where('price_category_id', $detail->price_category_id)
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->where('stock_id', $detail->stock_id)
                ->Where('inv_products.status', '1')
                ->orderBy('stock_id', 'desc')
                ->select('inv_products.id as id', 'name', 'price', 'stock_id')
                ->first('price');


            array_push($raw_prices_data, array(
                'name' => $product->currentStock['product']['name'],
                'quantity' => $detail->quantity,
                'buy_price' => $product->currentStock['unit_cost'],
                'sell_price' => $product->price,
                'sold_amount' => $sub_total,
                'amount' => $detail->quantity * $product->price,
                'profit' => ($detail->quantity * $product->price) -
                    ($product->currentStock['unit_cost'] * $detail->quantity),
                'capital_invested' => $product->currentStock['unit_cost'] * $detail->quantity,
                'date' => $detail->dates
            ));

        }

        $gross_detail_by_key_date = array();
        foreach ($raw_prices_data as $prices_datum){
            if (array_key_exists('date',$prices_datum)){
                $gross_detail_by_key_date[$prices_datum['date']][] = $prices_datum;
            }
        }

        /*sum total amount for the total*/
        $grand_total_amount = 0;
        foreach ($gross_detail_by_key_date as $key => $value) {
            foreach ($value as $item) {
                $grand_total_amount = $grand_total_amount + $item['amount'];
            }
        }

        /*sum total profit for the total*/
        $grand_total_profit = 0;
        foreach ($gross_detail_by_key_date as $key => $value) {
            foreach ($value as $item) {
                $grand_total_profit = $grand_total_profit + $item['profit'];
            }
        }

        /*sum total buy_price for the total*/
        $grand_total_buy = 0;
        foreach ($gross_detail_by_key_date as $key => $value) {
            foreach ($value as $item) {
                $grand_total_buy = $grand_total_buy + $item['capital_invested'];
            }
        }

        $to_print = array();
        array_push($to_print,array(
            'data' => $gross_detail_by_key_date,
            'total_buy' => $grand_total_buy,
            'total_amount' => $grand_total_amount,
            'total_profit' => $grand_total_profit,
            'from' => $date[0],
            'to' => $date[1]
        ));

        return $to_print;


    }


}
