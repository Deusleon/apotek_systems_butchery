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
use Illuminate\Support\Facades\Log;

ini_set( 'max_execution_time', 500 );
set_time_limit( 500 );
ini_set( 'memory_limit', '512M' );

class SaleReportController extends Controller {
    public function index() {
        if (!Auth()->user()->checkPermission('View Sales Reports')) {
            abort(403, 'Access Denied');
        }
        $price_category = PriceCategory::all();
        $customers = Customer::join( 'sales', 'sales.customer_id', '=', 'customers.id' )
        ->join( 'sales_credits', 'sales_credits.sale_id', '=', 'sales.id' )
        ->groupby( 'customers.id' )
        ->orderBy('customers.name', 'asc')
        ->get();
        return view( 'sale_reports.index', compact( 'price_category', 'customers' ) );
    }

    protected function reportOption( Request $request ) {
        if (!Auth()->user()->checkPermission('View Sales Reports')) {
            abort(403, 'Access Denied');
        }
        // dd( 'Data is', $request->all() );
        $date_range = explode( '-', $request->date_range );
        $from = trim( $date_range[ 0 ] );
        $to = trim( $date_range[ 1 ] );
        $type = $request->price_type;
        $enable_discount = Setting::where( 'id', 111 )->value( 'value' );
        $pharmacy[ 'name' ] = Setting::where( 'id', 100 )->value( 'value' );
        $pharmacy[ 'logo' ] = Setting::where( 'id', 105 )->value( 'value' );
        $pharmacy[ 'address' ] = Setting::where( 'id', 106 )->value( 'value' );
        $pharmacy[ 'email' ] = Setting::where( 'id', 108 )->value( 'value' );
        $pharmacy[ 'website' ] = Setting::where( 'id', 109 )->value( 'value' );
        $pharmacy[ 'phone' ] = Setting::where( 'id', 107 )->value( 'value' );
        $pharmacy[ 'tin_number' ] = Setting::where( 'id', 102 )->value( 'value' );
        $pharmacy[ 'date_range' ] = 'From' . ' ' . date( 'Y-m-d', strtotime( $from ) ) . ' ' . 'To' . ' ' . date( 'Y-m-d', strtotime( $to ) );

        switch ( $request->report_option ) {
            case 1:
            $data = $this->cashSaleDetailReport( $from, $to );
            if ( empty( $data ) || ( isset( $data[ 0 ] ) && empty( array_filter( $data[ 0 ] ) ) ) ) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $pdf = PDF::loadView( 'sale_reports.cash_sale_detail_report1_pdf',
            compact( 'data', 'pharmacy', 'enable_discount' ) )
            ->setPaper( 'a4', 'landscape' );
            return $pdf->stream( 'Cash_sale_detail_report.pdf' );

            case 2:
            $data = $this->cashSaleSummaryReport( $from, $to );
            if ( empty( $data ) ) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $pdf = PDF::loadView( 'sale_reports.cash_sale_summary_report_pdf',
            compact( 'data', 'pharmacy', 'enable_discount' ) )
            ->setPaper( 'a4', 'landscape' );
            return $pdf->stream( 'Cash_sale_summary_report.pdf' );

            case 3:
            $data = $this->creditSaleDetailReport( $from, $to );
            if ( empty( $data ) ) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $pdf = PDF::loadView( 'sale_reports.credit_sale_detail_report_pdf',
            compact( 'data', 'pharmacy', 'enable_discount' ) )
            ->setPaper( 'a4', 'landscape' );
            return $pdf->stream( 'Credit_sale_detail_report.pdf' );

            case 4:
            $data = $this->creditSaleSummaryReport( $from, $to );
            if ( empty( $data ) ) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $pdf = PDF::loadView( 'sale_reports.credit_sale_summary_report_pdf',
            compact( 'data', 'pharmacy' ) )
            ->setPaper( 'a4', 'landscape' );
            return $pdf->stream( 'Credit_sale_summary_report.pdf' );

            case 5:
            $data = $this->creditPaymentReport( $from, $to );
            if ($data->isEmpty()) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $pdf = PDF::loadView( 'sale_reports.credit_payment_report_pdf',
            compact( 'data', 'pharmacy' ) );
            return $pdf->stream( 'Credit_payment_report.pdf' );

            case 6:
            $request->validate( [
                'customer_id' => 'required',
            ] );
            $data = $this->customerStatement( $from, $to, $request->customer_id );
            if ( empty( $data ) ) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $customer = Customer::where( 'id', $request->customer_id )->value( 'name' );
            $pdf = PDF::loadView( 'sale_reports.customer_payment_statement_pdf',
            compact( 'data', 'pharmacy', 'customer' ) );
            return $pdf->stream( 'Customer_payment_statement.pdf' );
            
            case 7:
            $data = $this->salesTotalReport( $from, $to );
            if ( empty( $data ) || ( isset( $data[ 0 ] ) && empty( array_filter( $data[ 0 ] ) ) ) ) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $pdf = PDF::loadView( 'sale_reports.sales_total_report_pdf',
            compact( 'data', 'pharmacy', 'enable_discount' ) )
            ->setPaper( 'a4', 'landscape' );
            return $pdf->stream( 'Sales_total_report.pdf' );

            case 8:
            if($request->category === 'all'){
                $data = $this->priceListPivot();
                if (empty($data)) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $pdf = Pdf::loadView('sale_reports.price_list_all_categories_report_pdf', 
                compact( 'data', 'pharmacy', 'type' ) );
                return $pdf->stream('Price_list_all_categories_report_pdf.pdf');
            }else{
                $data = $this->priceListReport($request->category);
                if (empty($data)) {
                    return response()->view('error_pages.pdf_zero_data');
                }
                $pdf = Pdf::loadView('sale_reports.price_list_report_pdf', 
                compact( 'data', 'pharmacy', 'type' ) );
                return $pdf->stream('Price_list_report.pdf');
            }
            
            case 9:
            $data = $this->saleDetailReport( $from, $to );
            if ( empty( $data ) || ( isset( $data[ 0 ] ) && empty( array_filter( $data[ 0 ] ) ) ) ) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $pdf = PDF::loadView( 'sale_reports.sale_detail_report_pdf',
            compact( 'data', 'pharmacy', 'enable_discount' ) )
            ->setPaper( 'a4', 'landscape' );
            return $pdf->stream( 'Sales_detail_Report.pdf' );
            
            case 10:
            $data = $this->SaleSummaryReport( $from, $to );
            if ( empty( $data ) ) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $pdf = PDF::loadView( 'sale_reports.sale_summary_report_pdf',
            compact( 'data', 'pharmacy', 'enable_discount' ) )
            ->setPaper( 'a4', 'landscape' );
            return $pdf->stream( 'Sale_summary_report.pdf' );

            case 11:
            $data = $this->saleReturnReport($from, $to);
            if ($data->isEmpty()) {
                return response()->view('error_pages.pdf_zero_data');
            }
            $pdf = PDF::loadView( 'sale_reports.sale_return_report_pdf',
            compact( 'data', 'pharmacy' ) )
            ->setPaper( 'a4', 'landscape' );
            return $pdf->stream( 'Sale_return_report.pdf' );

            case 12:
            $data = $this->salesComparison( $from, $to );
            if ( empty($data) || $data[0]['grand_total'] == 0 ) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $pdf = PDF::loadView( 'sale_reports.sales_comparison_report_pdf',
            compact( 'data', 'pharmacy' ) )
            ->setPaper( 'a4', 'landscape' );
            return $pdf->stream( 'Sales_comparison_report.pdf' );
            
            case 13:
            $data = $this->cashSalesTotalReport( $from, $to );
            if ( empty( $data ) || ( isset( $data[ 0 ] ) && empty( array_filter( $data[ 0 ] ) ) ) ) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $pdf = PDF::loadView( 'sale_reports.cash_sales_total_report_pdf',
            compact( 'data', 'pharmacy', 'enable_discount' ) )
            ->setPaper( 'a4', 'landscape' );
            return $pdf->stream( 'Cash_sales_total_report.pdf' );
            
            case 14:
            $data = $this->creditSalesTotalReport( $from, $to );
            if ( empty( $data ) || ( isset( $data[ 0 ] ) && empty( array_filter( $data[ 0 ] ) ) ) ) {
                return response()->view( 'error_pages.pdf_zero_data' );
            }
            $pdf = PDF::loadView( 'sale_reports.credit_sales_total_report_pdf',
            compact( 'data', 'pharmacy', 'enable_discount' ) )
            ->setPaper( 'a4', 'landscape' );
            return $pdf->stream( 'Credit_sales_total_report.pdf' );

            default:

        }
    }
    private function saleDetailReport( $from, $to ) {
        if (!Auth()->user()->checkPermission('Sales Details Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $from = date( 'Y-m-d', strtotime( $from ) );
        $to = date( 'Y-m-d', strtotime( $to ) );

        $query = SalesDetail::join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->join( 'price_categories', 'price_categories.id', '=', 'sales.price_category_id' )
        ->select(
            'inv_current_stock.*',
            'inv_current_stock.quantity as current_quantity',
            'sales.*',
            'sales_details.*',
            'price_categories.name as price_type'
        )
        ->whereBetween( DB::raw( 'date(sales.date)' ), [ $from, $to ] )
        ->whereRaw( '(sales_details.status != 3 or sales_details.status is null)' )
        ->orderBy('sales.date', 'DESC');

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
    private function cashSaleDetailReport( $from, $to ) {
        if (!Auth()->user()->checkPermission('Cash Sales Details Report')) {
            abort(403, 'Access Denied');
        }
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
        ->whereNull( 'sales_credits.sale_id' )
        ->orderBy('sales.date', 'DESC');

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
            $item->sale[ 'receipt_number' ].'|'.
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
        if (!Auth()->user()->checkPermission('Cash Sales Summary Report')) {
            abort(403, 'Access Denied');
        }
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
    private function cashSalesTotalReport( $from, $to )
    {
        if (!Auth()->user()->checkPermission('Cash Sales Total Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $from = date( 'Y-m-d', strtotime( $from ) );
        $to = date( 'Y-m-d', strtotime( $to ) );

        $query = SalesDetail::join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->join( 'customers', 'customers.id', '=', 'sales.customer_id' )
        ->join( 'users', 'users.id', '=', 'sales.created_by' )
        ->leftJoin( 'sales_credits', 'sales_credits.sale_id', '=', 'sales.id' )
        ->select(
            DB::raw( 'DATE(sales.date) as sale_date' ),
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

        $query->groupBy( 'sale_date' )
        ->orderBy( 'sale_date', 'desc' );

        $sale_detail = $query->get();

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
    private function saleSummaryReport( $from, $to )
    {
        if (!Auth()->user()->checkPermission('Sales Summary Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $from = date( 'Y-m-d', strtotime( $from ) );
        $to = date( 'Y-m-d', strtotime( $to ) );

        $query = SalesDetail::join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->join( 'customers', 'customers.id', '=', 'sales.customer_id' )
        ->join( 'users', 'users.id', '=', 'sales.created_by' )
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
        ->whereRaw( '(sales_details.status != 3 OR sales_details.status IS NULL)' );

        if ( !is_all_store() ) {
            $query->where( 'inv_current_stock.store_id', $store_id );
        }

        $query->groupBy( 'sales.receipt_number', 'sale_date', 'customer_name', 'sold_by' )
        ->orderBy( 'sale_date', 'desc' );

        $sale_detail = $query->get();

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
    private function salesTotalReport( $from, $to )
    {
        if (!Auth()->user()->checkPermission('Sales Total Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $from = date( 'Y-m-d', strtotime( $from ) );
        $to = date( 'Y-m-d', strtotime( $to ) );

        $query = SalesDetail::join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->join( 'customers', 'customers.id', '=', 'sales.customer_id' )
        ->join( 'users', 'users.id', '=', 'sales.created_by' )
        ->select(
            DB::raw( 'DATE(sales.date) as sale_date' ),
            DB::raw( 'SUM(sales_details.price * sales_details.quantity) as sub_total' ),
            DB::raw( 'SUM(sales_details.discount) as discount_total' ),
            DB::raw( 'SUM(sales_details.vat) as vat_total' ),
            DB::raw( 'SUM(sales_details.amount) as total_amount' )
        )
        ->whereBetween( DB::raw( 'DATE(sales.date)' ), [ $from, $to ] )
        ->whereRaw( '(sales_details.status != 3 OR sales_details.status IS NULL)' );

        if ( !is_all_store() ) {
            $query->where( 'inv_current_stock.store_id', $store_id );
        }

        $query->groupBy( 'sale_date' )
        ->orderBy( 'sale_date', 'desc' );

        $sale_detail = $query->get();

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
        if (!Auth()->user()->checkPermission('Credit Sales Details Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $from = date( 'Y-m-d', strtotime( $from ) );
        $to = date( 'Y-m-d', strtotime( $to ) );

        $creditsSumSub = DB::table( 'sales_credits' )
        ->select( 'sale_id', DB::raw( 'SUM(paid_amount) as total_paid' ) )
        ->groupBy( [ 'sale_id' ] );

        $creditsLatestSub = DB::table( 'sales_credits as sc1' )
        ->select( 'sc1.sale_id', 'sc1.balance' )
        ->whereRaw( 'sc1.id = (
                SELECT sc2.id
                FROM sales_credits sc2
                WHERE sc2.sale_id = sc1.sale_id
                ORDER BY sc2.id DESC
                LIMIT 1
            )' );

        $query = SalesDetail::join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->joinSub( $creditsSumSub, 'credits_sum', function( $join ) {
            $join->on( 'credits_sum.sale_id', '=', 'sales_details.sale_id' );
        }
    )
    ->joinSub( $creditsLatestSub, 'credits_latest', function( $join ) {
        $join->on( 'credits_latest.sale_id', '=', 'sales_details.sale_id' );
    }
)
->select(
    'inv_current_stock.store_id as store_id',
    'inv_current_stock.quantity as current_quantity',
    'sales.*',
    'sales_details.*',
    'credits_sum.total_paid',
    'credits_latest.balance as latest_balance'
)
->whereBetween( DB::raw( 'date(sales.date)' ), [ $from, $to ] )
->whereRaw( '(sales_details.status != 3 or sales_details.status is null)' );

if ( !is_all_store() ) {
    $query->where( 'inv_current_stock.store_id', $store_id );
}

$sale_detail = $query->get();

// === 1 ) Build sale-level structure with grouped products ===
$salesById = [];

foreach ( $sale_detail as $item ) {
    $sale = $item->sale;

    $saleId = $sale[ 'id' ];

    // Initialize sale entry once per sale
    if ( !isset( $salesById[ $saleId ] ) ) {
        $salesById[ $saleId ] = [
            'sale_id' => $saleId,
            'receipt_number' => $sale[ 'receipt_number' ] ?? null,
            'date' => date( 'Y-m-d', strtotime( $sale[ 'date' ] ) ),
            'sold_by' => $sale[ 'user' ][ 'name' ] ?? null,
            'customer' => $sale[ 'customer' ][ 'name' ] ?? null,
            'total_paid' => ( float ) ( $item->total_paid ?? 0 ),
            'latest_balance' => ( float ) ( $item->latest_balance ?? 0 ),
            'total_vat' => ( float ) ( $sale[ 'cost' ][ 'vat' ] ?? 0 ),
            'total_discount' => ( float ) ( $sale[ 'cost' ][ 'discount' ] ?? 0 ),
            'grand_total' => ( float ) ( ( $sale[ 'cost' ][ 'amount' ] ?? 0 ) - ( $sale[ 'cost' ][ 'discount' ] ?? 0 ) ),
            'grouped_items' => [], // Products grouped by similarity
        ];
    }

    $product_name = $item->currentStock[ 'product' ][ 'name' ].' '.
    ( $item->currentStock[ 'product' ][ 'brand' ] ? $item->currentStock[ 'product' ][ 'brand' ].' ' : '' ).
    ( $item->currentStock[ 'product' ][ 'pack_size' ] ?? '' ).
    $item->currentStock[ 'product' ][ 'sales_uom' ] ?? '';

    // Create grouping key based on: name, batch, sold_by, price, date
    $groupKey = md5(
        $product_name . '|' .
        ( $item->currentStock->batch_number ?? '' ) . '|' .
        ( $sale[ 'user' ][ 'name' ] ?? '' ) . '|' .
        $item->price . '|' .
        $salesById[$saleId][ 'receipt_number' ].'|'.
        date( 'Y-m-d', strtotime( $sale[ 'date' ] ) )
    );

    // Group similar products
    if ( !isset( $salesById[ $saleId ][ 'grouped_items' ][ $groupKey ] ) ) {
        $salesById[ $saleId ][ 'grouped_items' ][ $groupKey ] = [
            'name' => $product_name ?? null,
            'batch' => $item->currentStock->batch_number,
            'sold_by' => $sale[ 'user' ][ 'name' ] ?? null,
            'price' => $item->price,
            'quantity' => 0, // Will be summed
            'amount' => 0,   // Will be summed
            'sub_total' => 0, // Will be calculated
            'vat' => 0,      // Will be summed
            'discount' => 0, // Will be summed
            'date' => date( 'Y-m-d', strtotime( $sale[ 'date' ] ) ),
        ];
    }

    // Sum quantities and amounts for grouped products
    $salesById[ $saleId ][ 'grouped_items' ][ $groupKey ][ 'quantity' ] += $item->quantity;
    $salesById[ $saleId ][ 'grouped_items' ][ $groupKey ][ 'amount' ] += $item->amount;
    $salesById[ $saleId ][ 'grouped_items' ][ $groupKey ][ 'vat' ] += $item->vat;
    $salesById[ $saleId ][ 'grouped_items' ][ $groupKey ][ 'discount' ] += $item->discount;

    // Recalculate sub_total based on grouped quantity and price
    $salesById[ $saleId ][ 'grouped_items' ][ $groupKey ][ 'sub_total' ] =
    $salesById[ $saleId ][ 'grouped_items' ][ $groupKey ][ 'price' ] *
    $salesById[ $saleId ][ 'grouped_items' ][ $groupKey ][ 'quantity' ];
}

// Convert grouped_items from associative array to indexed array
foreach ( $salesById as $sid => $sale ) {
    $salesById[ $sid ][ 'items' ] = array_values( $sale[ 'grouped_items' ] ?? [] );
    unset( $salesById[ $sid ][ 'grouped_items' ] );
}

// === 2 ) Group sales by date and compute daily totals ===
$groupedByDate = [];

foreach ( $salesById as $sale ) {
    $date = $sale[ 'date' ];

    if ( !isset( $groupedByDate[ $date ] ) ) {
        $groupedByDate[ $date ] = [
            'date' => $date,
            'sales' => [],
            'totals' => [
                'total_sales' => 0,
                'total_paid' => 0,
                'total_balance' => 0,
                'total_vat' => 0,
                'total_discount' => 0,
                'grand_total' => 0,
                'count_sales' => 0,
            ],
        ];
    }

    // Append sale ( with grouped items )
    $groupedByDate[ $date ][ 'sales' ][ $sale[ 'sale_id' ] ] = $sale;

    // Update totals using grouped item amounts
    $saleItemsAmount = 0;
    foreach ( $sale[ 'items' ] as $line ) {
        $saleItemsAmount += ( float ) ( $line[ 'amount' ] ?? 0 );
    }

    $groupedByDate[ $date ][ 'totals' ][ 'total_sales' ] += $saleItemsAmount;
    $groupedByDate[ $date ][ 'totals' ][ 'total_paid' ] += ( float ) $sale[ 'total_paid' ];
    $groupedByDate[ $date ][ 'totals' ][ 'total_balance' ] += ( float ) $sale[ 'latest_balance' ];
    $groupedByDate[ $date ][ 'totals' ][ 'total_vat' ] += ( float ) $sale[ 'total_vat' ];
    $groupedByDate[ $date ][ 'totals' ][ 'total_discount' ] += ( float ) $sale[ 'total_discount' ];
    $groupedByDate[ $date ][ 'totals' ][ 'grand_total' ] += ( float ) $sale[ 'grand_total' ];
    $groupedByDate[ $date ][ 'totals' ][ 'count_sales' ] += 1;
}

// === 3 ) BONUS: Group similar products across ALL sales for the date ===
foreach ( $groupedByDate as $date => &$dayData ) {
    $crossSaleGrouped = [];

    foreach ( $dayData[ 'sales' ] as $sale ) {
        $receiptNo = $sale[ 'receipt_number' ] ?? null;

        foreach ( $sale[ 'items' ] as $item ) {
            // allow item to contain detail_ids from per-sale grouping
            $itemDetailIds = $item[ 'detail_ids' ] ?? [];

            $crossKey = md5( implode( '|', [
                strtolower( preg_replace( '/\s+/', ' ', trim( $item[ 'name' ] ) ) ),
                ( string )( $item[ 'batch' ] ?? '' ),
                strtolower( ( string )( $item[ 'sold_by' ] ?? '' ) ),
                number_format( ( float )$item[ 'price' ], 2, '.', '' ),
                $date,
                (string)$receiptNo
            ] ) );

            if ( !isset( $crossSaleGrouped[ $crossKey ] ) ) {
                $crossSaleGrouped[ $crossKey ] = [
                    'name' => $item[ 'name' ],
                    'batch' => $item[ 'batch' ],
                    'sold_by' => $item[ 'sold_by' ],
                    'price' => ( float )$item[ 'price' ],
                    'receipt'=> $receiptNo,
                    'quantity' => 0.0,
                    'amount' => 0.0,
                    'sub_total' => 0.0,
                    'vat' => 0.0,
                    'discount' => 0.0,
                    'date' => $date,
                    'sale_count' => 0,
                    'receipts' => [],
                    'detail_ids' => [],
                ];
            }

            $crossSaleGrouped[$crossKey]['quantity'] += (float)$item['quantity'];
            $crossSaleGrouped[$crossKey]['amount'] += (float)$item['amount'];
            $crossSaleGrouped[$crossKey]['vat'] += (float)$item['vat'];
            $crossSaleGrouped[$crossKey]['discount'] += (float)$item['discount'];
            $crossSaleGrouped[$crossKey]['sub_total'] = $crossSaleGrouped[$crossKey]['price'] * $crossSaleGrouped[$crossKey]['quantity'];

            if ($receiptNo && !in_array($receiptNo, $crossSaleGrouped[$crossKey]['receipts'])) {
                $crossSaleGrouped[$crossKey]['receipts'][] = $receiptNo;
            }

            // merge detail_ids uniquely
            foreach ($itemDetailIds as $did) {
                if (!in_array($did, $crossSaleGrouped[$crossKey]['detail_ids'])) {
                    $crossSaleGrouped[$crossKey]['detail_ids'][] = $did;
                }
            }
        }
    }

    // finalize sale_count
    foreach ($crossSaleGrouped as $k => $v) {
        $crossSaleGrouped[$k]['sale_count'] = count($v['receipts']);
    }

    $dayData['grouped_data'] = array_values($crossSaleGrouped);
}
unset($dayData);

    // Return as indexed array (dates descending)
    krsort($groupedByDate);
    return array_values($groupedByDate);
    }
    private function creditSaleSummaryReport($from, $to)
    {
        if (!Auth()->user()->checkPermission('Credit Sales Summary Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $from = date('Y-m-d', strtotime($from));
        $to   = date('Y-m-d', strtotime($to));

        // Subquery: Total credits paid in each sale
        $creditsSumSub = DB::table('sales_credits')
            ->select('sale_id', DB::raw('SUM(paid_amount) as total_paid'))
            ->groupBy(['sale_id']);

        // Subquery: Latest Balance
        $creditsLatestSub = DB::table('sales_credits as sc1')
            ->select('sc1.sale_id', 'sc1.balance')
            ->whereRaw('sc1.id = (
                SELECT sc2.id
                FROM sales_credits sc2
                WHERE sc2.sale_id = sc1.sale_id
                ORDER BY sc2.id DESC
                LIMIT 1
            )');

        $query = SalesDetail::join('sales', 'sales.id', '=', 'sales_details.sale_id')
            ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id')
            ->join('users', 'users.id', '=', 'sales.created_by')
            ->join('customers', 'customers.id', '=', 'sales.customer_id')
            ->joinSub($creditsSumSub, 'credits_sum', function($join) {
                $join->on('credits_sum.sale_id', '=', 'sales.id');
            })
            ->joinSub($creditsLatestSub, 'credits_latest', function($join) {
                $join->on('credits_latest.sale_id', '=', 'sales.id');
            })
            ->select(
                'sales.id as sale_id',
                'sales.date as sales_date',
                'sales.receipt_number',
                'customers.name as customer_name',
                'users.name as sold_by',
                DB::raw('SUM(sales_details.amount) as total'),
                DB::raw('COALESCE(credits_sum.total_paid, 0) as paid'),
                DB::raw('COALESCE(credits_latest.balance, 0) as balance'),
                DB::raw('date(sales.date) as date')
            )
            ->whereBetween(DB::raw('date(sales.date)'), [$from, $to])
            ->groupBy(
                'sales.id',
                'sales.receipt_number',
                'customers.name',
                'users.name',
                'sales.date',
                'credits_sum.total_paid',
                'credits_latest.balance'
            )
            ->orderBy('sales.date', 'desc')
            ->orderBy('sales.id', 'desc');

        if (!is_all_store()) {
            $query->where('inv_current_stock.store_id', $store_id);
        }

        $sale_detail = $query->get();

        $data = [];
        $grand_total   = 0;
        $total_paid    = 0;
        $total_balance = 0;

        foreach ($sale_detail as $item) {
            $status = 'Unpaid';
            if ($item->balance <= 0) {
                $status = 'Paid';
            } elseif ($item->paid > 0 && $item->balance > 0) {
                $status = 'Partial';
            }

            $data[] = [
                'sales_date' => $item->sales_date,
                'receipt_number' => $item->receipt_number,
                'customer_name'  => $item->customer_name,
                'total'          => (float)$item->total,
                'paid'           => (float)$item->paid,
                'balance'        => (float)$item->balance,
                'sold_by'        => $item->sold_by,
                'status'         => $status,
            ];

            // totals for footer
            $grand_total   += $item->total;
            $total_paid    += $item->paid;
            $total_balance += $item->balance;
        }

        return [
            'info'          => $data,
            'grand_total'   => $grand_total,
            'total_paid'    => $total_paid,
            'total_balance' => $total_balance,
        ];
    }
    private function creditSalesTotalReport( $from, $to )
    {
        if (!Auth()->user()->checkPermission('Credit Sales Total Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $from = date('Y-m-d', strtotime($from));
        $to = date('Y-m-d', strtotime($to));

        // Step 1: pre-aggregate sales_credits
        $creditsSub = DB::table('sales_credits')
            ->select('sale_id', DB::raw('SUM(paid_amount) as total_paid'))
            ->groupBy(['sale_id']);

        $query = SalesDetail::join('sales', 'sales.id', '=', 'sales_details.sale_id')
            ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id')
            ->join('customers', 'customers.id', '=', 'sales.customer_id')
            ->join('users', 'users.id', '=', 'sales.created_by')
            ->joinSub($creditsSub, 'credits', function($join) {
                $join->on('credits.sale_id', '=', 'sales_details.sale_id');
            })
            ->select(
                DB::raw('DATE(sales.date) as sale_date'),
                DB::raw('SUM(sales_details.price * sales_details.quantity) as sub_total'),
                DB::raw('SUM(sales_details.discount) as discount_total'),
                DB::raw('SUM(sales_details.vat) as vat_total'),
                DB::raw('SUM(sales_details.amount) as total_amount'),
                'credits.total_paid'
            )
            ->whereBetween(DB::raw('DATE(sales.date)'), [$from, $to])
            ->whereRaw('(sales_details.status != 3 OR sales_details.status IS NULL)');

        if (!is_all_store()) {
            $query->where('inv_current_stock.store_id', $store_id);
        }

        $query->groupBy('sale_date')
            ->orderBy('sale_date', 'desc');

        $sale_detail = $query->get();


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
    private function creditPaymentReport($from, $to) {
        if (!Auth()->user()->checkPermission('Credit Payments Report')) {
            abort(403, 'Access Denied');
        }
    $store_id = current_store_id();
    $from = date('Y-m-d', strtotime($from));
    $to   = date('Y-m-d', strtotime($to));

    $query = SalesCredit::join('sales', 'sales.id', '=', 'sales_credits.sale_id')
        ->join('customers', 'customers.id', '=', 'sales.customer_id')
        ->join('users', 'users.id', '=', 'sales.created_by')
        ->whereBetween(DB::raw('date(sales_credits.created_at)'), [$from, $to])
        ->where('sales_credits.paid_amount', '>', 0)
        ->select(
            'sales_credits.*',
            'sales.receipt_number',
            'customers.name as customer_name',
            'users.name as received_by'
        );

    // Filter by store without causing duplicates
    if (!is_all_store()) {
        $query->whereExists(function($q) use ($store_id) {
            $q->select(DB::raw(1))
              ->from('sales_details')
              ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id')
              ->whereColumn('sales_details.sale_id', 'sales_credits.sale_id')
              ->where('inv_current_stock.store_id', $store_id);
        });
    }

    $payments = $query->get();

    return $payments;
    }
    private function customerStatement($from, $to, $customer_id)
    {
        if (!Auth()->user()->checkPermission('Customer Payment Statement')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $from = date('Y-m-d', strtotime($from));
        $to   = date('Y-m-d', strtotime($to));

        $query = Sale::join('sales_credits', 'sales_credits.sale_id', '=', 'sales.id')
            ->join('customers', 'customers.id', '=', 'sales.customer_id')
            ->join('users', 'users.id', '=', 'sales.created_by')
            ->whereBetween(DB::raw('date(sales_credits.created_at)'), [$from, $to])
            ->where('sales.customer_id', $customer_id)
            ->select(
                'sales.*',
                'customers.name as customer_name',
                'users.name as received_by',
                'sales_credits.created_at as created_at',
                'sales_credits.paid_amount',
                'sales_credits.balance'
            );

        // Filter by store without causing duplication
        if (!is_all_store()) {
            $query->whereExists(function ($q) use ($store_id) {
                $q->select(DB::raw(1))
                ->from('sales_details')
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id')
                ->whereColumn('sales_details.sale_id', 'sales.id')
                ->where('inv_current_stock.store_id', $store_id);
            });
        }

        // fetch as array
        $data = $query->get()->toArray();

        // first: group by receipt_number and sum total_paid across all rows
        $grouped_data = [];
        $total_paid = 0.0;

        foreach ($data as $val) {
            $receipt = $val['receipt_number'] ?? 'UNKNOWN';
            $grouped_data[$receipt][] = $val;

            $total_paid += (float)($val['paid_amount'] ?? 0);
        }

        // second: compute total_balance = sum of **latest balance per receipt**
        $total_balance = 0.0;
        foreach ($grouped_data as $receipt => $rows) {
            // find the latest credit row in $rows by created_at
            $latest = null;
            foreach ($rows as $r) {
                // ensure created_at exists; compare timestamps
                if ($latest === null) {
                    $latest = $r;
                } else {
                    $t1 = strtotime($r['created_at'] ?? '1970-01-01 00:00:00');
                    $t2 = strtotime($latest['created_at'] ?? '1970-01-01 00:00:00');
                    if ($t1 > $t2) {
                        $latest = $r;
                    }
                }
            }

            $total_balance += (float)($latest['balance'] ?? 0);
        }

        return [
            'grouped_data'   => $grouped_data,
            'total_paid'     => $total_paid,
            'total_balance'  => $total_balance,
        ];
    }
    private function priceListReport( $category ) {
        if (!Auth()->user()->checkPermission('Price List Report')) {
            abort(403, 'Access Denied');
        }
        $max_prices = array();
        $products = PriceList::where( 'price_category_id', $category )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id' )
        ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
        ->Where( 'inv_products.status', '1' )
        ->select( 'inv_products.id as id', 'inv_products.name', 'inv_products.brand', 'inv_products.pack_size', 'inv_products.sales_uom' )
        ->groupBy( 'product_id' )
        ->get();

        foreach ( $products as $product ) {
            $data = PriceList::select( 'stock_id', 'price', 'price_category_id' )->where( 'price_category_id', $category )
            ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id' )
            ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
            ->orderBy( 'stock_id', 'desc' )
            ->where( 'product_id', $product->id )
            ->first( 'price' );

            $product_name = $data->currentStock[ 'product' ][ 'name' ].' '.($data->currentStock[ 'product' ][ 'brand' ].' ' ?? '').$data->currentStock[ 'product' ][ 'pack_size' ].$data->currentStock[ 'product' ][ 'sales_uom' ];
            array_push( $max_prices, array(
                'name' => $product_name,
                'buy_price' => $data->currentStock[ 'unit_cost' ],
                'sell_price' => $data->price,
                'category_name' => $data->priceCategory[ 'name' ]
            ) );

        }

        $sort_column = array_column( $max_prices, 'name' );
        array_multisort( $sort_column, SORT_ASC, $max_prices );

        return $max_prices;

    }
    private function priceListPivot()
    {
        $categories = PriceList::with('priceCategory')
            ->select('price_category_id')
            ->distinct()
            ->get()
            ->pluck('priceCategory.name', 'price_category_id')
            ->toArray();

        $products = DB::table('inv_products')
            ->join('inv_current_stock', 'inv_current_stock.product_id', '=', 'inv_products.id')
            ->where('inv_products.status', 1)
            ->select('inv_products.id', 'inv_products.name', 'inv_products.brand', 'inv_products.pack_size', 'inv_products.sales_uom', 'inv_current_stock.unit_cost')
            ->groupBy(['inv_products.id', 'inv_products.name', 'inv_current_stock.unit_cost'])
            ->get();

        $result = [];
        
        foreach ($products as $i => $product) {
            $product_name = trim(
                $product->name . ' ' .
                ($product->brand.' ' ?? '') . 
                ($product->pack_size ?? '') .
                ($product->sales_uom ?? '')
            );
            $row = [
                '#'          => $i + 1,
                'name'       => $product_name,
                'buy_price'  => $product->unit_cost,
            ];

            foreach ($categories as $catId => $catName) {
                $row[$catName] = null;
            }

            $prices = PriceList::whereHas('priceCategory') 
                    ->whereHas('currentStock', function ($q) use ($product) {
                        $q->where('product_id', $product->id);
                    })
                    ->with('priceCategory')
                    ->get();

            foreach ($prices as $price) {
                if ($price->priceCategory) { 
                    $row[$price->priceCategory->name] = $price->price;
                }
            }

            $result[] = $row;
        }

        return [
            'categories' => $categories,
            'products'   => $result,
        ];
    }
    private function saleReturnReport($from, $to) {
        if (!Auth()->user()->checkPermission('Sales Return Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $query = SalesReturn::join( 'sales_details', 'sales_details.id', '=', 'sales_returns.sale_detail_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->whereBetween(DB::raw('date(sales_returns.date)'), [$from, $to])
        ->where(function($q) {
            $q->where('sales_details.status', 3)
              ->orWhere('sales_details.status', 5);
        });
        
        if (!is_all_store()) {
            $query->where('inv_current_stock.store_id', $store_id);
        }

        $returns = $query->get();
        // dd($returns);
        return $returns;
    }
    private function salesComparison( $from, $to ) {
        if (!Auth()->user()->checkPermission('Sales Comparison Report')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $initial = array();
        $dates_only = array();
        $users = array();
        $query1 = Sale::join( 'sales_details', 'sales.id', '=', 'sales_details.sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->orderby( 'sales.id', 'asc' )->groupby( 'sales.created_by' );
        $query2 = Sale::join( 'sales_details', 'sales.id', '=', 'sales_details.sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->select( DB::Raw( 'date( date ) as date' ) )
        ->whereBetween( DB::Raw( 'date( date )' ), [ $from, $to ] )
        ->orderby( 'sales.id', 'asc' )->groupby( DB::Raw( 'date( date )' ) );

        if (!is_all_store()) {
            $query1->where('inv_current_stock.store_id', $store_id);
            $query2->where('inv_current_stock.store_id', $store_id);
        }
        $user_sales = $query1->pluck('sales.created_by');;
        $user_sales_date = $query2->get();

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

        $query = SalesDetail::select( DB::Raw( 'sum( amount ) as amount' ), 'sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->whereBetween( DB::Raw( 'date( date )' ), [ $from, $to ] )
        ->groupby( 'sale_id' );
        
        if (!is_all_store()) {
            $query->where('inv_current_stock.store_id', $store_id);
        }
        
        $sale_detail = $query->get();

        foreach ( $sale_detail as $detail ) {
            array_push( $initial, array(
                'user' => $detail->sale[ 'user' ][ 'name' ],
                'amount' => $detail->amount,
                'from' => $from,
                'to' => $to,
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
