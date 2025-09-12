<?php

namespace App\Http\Controllers;

use App\CommonFunctions;
use App\Customer;
use App\PriceCategory;
use App\PriceList;
use App\Sale;
use App\SalesCredit;
use App\SalesDetail;
use App\SalesReturn;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

ini_set( 'max_execution_time', 500 );
set_time_limit( 500 );
ini_set( 'memory_limit', '512M' );

class SaleReportController extends Controller {
    public function index() {
        $price_category = PriceCategory::all();
        $customers = Customer::join( 'sales', 'sales.customer_id', '=', 'customers.id' )
        ->join( 'sales_credits', 'sales_credits.sale_id', '=', 'sales.id' )
        ->groupby( 'customers.id' )
        ->get();
        return view( 'sale_reports.index', compact( 'price_category', 'customers' ) );
    }

    protected function reportOption( Request $request ) {
        // dd( 'Data is', $request->all() );
        $date_range = explode( '-', $request->date_range );
        $from = trim( $date_range[ 0 ] );
        $to = trim( $date_range[ 1 ] );
        $pharmacy[ 'name' ] = Setting::where( 'id', 100 )->value( 'value' );
        $pharmacy[ 'logo' ] = Setting::where( 'id', 105 )->value( 'value' );
        $pharmacy[ 'address' ] = Setting::where( 'id', 106 )->value( 'value' );
        $pharmacy[ 'email' ] = Setting::where( 'id', 108 )->value( 'value' );
        $pharmacy[ 'website' ] = Setting::where( 'id', 109 )->value( 'value' );
        $pharmacy[ 'phone' ] = Setting::where( 'id', 107 )->value( 'value' );
        $pharmacy[ 'tin_number' ] = Setting::where( 'id', 102 )->value( 'value' );
        $pharmacy[ 'date_range' ] = 'From' . ' ' . date( 'j M, Y', strtotime( $from ) ) . ' ' . 'To' . ' ' . date( 'j M, Y', strtotime( $to ) );

        switch ( $request->report_option ) {
            case 1:
            $data = $this->cashSaleDetailReport( $from, $to );
            $enable_discount = Setting::where( 'id', 111 )->value( 'value' );
            if ( $data == [] ) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $pdf = PDF::loadView( 'sale_reports.cash_sale_detail_report1_pdf',
            compact( 'data', 'pharmacy', 'enable_discount' ) )
            ->setPaper( 'a4', 'landscape' );
            return $pdf->stream( 'Cash Sale.pdf' );

            case 2:
            $data = $this->cashSaleSummaryReport( $from, $to );
            $pdf = PDF::loadView( 'sale_reports.cash_sale_summary_report_pdf',
            compact( 'data', 'pharmacy' ) )
            ->setPaper( 'a4', 'landscape' );
            return $pdf->stream( 'sale_summary_report.pdf' );

            case 3:
            $data = $this->creditSaleDetailReport( $from, $to );
            $pdf = PDF::loadView( 'sale_reports.credit_sale_detail_report_pdf',
            compact( 'data', 'pharmacy' ) );
            return $pdf->stream( 'Credit Sale.pdf' );

            case 4:
            $data = $this->creditSaleSummaryReport( $from, $to );
            $pdf = PDF::loadView( 'sale_reports.credit_sale_summary_report_pdf',
            compact( 'data', 'pharmacy' ) );
            return $pdf->stream( 'sale_summary_report.pdf' );

            case 5:
            $data = $this->creditPaymentReport( $from, $to );
            $pdf = PDF::loadView( 'sale_reports.credit_payment_report_pdf',
            compact( 'data', 'pharmacy' ) );
            return $pdf->stream( 'credit_payment_report.pdf' );

            case 6:
            $request->validate( [
                'customer_id' => 'required',
            ] );
            $data = $this->customerStatement( $from, $to, $request->customer_id );
            $customer = Customer::where( 'id', $request->customer_id )->value( 'name' );
            $pdf = PDF::loadView( 'sale_reports.customer_payment_statement_pdf',
            compact( 'data', 'pharmacy', 'customer' ) );
            return $pdf->stream( 'customer_payment_statement.pdf' );

            case 8:
            $data = $this->priceListReport( $request->category );
            $view = 'sale_reports.price_list_report_pdf';
            $output = 'price_list_report.pdf';
            $inventory_report = new InventoryReportController();
            $inventory_report->splitPdf( $data, $view, $output );
            break;

            case 11:
            $data = $this->saleReturnReport();
            $pdf = PDF::loadView( 'sale_reports.sale_return_report_pdf',
            compact( 'data', 'pharmacy' ) );
            return $pdf->stream( 'sale_return_report.pdf' );

            case 12:
            $dates = explode( ' - ', $request->date_range );
            $data = $this->salesComparison( $dates );
            if ( $data == [] ) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $pdf = PDF::loadView( 'sale_reports.sales_comparison_report_pdf',
            compact( 'data', 'pharmacy' ) );
            return $pdf->stream( 'sales_comparison_report.pdf' );
            default:

        }
    }

