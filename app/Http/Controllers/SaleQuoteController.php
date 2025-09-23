<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\Customer;
use App\PriceCategory;
use App\Sale;
use App\SalesCredit;
use App\SalesDetail;
use App\SalesQuote;
use App\SalesQuoteDetail;
use App\Setting;
use App\StockTracking;
use Dompdf\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\View;

class SaleQuoteController extends Controller {

    public function index() {
        if (!Auth()->user()->checkPermission('View Sales Orders')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $vat = Setting::where( 'id', 120 )->value( 'value' ) / 100;
        //Get VAT %
        $enable_discount = Setting::where( 'id', 111 )->value( 'value' );

        /*get default Price Category*/
        $default_sale_type = Setting::where( 'id', 125 )->value( 'value' );
        $sale_type = PriceCategory::where( 'name', $default_sale_type )->first();

        if ( $sale_type != null ) {
            $default_sale_type = $sale_type->id;
        } else {
            $default_sale_type = PriceCategory::first()->value( 'id' );
        }

        $price_category = PriceCategory::all();
        $sale_quotes = SalesQuote::orderBy( 'id', 'DESC' )
        ->where( 'store_id', '=', $store_id )
        ->get();

        $customers = Customer::orderBy( 'name', 'ASC' )->get();
        $current_stock = CurrentStock::all();
        $count = $sale_quotes->count();
        return View::make( 'sales.sale_quotes.index' )
        ->with( compact( 'vat' ) )
        ->with( compact( 'count' ) )
        ->with( compact( 'sale_quotes' ) )
        ->with( compact( 'customers' ) )
        ->with( compact( 'price_category' ) )
        ->with( compact( 'default_sale_type' ) )
        ->with( compact( 'current_stock' ) )
        ->with( compact( 'enable_discount' ) );
    }

    public function orderList() {
        $store_id = current_store_id();
        $vat = Setting::where( 'id', 120 )->value( 'value' ) / 100;
        //Get VAT %
        $enable_discount = Setting::where( 'id', 111 )->value( 'value' );

        /*get default Price Category*/
        $default_sale_type = Setting::where( 'id', 125 )->value( 'value' );
        $sale_type = PriceCategory::where( 'name', $default_sale_type )->first();

        if ( $sale_type != null ) {
            $default_sale_type = $sale_type->id;
        } else {
            $default_sale_type = PriceCategory::first()->value( 'id' );
        }

        $price_category = PriceCategory::all();
        $sale_quotes = SalesQuote::orderBy( 'sales_quotes.id', 'DESC' )
        ->join( 'sales_quote_details', 'sales_quote_details.quote_id', '=', 'sales_quotes.id' )
        ->select( 'sales_quote_details.status' )
        ->where( 'store_id', '=', $store_id )
        ->get();
        $customers = Customer::orderBy( 'name', 'ASC' )->get();
        $current_stock = CurrentStock::all();
        $count = $sale_quotes->count();
        return View::make( 'sales.sale_quotes.index_quotes' )
        ->with( compact( 'vat' ) )
        ->with( compact( 'count' ) )
        ->with( compact( 'sale_quotes' ) )
        ->with( compact( 'customers' ) )
        ->with( compact( 'price_category' ) )
        ->with( compact( 'default_sale_type' ) )
        ->with( compact( 'current_stock' ) )
        ->with( compact( 'enable_discount' ) );
    }

    public function getQuotes( Request $request ) {
        $store_id = current_store_id();
        $date_range = explode( '-', $request->date );
        $from = date( 'Y-m-d 00:00:00', strtotime( trim( $date_range[ 0 ] ) ) );
        $to = date( 'Y-m-d 23:59:59', strtotime( trim( $date_range[ 1 ] ) ) );
        $sale_quotes = SalesQuote::with( [ 'cost', 'customer', 'details' ] )
        ->select( 'sales_quotes.*' )
        ->selectSub( function ( $query ) {
            $query->from( 'sales_quote_details' )
            ->select( 'status' )
            ->whereColumn( 'sales_quote_details.quote_id', 'sales_quotes.id' )
            ->orderBy( 'sales_quote_details.id', 'asc' ) 
            ->limit( 1 );
        }
        , 'status' )
        ->where( 'store_id', $store_id )
        ->whereBetween( 'date', [ $from, $to ] )
        ->whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('sales_quote_details')
                ->whereColumn('sales_quote_details.quote_id', 'sales_quotes.id')
                ->where('sales_quote_details.status', 1);
        })
        ->orderBy( 'id', 'desc' )
        ->get();
        return response()->json( $sale_quotes, 200 );
    }

    public function storeQuote( Request $request ) {
        if ( $request->ajax() ) {
            $this->store( $request );
            return response()->json( [
                'redirect_to' => route( 'getQuoteReceipt' )
            ] );
        }
    }

    //Edit Sales Order
    public function updateQuote( Request $request ) {
        $quantity = $request->quantity;
        $amount = $request->quantity * $request->price;
        $id = $request->id;

        $updateOrder = DB::table( 'sales_quote_details' )
        ->where( 'id', $id )
        ->update( [
            'quantity'=>$quantity,
            'amount'=>$amount
        ] );

        if ( $updateOrder ) {
            Session::flash( 'alert-success', 'Order updated successfully!' );
            return redirect()->back();
        }

        Session::flash( 'alert-danger', 'Oop something went wrong!' );

        return redirect()->back();

    }

    public function addQuoteItem( Request $request ) {
        $vatRate = Setting::where( 'id', 120 )->value( 'value' ) / 100;
        $price   = $request->price;

        $existingItem = SalesQuoteDetail::where( 'quote_id', $request->quote_id )
        ->where( 'product_id', $request->product_id )
        ->where( 'status', 1 )
        ->first();

        if ( $existingItem ) {
            // update quantity na amount
            $existingItem->quantity += $request->quantity;
            $existingItem->vat      = ($existingItem->price * $vatRate)*$existingItem->quantity;
            $existingItem->amount   = $existingItem->price * $existingItem->quantity;
            $existingItem->updated_at = now();
            $existingItem->updated_by = Auth::user()->id;
            $existingItem->save();

            // Fetch updated quote details as JSON
            $data = $this->fetchUpdates( $request->quote_id );

            // Include status and message
            return response()->json( [
                'status' => 'success',
                'message' => 'Order item added successfully',
                'data' => $data->original ?? $data
            ] );
        }

        // If not existing, insert new
        $newItem = new SalesQuoteDetail();
        $newItem->quote_id   = $request->quote_id;
        $newItem->product_id = $request->product_id;
        $newItem->quantity   = $request->quantity;
        $newItem->price      = $price;
        $newItem->vat        = ($price * $vatRate)*$request->quantity;
        $newItem->amount     = $price * $request->quantity;
        $newItem->status     = 1;
        $newItem->updated_at = now();
        $newItem->updated_by = Auth::user()->id;
        $newItem->save();

        // Fetch updated quote details as JSON
        $data = $this->fetchUpdates( $request->quote_id );

        // Include status and message
        return response()->json( [
            'status' => 'success',
            'message' => 'Order item added successfully',
            'data' => $data->original ?? $data
        ] );
    }

    public function updateQuoteItem( Request $request ) {
        $vatRate = Setting::where( 'id', 120 )->value( 'value' ) / 100;
        $quantity = $request->quantity;
        $price = $request->price;
        $vatAmount = ((float)$price*$vatRate)*$quantity;
        $amount = $quantity * $price;
        $id = $request->id;
        $quote_id = $request->quote_id;

        $updateOrder = DB::table( 'sales_quote_details' )
        ->where( 'id', $id )
        ->update( [
            'quantity'=>$quantity,
            'price'=>$price,
            'vat'=>$vatAmount,
            'amount'=>$amount
        ] );

        if ( $updateOrder ) {
            // Fetch updated quote details as JSON
            $data = $this->fetchUpdates( $quote_id );

            // Include status and message
            return response()->json( [
                'status' => 'success',
                'message' => 'Order item updated successfully',
                'data' => $data->original ?? $data
            ] );
        }

        return [ 'status' => 'failed', 'message'=>'Failed to update order item' ];
    }

    public function deleteQuoteItem( Request $request ) {
        $id = $request->id;

        $removeItem = SalesQuoteDetail::destroy( $id );
        if ( $removeItem ) {
            // Fetch updated quote details as JSON
            $data = $this->fetchUpdates( $request->quote_id );

            // Include status and message
            return response()->json( [
                'status' => 'success',
                'message' => 'Order item removed successfully',
                'data' => $data->original ?? $data
            ] );
        }
        // Include status and message
        return response()->json( [
            'status' => 'failed',
            'message' => 'Failed to remove order item',
        ] );
    }

    public function changeCustomer( Request $request ) {
        $updateCustomer = DB::table( 'sales_quotes' )
        ->where( 'id', $request->id )
        ->update( [
            'customer_id'=>$request->customer_id
        ] );

        if ( $updateCustomer ) {
            return [ 'status' => 'success', 'message'=>'Customer changed successful' ];
        }
    }

    public function changePriceCatg( Request $request ) {
        $updatePrice = DB::table( 'sales_quotes' )
        ->where( 'id', $request->id )
        ->update( [
            'price_category_id'=>$request->price_category_id
        ] );

        if ( $updatePrice ) {
            return [ 'status' => 'success', 'message'=>'Price changed successful' ];
        }
    }

    public function saveFinalQuote(Request $request)
    {
        $details = DB::table('sales_quote_details')
            ->where('quote_id', $request->id)
            ->where('status', 1)
            ->get();

        if ($details->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Quote not found!'
            ]);
        }

        $discount = (float) str_replace(',', '', $request->discount);
        $total = $details->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        foreach ($details as $item) {
            $line_total = $item->price * $item->quantity;
            $discount_share = ($line_total / $total) * $discount;

            DB::table('sales_quote_details')
                ->where('id', $item->id)
                ->update([
                    'discount' => round($discount_share, 2),
                    // 'amount'   => ($line_total + $item->vat) - $discount_share
                ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Order updated successfully',
            'redirect' => route('sale-quotes.order_list')
        ]);
    }

    //Convert Sales Order to Sale ( Cash or Credit )
    public function convertToSale( $id ) {
    }

    //Store sales order data
    public function store( Request $request ) {
        $store_id = current_store_id();
        date_default_timezone_set( 'Africa/Nairobi' );
        
        $cart = json_decode( $request->cart, true );
        $vat = Setting::where( 'id', 120 )->value( 'value' ) / 100;
        $quote_number = strtoupper( substr( md5( microtime() ), rand( 0, 26 ), 8 ) );
        $discount = $request->discount_amount;
        $date = date( 'Y-m-d H:i:s' );
        $total = 0;
        $totalData = count($cart);

        if ( !$cart ) {
            session()->flash( 'alert-danger', 'You can not save an empty Cart!' );
        } else {
            //calculating the Total Amount
            foreach ( $cart as $bought ) {
                $total += $bought[ 'amount' ];
            }
            //Saving Sale-Quote Summary and Get its ID
            $quote = DB::table( 'sales_quotes' )->insertGetId( array(
                'remark' => $request->remark,
                'quote_number' => $quote_number,
                'customer_id' => $request->customer_id,
                'price_category_id' => $request->price_category_id,
                'date' => $date,
                'created_by' => Auth::User()->id,
                'store_id'=>$store_id
            ) );

            //Saving Quote Details
            foreach ( $cart as $bought ) {
                $bought[ 'quantity' ] = str_replace( ',', '', $bought[ 'quantity' ] );
                $qty = $bought['quantity'];
                $unit_discount = (($bought['amount'] / ($total ?: 1)) * $discount) / $bought['quantity'];
                $details = new SalesQuoteDetail;
                $details->quote_id = $quote;
                $details->product_id = $bought[ 'product_id' ];
                $details->quantity = $bought[ 'quantity' ];
                $details->price = $bought['price'];
                $details->vat = ($details->price * $vat)*$bought['quantity'];
                $details->amount = ($bought['price'] * $bought['quantity']) + $details->vat;
                $details->discount = $unit_discount * $qty;
                $details->save();
            }

        }

    }

    //Retrieves update sales details
    public function update( $id ) {
        //1. Retrieve Item Details
        $sales_details = DB::table( 'sales_quote_details' )
        ->join( 'inv_products', 'sales_quote_details.product_id', '=', 'inv_products.id' )
        ->select( 'sales_quote_details.id', 'sales_quote_details.quote_id', 'sales_quote_details.product_id', 'inv_products.name', 'inv_products.brand', 'inv_products.pack_size', 'inv_products.sales_uom', 'sales_quote_details.price', 'sales_quote_details.quantity', 'sales_quote_details.vat', 'sales_quote_details.discount', 'sales_quote_details.amount' )
        ->where( 'sales_quote_details.quote_id', '=', $id )
        ->where( 'sales_quote_details.status', 1 )
        ->get();

        $quote_id = $id;

        $order = SalesQuote::where( 'id', $id )->first();
        $quote_details = DB::table( 'sales_quote_details' )
        ->join( 'inv_products', 'sales_quote_details.product_id', '=', 'inv_products.id' )
        ->select(
            'inv_products.name',
            'sales_quote_details.price',
            'sales_quote_details.quantity',
            'sales_quote_details.vat',
            'sales_quote_details.discount',
            'sales_quote_details.amount'
        )
        ->where( 'sales_quote_details.quote_id', $id )
        ->where( 'sales_quote_details.status', '1' )
        ->get();

        $sub_amount = $quote_details->sum( 'amount' );
            
        $total_vat = $quote_details->sum( 'vat' );

        $customer_id = $order->customer_id;
        $quote_number = $order->quote_number;
        $discount = $quote_details->sum('discount') ?? 0;

        $vat = Setting::where( 'id', 120 )->value( 'value' ) / 100;
        $vat_rate = $vat;
        //Get VAT %
        $enable_discount = Setting::where( 'id', 111 )->value( 'value' );

        /*get default Price Category*/
        $default_sale_type = Setting::where( 'id', 125 )->value( 'value' );
        $sale_type = PriceCategory::where( 'name', $default_sale_type )->first();

        if ( $sale_type != null ) {
            $default_sale_type = $sale_type->id;
        } else {
            $default_sale_type = PriceCategory::first()->value( 'id' );
        }

        $total = $sub_amount - $discount;
        $sub_total = $sub_amount - $total_vat;

        $price_category = PriceCategory::all();
        $sale_quotes = SalesQuote::where( 'id', $id )->orderBy( 'id', 'DESC' )->get();
        $customers = Customer::all();
        $current_stock = CurrentStock::all();
        $count = $sale_quotes->count();
        return View::make( 'sales.sale_quotes.edit' )
        ->with( compact( 'customer_id' ) )
        ->with( compact( 'quote_number' ) )
        ->with( compact( 'quote_id' ) )
        ->with( compact( 'total_vat' ) )
        ->with( compact( 'vat_rate' ) )
        ->with( compact( 'total' ) )
        ->with( compact( 'sub_total' ) )
        ->with( compact( 'discount' ) )
        ->with( compact( 'count' ) )
        ->with( compact( 'sale_quotes' ) )
        ->with( compact( 'customers' ) )
        ->with( compact( 'price_category' ) )
        ->with( compact( 'default_sale_type' ) )
        ->with( compact( 'current_stock' ) )
        ->with( compact( [ 'enable_discount', 'sales_details' ] ) );
    }

    public function fetchUpdates( $id ) {
        // 1. Retrieve Item Details
        $sales_details = DB::table( 'sales_quote_details' )
        ->join( 'inv_products', 'sales_quote_details.product_id', '=', 'inv_products.id' )
        ->select(
            'sales_quote_details.id',
            'sales_quote_details.quote_id',
            'sales_quote_details.product_id',
            'inv_products.name',
            'inv_products.brand',
            'inv_products.pack_size',
            'inv_products.sales_uom',
            'sales_quote_details.price',
            'sales_quote_details.quantity',
            'sales_quote_details.vat',
            'sales_quote_details.discount',
            'sales_quote_details.amount'
        )
        ->where( 'sales_quote_details.quote_id', '=', $id )
        ->where( 'sales_quote_details.status', 1 )
        ->get();

        $quote_id = $id;
        $discount = $sales_details->sum('discount') ?? 0;

        $order = SalesQuote::where( 'id', $id )->first();

        $sub_total = $sales_details->sum( 'amount' );

        $total_vat = $sales_details->sum( 'vat' );

        $customer_id = $order->customer_id ?? null;
        $quote_number = $order->quote_number ?? null;

        $vatRate = Setting::where( 'id', 120 )->value( 'value' ) / 100;
        $enable_discount = Setting::where( 'id', 111 )->value( 'value' );

        /* get default Price Category */
        $default_sale_type = Setting::where( 'id', 125 )->value( 'value' );
        $sale_type = PriceCategory::where( 'name', $default_sale_type )->first();

        if ( $sale_type != null ) {
            $default_sale_type = $sale_type->id;
        } else {
            $default_sale_type = PriceCategory::first()->value( 'id' );
        }

        $total = ($sub_total + $total_vat)-$discount;

        $price_category = PriceCategory::all();
        $sale_quotes = SalesQuote::where( 'id', $id )->orderBy( 'id', 'DESC' )->get();
        // $customers = Customer::where( 'id', $sale_quotes[ 0 ]->customer_id ?? 0 )->orderBy( 'name', 'ASC' )->get();
        $customers = Customer::all();
        $current_stock = CurrentStock::all();
        $count = $sale_quotes->count();

        // Return JSON instead of view
        return response()->json( [
            'customer_id' => $customer_id,
            'quote_number' => $quote_number,
            'quote_id' => $quote_id,
            'vat' => $total_vat,
            'vat_rate'=> $vatRate,
            'total' => $total,
            'discount'=> $discount,
            'sub_total' => $sub_total,
            'count' => $count,
            'sale_quotes' => $sale_quotes,
            'customers' => $customers,
            'price_category' => $price_category,
            'default_sale_type' => $default_sale_type,
            'current_stock' => $current_stock,
            'enable_discount' => $enable_discount,
            'sales_details' => $sales_details,
        ] );
    }

    public function destroy( Request $request ) {
        dd( $request->all() );
    }

    public function getQuoteReceipt() {

        $page = -22;
        $receipt_size = Setting::where( 'id', 119 )->value( 'value' );
        $pharmacy[ 'name' ] = Setting::where( 'id', 100 )->value( 'value' );
        $pharmacy[ 'logo' ] = Setting::where( 'id', 105 )->value( 'value' );
        $pharmacy[ 'address' ] = Setting::where( 'id', 106 )->value( 'value' );
        $pharmacy[ 'tin_number' ] = Setting::where( 'id', 102 )->value( 'value' );
        $pharmacy[ 'phone' ] = Setting::where( 'id', 107 )->value( 'value' );
        $pharmacy[ 'slogan' ] = Setting::where( 'id', 104 )->value( 'value' );
        $pharmacy[ 'vrn_number' ] = Setting::where( 'id', 103 )->value( 'value' );

        $id = SalesQuoteDetail::orderBy( 'id', 'desc' )->value( 'quote_id' );

        $sale_quote = SalesQuoteDetail::where( 'quote_id', $id )->get();

        $sales = array();
        $grouped_sales = array();
        $sn = 0;
        foreach ( $sale_quote as $item ) {
            //            $receipt_no = $item->sale[ 'receipt_number' ];
            $amount = $item->amount - $item->discount;
            if ( intVal( $item->vat ) === 0 ) {
                $vat_percent = 0;
            } else {
                $vat_percent = $item->vat / $item->price;
            }
            $sub_total = $item->price * $item->quantity;
            $vat = $amount - $sub_total;
            $sn++;

            array_push( $sales, array(
                'receipt_number' => $item->quote[ 'quote_number' ],
                'name' => $item->product[ 'name' ],
                'sn' => $sn,
                'quantity' => $item->quantity,
                'vat' => $vat,
                'discount' => $item->discount,
                'discount_total' => $item->quote[ 'cost' ][ 'discount' ],
                'price' => $item->price,
                'amount' => $amount,
                'sub_total' => $sub_total,
                'grand_total' => ( $item->quote[ 'amount' ] - $item->quote[ 'discount' ] ),
                'total_vat' => ( $item->quote[ 'cost' ][ 'vat' ] ),
                'sold_by' => $item->quote[ 'user' ][ 'name' ],
                'customer' => $item->quote[ 'customer' ][ 'name' ],
                'customer_tin' => $item->quote->customer->tin,
                'created_at' => date( 'Y-m-d', strtotime( $item->quote[ 'date' ] ) )
            ) );
        }

        foreach ( $sales as $val ) {
            if ( array_key_exists( 'receipt_number', $val ) ) {
                $grouped_sales[ $val[ 'receipt_number' ] ][] = $val;
            }
        }

        $data = $grouped_sales;

        if ( $receipt_size === '58mm Thermal Paper' ) {
            $pdf = PDF::loadView( 'sales.cash_sales.order_receipt_thermal',
            compact( 'data', 'pharmacy', 'page' ) );
        } else if ( $receipt_size === 'A4 / Letter' ) {
            $pdf = PDF::loadView( 'sales.sale_quotes.quote_receipt',
            compact( 'data', 'pharmacy', 'page' ) );
        } else if ( $receipt_size === '80mm Thermal Paper' ) {
            $pdf = PDF::loadView( 'sales.cash_sales.order_receipt_thermal',
            compact( 'data', 'pharmacy', 'page' ) );
        } else if ( $receipt_size === 'A5 / Half Letter' ) {
            $pdf = PDF::loadView( 'sales.sale_quotes.quote_receipt',
            compact( 'data', 'pharmacy', 'page' ) );
        }

        return $pdf->stream( $id . '.pdf' );
    }

    public function receiptReprint( $id ) {
        try {
            $page = -22;
            $receipt_size = Setting::where( 'id', 119 )->value( 'value' );
            $pharmacy[ 'name' ] = Setting::where( 'id', 100 )->value( 'value' );
            $pharmacy[ 'logo' ] = Setting::where( 'id', 105 )->value( 'value' );
            $pharmacy[ 'address' ] = Setting::where( 'id', 106 )->value( 'value' );
            $pharmacy[ 'tin_number' ] = Setting::where( 'id', 102 )->value( 'value' );
            $pharmacy[ 'phone' ] = Setting::where( 'id', 107 )->value( 'value' );
            $pharmacy[ 'slogan' ] = Setting::where( 'id', 104 )->value( 'value' );
            $pharmacy[ 'vrn_number' ] = Setting::where( 'id', 103 )->value( 'value' );

            $sale_quote = SalesQuoteDetail::where( 'quote_id', $id )->get();

            $sales = array();
            $grouped_sales = array();
            $sn = 0;
            foreach ( $sale_quote as $item ) {
                //            $receipt_no = $item->sale[ 'receipt_number' ];
                $amount = $item->amount - $item->discount;
                if ( intVal( $item->vat ) === 0 ) {
                    $vat_percent = 0;
                } else {
                    $vat_percent = $item->vat / $item->price;
                }
                $sub_total = $item->amount  - $item->vat;
                $vat = $item->amount - $sub_total;
                $sn++;
                array_push( $sales, array(
                    'receipt_number' => $item->quote[ 'quote_number' ],
                    'name' => $item->product[ 'name' ],
                    'brand' => $item->product[ 'brand' ],
                    'pack_size' => $item->product[ 'pack_size' ],
                    'sales_uom' => $item->product[ 'sales_uom' ],
                    'sn' => $sn,
                    'quantity' => $item->quantity,
                    'vat' => $vat,
                    'discount' => $item->discount,
                    'discount_total' => $item->quote[ 'cost' ][ 'discount' ],
                    'price' => $item->price,
                    'amount' => $item->amount,
                    'sub_total' => $sub_total,
                    'grand_total' => ( $item->quote[ 'cost' ][ 'amount' ] ) - ( $item->quote[ 'cost' ][ 'discount' ] ),
                    'total_vat' => ( $item->quote[ 'cost' ][ 'vat' ] ),
                    'sold_by' => $item->quote[ 'user' ][ 'name' ],
                    'customer' => $item->quote[ 'customer' ][ 'name' ],
                    'customer_tin' => $item->sale[ 'customer' ][ 'tin' ] ?? '',
                    'created_at' => date( 'Y-m-d', strtotime( $item->quote[ 'date' ] ) )
                ) );
            }

            foreach ( $sales as $val ) {
                if ( array_key_exists( 'receipt_number', $val ) ) {
                    $grouped_sales[ $val[ 'receipt_number' ] ][] = $val;
                }
            }

            $data = $grouped_sales;

            if ( $receipt_size === '58mm Thermal Paper' ) {
                $pdf = PDF::loadView( 'sales.sale_quotes.quote_receipt_thermal_58',
                compact( 'data', 'pharmacy', 'page' ) )
                    ->setPaper([0, 0, 163, 600], '');
            } else if ( $receipt_size === '80mm Thermal Paper' ) {
                $pdf = PDF::loadView( 'sales.sale_quotes.quote_receipt_thermal_80',
                compact( 'data', 'pharmacy', 'page' ) )
                    ->setPaper([0, 0, 227, 600], '');
            } else if ( $receipt_size === 'A4 / Letter' ) {
                $pdf = PDF::loadView( 'sales.sale_quotes.quote_receipt_A4',
                compact( 'data', 'pharmacy', 'page' ) )
                    ->setPaper('a4', '');
            } else if ( $receipt_size === 'A5 / Half Letter' )  {
                $pdf = PDF::loadView( 'sales.sale_quotes.quote_receipt_A5',
                compact( 'data', 'pharmacy', 'page' ) )
                    ->setPaper('a5', '');
            }
            return $pdf->stream( $id . '.pdf' );
        } catch ( Exception $e ) {
            Log::info( 'Error', [ 'PrintingError'=>$e ] );
        }

    }

    //Convert sales order to sales
    public function convertToSales( Request $request ) {
        $quoteId = $request->quote_id;
        $saleType = $request->sale_type;
        $gracePeriod = $request->grace_period;
        $remarks = $request->notes;

        $default_store = current_store_id();
        DB::beginTransaction();
        try {
            $vat = Setting::where( 'id', 120 )->value( 'value' ) / 100;
            //Get VAT %

            //1. Retrieve Item Details
            $quote_details = DB::table( 'sales_quote_details' )
            ->join( 'inv_products', 'sales_quote_details.product_id', '=', 'inv_products.id' )
            ->join( 'sales_quotes', 'sales_quote_details.quote_id', '=', 'sales_quotes.id' ) // ← ongeza hii
            ->select(
                'sales_quote_details.id',
                'sales_quote_details.quote_id',
                'sales_quote_details.product_id',
                'inv_products.name',
                'sales_quote_details.price',
                'sales_quote_details.quantity',
                'sales_quote_details.vat',
                'sales_quote_details.discount',
                'sales_quote_details.amount',
                'sales_quotes.quote_number',
                'sales_quotes.customer_id',
                'sales_quotes.price_category_id'
            )
            ->where( 'sales_quote_details.quote_id', $quoteId )
            ->where( 'sales_quote_details.status', '1' )
            ->where( 'sales_quotes.store_id', $default_store )
            ->get();

            if ( $quote_details->isEmpty() ) {
                // return [ 'status' => 'error', 'message' => 'Order not found or already converted' ];
                throw new \Exception( 'Order not found or already converted' );
            }

            $total_amount = $quote_details->sum( 'amount' );
            $total_discount = $quote_details->sum('discount');

            //Saving Sale Summary and Get its ID
            $sale = DB::table( 'sales' )->insertGetId( array(
                'receipt_number' => $quote_details[ 0 ]->quote_number,
                'customer_id' => $quote_details[ 0 ]->customer_id,
                'price_category_id' => $quote_details[ 0 ]->price_category_id,
                'date' => now(),
                'created_by' => Auth::User()->id
            ) );

            foreach ( $quote_details as $item ) {

                $total_stock = CurrentStock::where( 'product_id', $item->product_id )
                ->where( 'store_id', $default_store )
                ->sum( 'quantity' );

                if ( $item->quantity > $total_stock ) {
                    throw new \Exception( "Insufficient stock for product {$item->name}" );
                }

                $stocks = CurrentStock::with( 'product' )->where( 'product_id', $item->product_id )
                ->where( 'store_id', $default_store )
                ->where( 'quantity', '>', 0 )
                ->get();

                foreach ( $stocks as $stock ) {
                    if ( $item->quantity <= $stock->quantity ) {
                        $qty = $item->quantity;
                        $price = ( float )$item->price;
                        $sale_discount = ( float )$item->discount;
                        $stock->quantity -= $qty;
                        $stock->created_by = Auth::User()->id;
                        $item->quantity -= $qty;
                    } else {
                        $qty = $stock->quantity;
                        $price = ( float )$item->price;
                        $sale_discount = ( float )$item->discount;
                        $stock->quantity = 0;
                        $stock->created_by = Auth::User()->id;
                        $item->quantity -= $qty;
                    }
                    if ( $qty > 0 ) {
                        $details = new SalesDetail;
                        $details->sale_id = $sale;
                        $details->stock_id = $stock->id;
                        $details->quantity = $qty;
                        $details->price = $price;
                        $details->vat = $details->price * $vat;
                        $details->amount = ($details->price*$details->quantity) + $details->vat;
                        $details->discount = $sale_discount;
                        $details->save();
                        $stock->save();

                        $stock_tracking = new StockTracking;
                        $stock_tracking->stock_id = $stock->id;
                        $stock_tracking->product_id = $item->product_id;
                        $stock_tracking->quantity = $qty;
                        $stock_tracking->store_id = $default_store;
                        $stock_tracking->created_by = Auth::user()->id;
                        $stock_tracking->updated_by = Auth::user()->id;
                        $stock_tracking->out_mode = $saleType === 'cash' ? 'Order sold in cash' : 'Order sold in credit';
                        $stock_tracking->updated_at = date( 'Y-m-d' );
                        $stock_tracking->movement = 'OUT';
                        $stock_tracking->save();

                    }
                    if ( $item->quantity <= 0 ) {
                        break;
                    }
                }
                //credit Sale
                if ( $saleType !== 'cash' ) {

                    $credit = new SalesCredit;
                    $customer = Customer::find( $quote_details[ 0 ]->customer_id );
                    $credit->sale_id = $sale;
                    $credit->paid_amount = 0;
                    $credit->balance = $total_amount-$total_discount;
                    $credit->grace_period = $gracePeriod;
                    $credit->remark = $remarks;
                    $credit->created_by = Auth::User()->id;
                    $credit->updated_by = Auth::User()->id;
                    $customer->total_credit += $credit->balance;
                    $credit->save();
                    $customer->save();
                }
            }

            $updatedQuote = DB::table( 'sales_quotes' )
            ->where( 'id', $quoteId )
            ->update( [
                'remark' => $remarks,
            ] );

            $update = DB::table( 'sales_quote_details' )
            ->where( 'quote_id', $quoteId )
            ->update( [
                'status' => '2',
                'updated_by' => Auth::user()->id
            ] );

            if ( !$update ) {
                throw new \Exception( 'Failed to update sales quote details' );
            }

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Order converted to sales successfully',
                'sale_id' => $sale
            ];
        } catch ( \Exception $e ) {
            DB::rollBack();
            Log::error( 'ConvertToSales Error: ' . $e->getMessage(), [ 'trace' => $e->getTraceAsString() ] );
            return [ 'status' => 'error', 'message' => 'An error occured', 'error' => $e->getMessage() ];
        }
    }

    public function generateTaxInvoice( $id ) {
        try {
            $page = -22;
            $receipt_size = Setting::where( 'id', 119 )->value( 'value' );
            $pharmacy[ 'name' ] = Setting::where( 'id', 100 )->value( 'value' );
            $pharmacy[ 'logo' ] = Setting::where( 'id', 105 )->value( 'value' );
            $pharmacy[ 'address' ] = Setting::where( 'id', 106 )->value( 'value' );
            $pharmacy[ 'tin_number' ] = Setting::where( 'id', 102 )->value( 'value' );
            $pharmacy[ 'phone' ] = Setting::where( 'id', 107 )->value( 'value' );
            $pharmacy[ 'slogan' ] = Setting::where( 'id', 104 )->value( 'value' );
            $pharmacy[ 'vrn_number' ] = Setting::where( 'id', 103 )->value( 'value' );

            $sale_quote = SalesQuoteDetail::where( 'quote_id', $id )->get();

            $sales = array();
            $grouped_sales = array();
            $sn = 0;
            foreach ( $sale_quote as $item ) {
                //            $receipt_no = $item->sale[ 'receipt_number' ];
                $amount = $item->amount - $item->discount;
                if ( intVal( $item->vat ) === 0 ) {
                    $vat_percent = 0;
                } else {
                    $vat_percent = $item->vat / $item->price;
                }
                $sub_total = ( $amount / ( 1 + $vat_percent ) );
                $vat = $amount - $sub_total;
                $sn++;
                array_push( $sales, array(
                    'receipt_number' => $item->quote[ 'quote_number' ],
                    'name' => $item->product[ 'name' ],
                    'sn' => $sn,
                    'quantity' => $item->quantity,
                    'vat' => $vat,
                    'discount' => $item->discount,
                    'discount_total' => $item->quote[ 'cost' ][ 'discount' ],
                    'price' => $item->price,
                    'amount' => $amount,
                    'sub_total' => $sub_total,
                    'grand_total' => ( $item->quote[ 'cost' ][ 'amount' ] ) - ( $item->quote[ 'cost' ][ 'discount' ] ),
                    'total_vat' => ( $item->quote[ 'cost' ][ 'vat' ] ),
                    'sold_by' => $item->quote[ 'user' ][ 'name' ],
                    'customer' => $item->quote[ 'customer' ][ 'name' ],
                    'customer_tin' => $item->quote->customer->tin,
                    'created_at' => date( 'Y-m-d', strtotime( $item->quote[ 'date' ] ) )
                ) );
            }

            foreach ( $sales as $val ) {
                if ( array_key_exists( 'receipt_number', $val ) ) {
                    $grouped_sales[ $val[ 'receipt_number' ] ][] = $val;
                }
            }

            $data = $grouped_sales;

            $pdf = PDF::loadView( 'sales.sale_quotes.tax_invoice',
            compact( 'data', 'pharmacy', 'page' ) );

            return $pdf->stream( 'TAX-INVOICE-' . $id . '.pdf' );

        } catch ( Exception $e ) {
            Log::error( $e->getMessage() );
        }
    }

    public function generateDeliveryNote( $id ) {
        try {
            $page = -22;
            $receipt_size = Setting::where( 'id', 119 )->value( 'value' );
            $pharmacy[ 'name' ] = Setting::where( 'id', 100 )->value( 'value' );
            $pharmacy[ 'logo' ] = Setting::where( 'id', 105 )->value( 'value' );
            $pharmacy[ 'address' ] = Setting::where( 'id', 106 )->value( 'value' );
            $pharmacy[ 'tin_number' ] = Setting::where( 'id', 102 )->value( 'value' );
            $pharmacy[ 'phone' ] = Setting::where( 'id', 107 )->value( 'value' );
            $pharmacy[ 'slogan' ] = Setting::where( 'id', 104 )->value( 'value' );
            $pharmacy[ 'vrn_number' ] = Setting::where( 'id', 103 )->value( 'value' );

            $sale_quote = SalesQuoteDetail::where( 'quote_id', $id )->get();

            $sales = array();
            $grouped_sales = array();
            $sn = 0;
            foreach ( $sale_quote as $item ) {
                //            $receipt_no = $item->sale[ 'receipt_number' ];
                $amount = $item->amount - $item->discount;
                if ( intVal( $item->vat ) === 0 ) {
                    $vat_percent = 0;
                } else {
                    $vat_percent = $item->vat / $item->price;
                }
                $sub_total = ( $amount / ( 1 + $vat_percent ) );
                $vat = $amount - $sub_total;
                $sn++;
                array_push( $sales, array(
                    'receipt_number' => $item->quote[ 'quote_number' ],
                    'name' => $item->product[ 'name' ],
                    'sn' => $sn,
                    'quantity' => $item->quantity,
                    'vat' => $vat,
                    'discount' => $item->discount,
                    'discount_total' => $item->quote[ 'cost' ][ 'discount' ],
                    'price' => $item->price,
                    'amount' => $amount,
                    'sub_total' => $sub_total,
                    'grand_total' => ( $item->quote[ 'cost' ][ 'amount' ] ) - ( $item->quote[ 'cost' ][ 'discount' ] ),
                    'total_vat' => ( $item->quote[ 'cost' ][ 'vat' ] ),
                    'sold_by' => $item->quote[ 'user' ][ 'name' ],
                    'customer' => $item->quote[ 'customer' ][ 'name' ],
                    'customer_tin' => $item->quote->customer->tin,
                    'created_at' => date( 'Y-m-d', strtotime( $item->quote[ 'date' ] ) )
                ) );
            }

            foreach ( $sales as $val ) {
                if ( array_key_exists( 'receipt_number', $val ) ) {
                    $grouped_sales[ $val[ 'receipt_number' ] ][] = $val;
                }
            }

            $data = $grouped_sales;

            $pdf = PDF::loadView( 'sales.sale_quotes.delivery_note',
            compact( 'data', 'pharmacy', 'page' ) );

            return $pdf->stream( 'DELIVERY-NOTE-' . $id . '.pdf' );

        } catch ( Exception $e ) {
            Log::error( $e->getMessage() );
        }
    }
}