    private function cashSaleDetailReport( $from, $to ) {
        $store_id = current_store_id();
        $from = date( 'Y-m-d', strtotime( $from ) );
        $to = date( 'Y-m-d', strtotime( $to ) );

        $query = SalesDetail::join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->join( 'price_categories', 'price_categories.id', '=', 'sales.price_category_id' )
        ->leftJoin( 'sales_credits', 'sales_credits.sale_id', '=', 'sales.id' )
        ->select(
            'inv_current_stock.*',
            'inv_current_stock.quantity as current_quantity',
            'sales.*',
            'sales_details.*',
            'price_categories.name as price_type'
        )
        ->whereBetween( DB::raw( 'date(sales.date)' ), [ $from, $to ] )
        ->whereRaw( '(sales_details.status != 3 or sales_details.status is null)' )
        ->whereNull( 'sales_credits.sale_id' );

        if ( !is_all_store() ) {
            $query->where( 'inv_current_stock.store_id', $store_id );
        }

        $sale_detail = $query->get();

        $sales = [];
        $grand_total = 0;
        $vat_total = 0;
        $discount_total = 0;
        $sub_total_total = 0;

        foreach ( $sale_detail as $item ) {
            $amount = $item->amount;
            $sub_total = $item->price * $item->quantity;
            $vat = $item->amount - $sub_total;

            $product_name = $item->currentStock[ 'product' ][ 'name' ] . ' ' .
            ( $item->currentStock[ 'product' ][ 'brand' ] ? $item->currentStock[ 'product' ][ 'brand' ] . ' ' : '' ) .
            ( $item->currentStock[ 'product' ][ 'pack_size' ] ? $item->currentStock[ 'product' ][ 'pack_size' ] : '' ) .
            $item->currentStock[ 'product' ][ 'sales_uom' ];

            // Grouping key ( Product + Batch + SoldBy + Price + Date )
            $group_key = $product_name . '|' .
            $item->currentStock[ 'batch_number' ] . '|' .
            $item->sale[ 'user' ][ 'name' ] . '|' .
            $item->price . '|' .
            date( 'Y-m-d', strtotime( $item->sale[ 'date' ] ) );

            if ( !isset( $sales[ $group_key ] ) ) {
                $sales[ $group_key ] = [
                    'receipt_number' => $item->sale[ 'receipt_number' ],
                    'name' => $product_name,
                    'batch_number' => $item->currentStock[ 'batch_number' ],
                    'price_type' => $item->price_type,
                    'date' => date( 'Y-m-d', strtotime( $item->date ) ),
                    'quantity' => 0,
                    'vat' => 0,
                    'discount' => 0,
                    'price' => $item->price,
                    'amount' => 0,
                    'sub_total' => 0,
                    'sold_by' => $item->sale[ 'user' ][ 'name' ],
                    'customer' => $item->sale[ 'customer' ][ 'name' ],
                    'created_at' => date( 'Y-m-d', strtotime( $item->sale[ 'date' ] ) )
                ];
            }

            // Aggregate duplicate rows
            $sales[ $group_key ][ 'quantity' ] += $item->quantity;
            $sales[ $group_key ][ 'sub_total' ] += $sub_total;
            $sales[ $group_key ][ 'vat' ] += $item->vat;
            $sales[ $group_key ][ 'discount' ] += $item->discount;
            $sales[ $group_key ][ 'amount' ] += $amount;

            // Update grand totals
            $grand_total += ( $item->amount ) - ( $item->discount );
            $sub_total_total += $item->amount;
            $vat_total += $item->vat;
            $discount_total += $item->discount;
        }

        // Normalize $sales array
        $sales = array_values( $sales );

        // Group sales by date
        $grouped_sales = [];
        foreach ( $sales as $val ) {
            if ( array_key_exists( 'created_at', $val ) ) {
                $grouped_sales[ $val[ 'created_at' ] ][] = $val;
            }
        }

        // Summaries per date
        $total_by_date = [];
        foreach ( $grouped_sales as $key => $j ) {
            $sb_total = 0;
            $dis_total = 0;
            $va_total = 0;
            $amount_total = 0;
            foreach ( $j as $i ) {
                $sb_total += $i[ 'amount' ];
                $dis_total += $i[ 'discount' ];
                $va_total += $i[ 'vat' ];
                $amount_total += ( $i[ 'amount' ] - $i[ 'discount' ] );
                $date = $i[ 'created_at' ];
            }
            $total_by_date[] = [
                'date' => $date,
                'sub_total' => $sb_total,
                'discount_total' => $dis_total,
                'vat_total' => $va_total,
                'amount_total' => $amount_total
            ];
        }

        $total_grouped_sales = [];
        foreach ( $total_by_date as $val ) {
            if ( array_key_exists( 'date', $val ) ) {
                $total_grouped_sales[ $val[ 'date' ] ][] = $val;
            }
        }

        $to_print = [];
        array_push( $to_print, [ $grouped_sales, $sales, $total_grouped_sales ] );

        return $to_print;
    }

    private function cashSaleSummaryReport( $from, $to )
 {
        $store_id = current_store_id();
        $from = date( 'Y-m-d', strtotime( $from ) );
        $to = date( 'Y-m-d', strtotime( $to ) );

        $query = SalesDetail::join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->join( 'customers', 'customers.id', '=', 'sales.customer_id' )
        ->join( 'users', 'users.id', '=', 'sales.created_by' )
        ->leftJoin( 'sales_credits', 'sales_credits.sale_id', '=', 'sales.id' )
        ->select(
            'sales.receipt_number',
            DB::raw( 'DATE(sales.date) as sale_date' ),
            'customers.name as customer_name',
            'users.name as sold_by',
            DB::raw( 'SUM(sales_details.price * sales_details.quantity) as sub_total' ),
            DB::raw( 'SUM(sales_details.discount) as discount_total' ),
            DB::raw( 'SUM(sales_details.vat) as vat_total' ),
            DB::raw( 'SUM(sales_details.amount) as total_amount' )
        )
        ->whereBetween( DB::raw( 'DATE(sales.date)' ), [ $from, $to ] )
        ->whereRaw( '(sales_details.status != 3 OR sales_details.status IS NULL)' )
        ->whereNull( 'sales_credits.sale_id' );

        if ( !is_all_store() ) {
            $query->where( 'inv_current_stock.store_id', $store_id );
        }

        $query->groupBy( 'sales.receipt_number', 'sale_date', 'customer_name', 'sold_by' )
        ->orderBy( 'sale_date', 'desc' );

        $sale_detail = $query->get();
        // dd($sale_detail);

        // Format ya kurudisha kwa PDF
        $sale_detail_to_pdf = [];
        foreach ( $sale_detail as $item ) {
            $sale_detail_to_pdf[] = [
                'receipt_number' => $item->receipt_number,
                'date'           => $item->sale_date,
                'sub_total'      => round( ( float ) $item->sub_total, 2 ),
                'discount'       => round( ( float ) $item->discount_total, 2 ),
                'vat'            => round( ( float ) $item->vat_total, 2 ),
                'total'          => round( ( float ) $item->total_amount, 2 ),
                'customer_name'  => $item->customer_name,
                'sold_by'        => $item->sold_by,
            ];
        }

        return $sale_detail_to_pdf;
    }

    private function creditSaleDetailReport( $from, $to ) {

        $from = date( 'Y-m-d', strtotime( $from ) );
        $to = date( 'Y-m-d', strtotime( $to ) );

        $sale_detail = SalesDetail::join( 'sales_credits', 'sales_credits.sale_id', '=', 'sales_details.sale_id' )
        ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->whereBetween( DB::raw( 'date(date)' ), [ $from, $to ] )
        ->get();

        $sales = array();
        $grouped_sales = array();
        foreach ( $sale_detail as $item ) {
            $amount = $item->amount - $item->discount;
            if ( $item->price == 0 ) {
                $item->price = 1;
            }
            $vat_percent = $item->vat / $item->price;
            //Here VAT % is calculated.
            $sub_total = ( $amount / ( 1 + $vat_percent ) );
            $vat = $amount - $sub_total;
            array_push( $sales, array(
                'receipt_number' => $item->sale[ 'receipt_number' ],
                'name' => $item->currentStock[ 'product' ][ 'name' ],
                'quantity' => $item->quantity,
                'vat' => $vat,
                'discount' => $item->discount,
                'price' => $item->price,
                'amount' => $amount,
                'sub_total' => $sub_total,
                'paid' => $item->paid_amount,
                'balance' => $item->balance,
                'total_vat' => $item->sale[ 'cost' ][ 'vat' ],
                'total_discount' => $item->sale[ 'cost' ][ 'discount' ],
                'grand_total' => ( $item->sale[ 'cost' ][ 'amount' ] ) - ( $item->sale[ 'cost' ][ 'discount' ] ),
                'sold_by' => $item->sale[ 'user' ][ 'name' ],
                'customer' => $item->sale[ 'customer' ][ 'name' ],
                'created_at' => date( 'Y-m-d', strtotime( $item->sale[ 'date' ] ) )
            ) );

        }

        foreach ( $sales as $val ) {
            if ( array_key_exists( 'receipt_number', $val ) ) {
                $grouped_sales[ $val[ 'receipt_number' ] ][] = $val;

            }

        }

        // $keys = array_keys( $grouped_sales );
        // dd( $keys );
        // foreach ( $keys as $value ) {
        // dd( $value );
        // }
        return $grouped_sales;
    }

    private function creditSaleSummaryReport( $from, $to ) {

        $from = date( 'Y-m-d', strtotime( $from ) );
        $to = date( 'Y-m-d', strtotime( $to ) );

        $sale_detail = SalesDetail::join( 'sales_credits', 'sales_credits.sale_id', '=', 'sales_details.sale_id' )
        ->select( DB::raw( 'sales.id' ),
        DB::raw( 'sum(amount) as amount' ),
        DB::raw( 'sum(vat) as vat' ),
        DB::raw( 'sum(price) as price' ),
        DB::raw( 'sum(discount) as discount' ),
        DB::raw( 'date(date) as dates' ), DB::raw( 'sales.created_by' ), DB::raw( 'name' ) )
        ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->whereBetween( DB::raw( 'date(date)' ), [ $from, $to ] )
        ->join( 'users', 'users.id', '=', 'sales.created_by' )
        ->groupby( 'dates', 'created_by' )
        ->get();

        $sale_detail_to_pdf = array();

        foreach ( $sale_detail as $item ) {
            $value = $item->amount - $item->discount;
            $vat_percent = $item->vat / $item->price;
            $sub_total = ( $value / ( 1 + $vat_percent ) );

            array_push( $sale_detail_to_pdf, array(
                'date' => $item->dates,
                'sub_total' => number_format( ( float )( $sub_total )
                , 2, '.', '' ),
                'sold_by' => $item->name
            ) );

        }

        return $sale_detail_to_pdf;
    }

    private function creditPaymentReport( $from, $to ) {

        $from = date( 'Y-m-d', strtotime( $from ) );
        $to = date( 'Y-m-d', strtotime( $to ) );

        $payments = SalesCredit::join( 'sales', 'sales.id', '=', 'sales_credits.sale_id' )
        ->join( 'customers', 'customers.id', '=', 'sales.customer_id' )
        ->whereBetween( DB::raw( 'date(sales_credits.created_at)' ), [ $from, $to ] )
        ->where( 'paid_amount', '>', 0 )
        ->get();
        return $payments;
    }

    private function customerStatement( $from, $to, $customer_id ) {

        $from = date( 'Y-m-d', strtotime( $from ) );
        $to = date( 'Y-m-d', strtotime( $to ) );

        $data = json_decode( Sale::join( 'sales_credits', 'sales_credits.sale_id', '=', 'sales.id' )
        ->whereBetween( DB::raw( 'date(sales_credits.created_at)' ), [ $from, $to ] )
        ->join( 'customers', 'customers.id', '=', 'sales.customer_id' )
        ->where( 'customer_id', $customer_id )
        // ->where( 'paid_amount', '>', 0 )
        ->get(), true );
        $grouped_data = [];
        foreach ( $data as $val ) {
            if ( array_key_exists( 'receipt_number', $val ) ) {
                $grouped_data[ $val[ 'receipt_number' ] ][] = $val;
            }
        }

        return $grouped_data;
    }

    private function priceListReport( $category ) {
        $max_prices = array();
        $products = PriceList::where( 'price_category_id', $category )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id' )
        ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
        ->Where( 'inv_products.status', '1' )
        ->select( 'inv_products.id as id', 'name' )
        ->groupBy( 'product_id' )
        ->get();

        foreach ( $products as $product ) {
            $data = PriceList::select( 'stock_id', 'price', 'price_category_id' )->where( 'price_category_id', $category )
            ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id' )
            ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
            ->orderBy( 'stock_id', 'desc' )
            ->where( 'product_id', $product->id )
            ->first( 'price' );

            array_push( $max_prices, array(
                'name' => $data->currentStock[ 'product' ][ 'name' ],
                'buy_price' => $data->currentStock[ 'unit_cost' ],
                'sell_price' => $data->price,
                'category_name' => $data->priceCategory[ 'name' ]
            ) );

        }

        $sort_column = array_column( $max_prices, 'name' );
        array_multisort( $sort_column, SORT_ASC, $max_prices );

        return $max_prices;

    }

    private function saleReturnReport() {

        $returns = SalesReturn::join( 'sales_details', 'sales_details.id', '=', 'sales_returns.sale_detail_id' )
        ->where( 'sales_details.status', '=', 3 )
        ->orWhere( 'sales_details.status', '=', 5 )
        ->get();
        foreach ( $returns as $value ) {
            $value->item_returned;
        }
        // dd( json_decode( $returns, true ) );
        return $returns;

    }

    private function salesComparison( $dates ) {
        $date[ 0 ] = date( 'Y-m-d', strtotime( $dates[ 0 ] ) );
        $date[ 1 ] = date( 'Y-m-d', strtotime( $dates[ 1 ] ) );

        $initial = array();
        $dates_only = array();
        $users = array();
        $user_sales = Sale::orderby( 'id', 'asc' )->groupby( 'created_by' )->pluck( 'created_by' );
        $user_sales_date = Sale::select( DB::Raw( 'date(date) as date' ) )
        ->whereBetween( DB::Raw( 'date(date)' ), [ $date[ 0 ], $date[ 1 ] ] )
        ->orderby( 'id', 'asc' )->groupby( DB::Raw( 'date(date)' ) )->get();

        foreach ( $user_sales as $user ) {
            $user_sale = User::find( $user );
            array_push( $users, array(
                'user' => $user_sale->name
            ) );

        }

        /*get only date for comparison and mapping*/
        foreach ( $user_sales_date as $dates ) {
            array_push( $dates_only, $dates->date );
        }

        $sale_detail = SalesDetail::select( DB::Raw( 'sum(amount) as amount' ), 'sale_id' )
        ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->whereBetween( DB::Raw( 'date(date)' ), [ $date[ 0 ], $date[ 1 ] ] )
        ->groupby( 'sale_id' )
        ->get();

        foreach ( $sale_detail as $detail ) {
            array_push( $initial, array(
                'user' => $detail->sale[ 'user' ][ 'name' ],
                'amount' => $detail->amount,
                'from' => $date[ 0 ],
                'to' => $date[ 1 ],
                'date' => date( 'Y-m-d', strtotime( $detail->sale[ 'date' ] ) )
            ) );

        }

        $data_by_key_user = [];
        $data_by_key_user_date = [];

        $sum_by_date = array();
        $sum_by_key = new CommonFunctions();
        foreach ( $initial as $value ) {
            $index = $sum_by_key->sumByKey( $value[ 'date' ], $sum_by_date, 'date' );
            if ( $index < 0 ) {
                $sum_by_date[] = $value;
            } else {
                $sum_by_date[ $index ][ 'amount' ] += $value[ 'amount' ];
            }
        }

        /*sum total by date mapping to user*/
        $data_sum_by_date = array();
        foreach ( $sum_by_date as $val ) {
            if ( array_key_exists( 'date', $val ) ) {
                $data_sum_by_date[ $val[ 'date' ] ][] = $val;
            }
        }

        /*sum by user*/
        $sum_by_user = array();
        foreach ( $initial as $value ) {
            $index = $sum_by_key->sumByKey( $value[ 'user' ], $sum_by_user, 'user' );
            if ( $index < 0 ) {
                $sum_by_user[] = $value;
            } else {
                $sum_by_user[ $index ][ 'amount' ] += $value[ 'amount' ];
            }
        }

        /*sum by user mapping to date*/
        $data_sum_by_user = array();
        foreach ( $sum_by_user as $val ) {
            if ( array_key_exists( 'user', $val ) ) {
                $data_sum_by_user[ $val[ 'user' ] ][] = $val;
            }
        }

        foreach ( $initial as $val ) {
            if ( array_key_exists( 'user', $val ) ) {
                $data_by_key_user[ $val[ 'user' ] ][] = $val;
            } else {
                $data_by_key_user[ $val[ 'user' ] ][] = $val;
            }
        }

        /*bind date to user for mapping*/
        foreach ( $data_by_key_user as $key => $vals ) {
            foreach ( $vals as $val ) {
                if ( array_key_exists( 'date', $val ) ) {
                    $data_by_key_user_date[ $key ][ $val[ 'date' ] ][] = $val;
                }
            }
        }

        $missing = array();

        /*add the missing date*/
        foreach ( $data_by_key_user_date as $key => $outer ) {
            foreach ( $dates_only as $value ) {
                if ( empty( $data_by_key_user_date[ $key ][ $value ] ) ) {
                    $data_by_key_user_date[ $key ][ $value ] = array( array(
                        'user' => $key,
                        'amount' => 0,
                        'from' => 0,
                        'to' => 0,
                        'date' => $value
                    ) );
                    array_push( $missing, $value );
                }
            }
        }

        /*test added*/
        $total = 0;
        foreach ( $data_by_key_user_date as $key => $val ) {
            //key user
            foreach ( $val as $key2 => $v ) {
                //key 2 date
                foreach ( $v as $inner => $datum ) {
                    $total = $total + $datum[ 'amount' ];
                }
                $data_by_key_user_date[ $key ][ $key2 ] = $total;
                //                array_push( $data_by_key_user_date[ $key ][ $key2 ], $total );
                $total = 0;
            }
        }
        /*end test added*/

        /*sum for the total*/
        $grand_total = 0;
        foreach ( $data_sum_by_user as $key => $value ) {
            foreach ( $value as $item ) {
                $grand_total = $grand_total + $item[ 'amount' ];
            }
        }

        $to_print = array();
        array_push( $to_print, array(
            'dates' => $dates_only,
            'data' => $data_by_key_user_date,
            'sum_by_date' => $data_sum_by_date,
            'sum_by_user' => $data_sum_by_user,
            'grand_total' => $grand_total
        ) );
        return $to_print;

    }

}
