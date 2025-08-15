<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\Customer;
use App\PriceCategory;
use App\Sale;
use App\SalesQuote;
use App\SalesQuoteDetail;
use App\Setting;
use Dompdf\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use PDF;
use Illuminate\Support\Facades\View;

class SaleQuoteController extends Controller
{

    public function index()
    {
        $store_id = Auth::user()->store_id;
        $vat = Setting::where('id', 120)->value('value') / 100;//Get VAT %
        $enable_discount = Setting::where('id', 111)->value('value');

        /*get default Price Category*/
        $default_sale_type = Setting::where('id', 125)->value('value');
        $sale_type = PriceCategory::where('name', $default_sale_type)->first();

        if ($sale_type != null) {
            $default_sale_type = $sale_type->id;
        } else {
            $default_sale_type = PriceCategory::first()->value('id');
        }


        $price_category = PriceCategory::all();
        $sale_quotes = SalesQuote::orderBy('id', 'DESC')
            ->where('store_id','=',$store_id)
            ->get();

        $customers = Customer::orderBy('name', 'ASC')->get();
        $current_stock = CurrentStock::all();
        $count = $sale_quotes->count();
        return View::make('sales.sale_quotes.index')
            ->with(compact('vat'))
            ->with(compact('count'))
            ->with(compact('sale_quotes'))
            ->with(compact('customers'))
            ->with(compact('price_category'))
            ->with(compact('default_sale_type'))
            ->with(compact('current_stock'))
            ->with(compact('enable_discount'));
    }

    public function orderList()
    {
        $store_id = Auth::user()->store_id;
        $vat = Setting::where('id', 120)->value('value') / 100;//Get VAT %
        $enable_discount = Setting::where('id', 111)->value('value');

        /*get default Price Category*/
        $default_sale_type = Setting::where('id', 125)->value('value');
        $sale_type = PriceCategory::where('name', $default_sale_type)->first();

        if ($sale_type != null) {
            $default_sale_type = $sale_type->id;
        } else {
            $default_sale_type = PriceCategory::first()->value('id');
        }


        $price_category = PriceCategory::all();
        $sale_quotes = SalesQuote::orderBy('id', 'DESC')
            ->where('store_id','=',$store_id)
            ->get();
        $customers = Customer::orderBy('name', 'ASC')->get();
        $current_stock = CurrentStock::all();
        $count = $sale_quotes->count();
        return View::make('sales.sale_quotes.index_quotes')
            ->with(compact('vat'))
            ->with(compact('count'))
            ->with(compact('sale_quotes'))
            ->with(compact('customers'))
            ->with(compact('price_category'))
            ->with(compact('default_sale_type'))
            ->with(compact('current_stock'))
            ->with(compact('enable_discount'));
    }

    public function getQuotes(Request $request)
    {
        $store_id = Auth::user()->store_id;
        $date_range = explode('-', $request->date);
        $from = date('Y-m-d', strtotime(trim($date_range[0])));
        $to = date('Y-m-d', strtotime(trim($date_range[1])));
        $sale_quotes = SalesQuote::with(['cost','customer','details'])->orderBy('id', 'DESC')
            ->where('store_id','=',$store_id)
            ->whereBetween(DB::raw("DATE(`date`)"), [$from, $to])->get();
        return response()->json($sale_quotes, 200);
    }

    public function storeQuote(Request $request)
    {
        if ($request->ajax()) {
            $this->store($request);
            return response()->json([
                'redirect_to' => route('getQuoteReceipt')
            ]);
        }
    }

    //Edit Sales Order
    public function updateQuote(Request $request)
    {
        $quantity = $request->quantity;
        $amount = $request->quantity * $request->price;
        $id = $request->id;

        $updateOrder = DB::table('sales_quote_details')
        ->where('id',$id)
        ->update([
            'quantity'=>$quantity,
            'amount'=>$amount
        ]);

        if($updateOrder)
        {
            Session::flash("alert-success", "Order updated successfully!");
            return redirect()->back();
        }


        Session::flash("alert-danger", "Oop something went wrong!");

        return redirect()->back();

    }

    //Convert Sales Order to Sale ( Cash or Credit )
    public function convertToSale($id)
    {

    }


    //Store sales order data
    public function store(Request $request)
    {
        $store_id = Auth::user()->store_id;
        date_default_timezone_set('Africa/Nairobi');
//some attributes declaration
        $cart = json_decode($request->cart, true);

        $vat = Setting::where('id', 120)->value('value') / 100;//Get VAT %
        $quote_number = strtoupper(substr(md5(microtime()), rand(0, 26), 8));

        $discount = $request->discount_amount;
        $date = date('Y-m-d,H:i:s');
        $total = 0;
//Avoid submission of a null Cart
        if (!$cart) {
            session()->flash("alert-danger", "You can not save an empty Cart!");
        } else {
//calculating the Total Amount
            foreach ($cart as $bought) {
                $total += $bought['amount'];
            }
//Saving Sale-Quote Summary and Get its ID
            $quote = DB::table('sales_quotes')->insertGetId(array(
                'remark' => $request->remark,
                'quote_number' => $quote_number,
                'customer_id' => $request->customer_id,
                'price_category_id' => $request->price_category_id,
                'date' => $date,
                'created_by' => Auth::User()->id,
                'store_id'=>$store_id
            ));

//Saving Quote Details
            foreach ($cart as $bought) {
                $bought['quantity'] = str_replace(',', '', $bought['quantity']);
                $discount = (($bought['amount'] / $total) * $discount);
                $price = $bought['price'] * $bought['quantity'];
                $details = new SalesQuoteDetail;
                $details->quote_id = $quote;
                $details->product_id = $bought['product_id'];
                $details->quantity = $bought['quantity'];
                $details->price = $price;
                $details->vat = $details->price * $vat;
                $details->amount = $details->price + $details->vat;
                $details->discount = $discount;
                $details->save();
            }

//            session()->flash("alert-success", "Sale Quote recorded successfully!");
        }
//        return back();

    }

    //Retrieves update sales details
    public function update($id)
    {

        //1. Retrieve Item Details
        $sales_details = DB::table('sales_quote_details')
            ->join('inv_products','sales_quote_details.product_id','=','inv_products.id')
            ->select('sales_quote_details.id','sales_quote_details.quote_id','sales_quote_details.product_id','inv_products.name','sales_quote_details.price','sales_quote_details.quantity','sales_quote_details.vat','sales_quote_details.discount','sales_quote_details.amount')
            ->where('sales_quote_details.quote_id',$id)
            ->where('sales_quote_details.status','1')
            ->get();

        $quote_id = $id;


        $order = SalesQuote::where('id',$id)->first();

        $sub_total = DB::table('sales_quote_details')
            ->join('inv_products','sales_quote_details.product_id','=','inv_products.id')
            ->select('inv_products.name','sales_quote_details.price','sales_quote_details.quantity','sales_quote_details.vat','sales_quote_details.discount','sales_quote_details.amount')
            ->where('sales_quote_details.quote_id',$id)
            ->where('sales_quote_details.status','1')
            ->sum('amount');

        $customer_id = $order->customer_id;
        $quote_number = $order->quote_number;


        $vat = Setting::where('id', 120)->value('value') / 100;//Get VAT %
        $enable_discount = Setting::where('id', 111)->value('value');

        /*get default Price Category*/
        $default_sale_type = Setting::where('id', 125)->value('value');
        $sale_type = PriceCategory::where('name', $default_sale_type)->first();

        if ($sale_type != null) {
            $default_sale_type = $sale_type->id;
        } else {
            $default_sale_type = PriceCategory::first()->value('id');
        }

        $total = $sub_total + $vat;


        $price_category = PriceCategory::all();
        $sale_quotes = SalesQuote::where('id',$id)->orderBy('id', 'DESC')->get();
        $customers = Customer::where('id',$sale_quotes[0]->customer_id)->orderBy('name', 'ASC')->get();
        $current_stock = CurrentStock::all();
        $count = $sale_quotes->count();
        return View::make('sales.sale_quotes.edit')
            ->with(compact('customer_id'))
            ->with(compact('quote_number'))
            ->with(compact('quote_id'))
            ->with(compact('vat'))
            ->with(compact('total'))
            ->with(compact('sub_total'))
            ->with(compact('count'))
            ->with(compact('sale_quotes'))
            ->with(compact('customers'))
            ->with(compact('price_category'))
            ->with(compact('default_sale_type'))
            ->with(compact('current_stock'))
            ->with(compact(['enable_discount','sales_details']));
    }

    public function destroy(Request $request)
    {
        dd($request->all());
    }

    public function getQuoteReceipt()
    {

        $page = -22;
        $receipt_size = Setting::where('id', 119)->value('value');
        $pharmacy['name'] = Setting::where('id', 100)->value('value');
        $pharmacy['logo'] = Setting::where('id', 105)->value('value');
        $pharmacy['address'] = Setting::where('id', 106)->value('value');
        $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');
        $pharmacy['phone'] = Setting::where('id', 107)->value('value');
        $pharmacy['slogan'] = Setting::where('id', 104)->value('value');
        $pharmacy['vrn_number'] = Setting::where('id', 103)->value('value');


        $id = SalesQuoteDetail::orderBy('id', 'desc')->value('quote_id');

        $sale_quote = SalesQuoteDetail::where('quote_id', $id)->get();

        $sales = array();
        $grouped_sales = array();
        $sn = 0;
        foreach ($sale_quote as $item) {
//            $receipt_no = $item->sale['receipt_number'];
            $amount = $item->amount - $item->discount;
            if (intVal($item->vat) === 0) {
                $vat_percent = 0;
            } else {
                $vat_percent = $item->vat / $item->price;
            }
            $sub_total = ($amount / (1 + $vat_percent));
            $vat = $amount - $sub_total;
            $sn++;

            array_push($sales, array(
                'receipt_number' => $item->quote['quote_number'],
                'name' => $item->product['name'],
                'sn' => $sn,
                'quantity' => $item->quantity,
                'vat' => $vat,
                'discount' => $item->discount,
                'discount_total' => $item->quote['cost']['discount'],
                'price' => $item->price,
                'amount' => $amount,
                'sub_total' => $sub_total,
                'grand_total' => ($item->quote['cost']['amount']) - ($item->quote['cost']['discount']),
                'total_vat' => ($item->quote['cost']['vat']),
                'sold_by' => $item->quote['user']['name'],
                'customer' => $item->quote['customer']['name'],
                'customer_tin' => $item->quote->customer->tin,
                'created_at' => date('Y-m-d', strtotime($item->quote['date']))
            ));
        }


        foreach ($sales as $val) {
            if (array_key_exists('receipt_number', $val)) {
                $grouped_sales[$val['receipt_number']][] = $val;
            }
        }

        $data = $grouped_sales;

        if ($receipt_size === '58mm Thermal Paper') {
            $pdf = PDF::loadView('sales.cash_sales.order_receipt_thermal',
                compact('data', 'pharmacy', 'page'));
        } else if($receipt_size === 'A4 / Letter') {
            $pdf = PDF::loadView('sales.sale_quotes.quote_receipt',
                compact('data', 'pharmacy', 'page'));
        } else if ($receipt_size === '80mm Thermal Paper') {
            $pdf = PDF::loadView('sales.cash_sales.order_receipt_thermal',
                compact('data', 'pharmacy', 'page'));
        }   else if($receipt_size === 'A5 / Half Letter'){
            $pdf = PDF::loadView('sales.sale_quotes.quote_receipt',
                compact('data', 'pharmacy', 'page'));
        }

        return $pdf->stream($id . '.pdf');
    }

    public function receiptReprint($id)
    {
        try {
            $page = -22;
            $receipt_size = Setting::where('id', 119)->value('value');
            $pharmacy['name'] = Setting::where('id', 100)->value('value');
            $pharmacy['logo'] = Setting::where('id', 105)->value('value');
            $pharmacy['address'] = Setting::where('id', 106)->value('value');
            $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');
            $pharmacy['phone'] = Setting::where('id', 107)->value('value');
            $pharmacy['slogan'] = Setting::where('id', 104)->value('value');
            $pharmacy['vrn_number'] = Setting::where('id', 103)->value('value');

            $sale_quote = SalesQuoteDetail::where('quote_id', $id)->get();

            $sales = array();
            $grouped_sales = array();
            $sn = 0;
            foreach ($sale_quote as $item) {
//            $receipt_no = $item->sale['receipt_number'];
                $amount = $item->amount - $item->discount;
                if (intVal($item->vat) === 0) {
                    $vat_percent = 0;
                } else {
                    $vat_percent = $item->vat / $item->price;
                }
                $sub_total = ($amount / (1 + $vat_percent));
                $vat = $amount - $sub_total;
                $sn++;
                array_push($sales, array(
                    'receipt_number' => $item->quote['quote_number'],
                    'name' => $item->product['name'],
                    'sn' => $sn,
                    'quantity' => $item->quantity,
                    'vat' => $vat,
                    'discount' => $item->discount,
                    'discount_total' => $item->quote['cost']['discount'],
                    'price' => $item->price,
                    'amount' => $amount,
                    'sub_total' => $sub_total,
                    'grand_total' => ($item->quote['cost']['amount']) - ($item->quote['cost']['discount']),
                    'total_vat' => ($item->quote['cost']['vat']),
                    'sold_by' => $item->quote['user']['name'],
                    'customer' => $item->quote['customer']['name'],
                    'customer_tin' => $item->sale['customer']['tin'],
                    'created_at' => date('Y-m-d', strtotime($item->quote['date']))
                ));
            }


            foreach ($sales as $val) {
                if (array_key_exists('receipt_number', $val)) {
                    $grouped_sales[$val['receipt_number']][] = $val;
                }
            }

            $data = $grouped_sales;

            if ($receipt_size === '58mm Thermal Paper') {
                $pdf = PDF::loadView('sales.cash_sales.receipt_thermal',
                    compact('data', 'pharmacy', 'page'));
            } else if ($receipt_size === '80mm Thermal Paper') {
                $pdf = PDF::loadView('sales.cash_sales.receipt_thermal',
                    compact('data', 'pharmacy', 'page'));
            } else if ($receipt_size === 'A4 / Letter') {
                $pdf = PDF::loadView('sales.sale_quotes.quote_receipt',
                    compact('data', 'pharmacy', 'page'));
            } else {
                $pdf = PDF::loadView('sales.sale_quotes.quote_receipt',
                    compact('data', 'pharmacy', 'page'));
            }
            return $pdf->stream($id . '.pdf');
        }catch (Exception $e)
        {
            Log::info('Error',['PrintingError'=>$e]);
        }

    }


    //Convert sales order to sales
    public function convertToSales(Request $request)
    {

        $update = DB::table('sales_quote_details')
            ->where('quote_id',$request->quote_id)
            ->update([
                'status'=>'2'
            ]);

        if($update)
        {
            $response = ['message'=>'success'];
        }

        if(!$update)
        {
            $response = ['message'=>'danger'];
        }


        return $response;
    }

    public function generateTaxInvoice($id)
    {
        try {
            $page = -22;
            $receipt_size = Setting::where('id', 119)->value('value');
            $pharmacy['name'] = Setting::where('id', 100)->value('value');
            $pharmacy['logo'] = Setting::where('id', 105)->value('value');
            $pharmacy['address'] = Setting::where('id', 106)->value('value');
            $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');
            $pharmacy['phone'] = Setting::where('id', 107)->value('value');
            $pharmacy['slogan'] = Setting::where('id', 104)->value('value');
            $pharmacy['vrn_number'] = Setting::where('id', 103)->value('value');

            $sale_quote = SalesQuoteDetail::where('quote_id', $id)->get();

            $sales = array();
            $grouped_sales = array();
            $sn = 0;
            foreach ($sale_quote as $item) {
//            $receipt_no = $item->sale['receipt_number'];
                $amount = $item->amount - $item->discount;
                if (intVal($item->vat) === 0) {
                    $vat_percent = 0;
                } else {
                    $vat_percent = $item->vat / $item->price;
                }
                $sub_total = ($amount / (1 + $vat_percent));
                $vat = $amount - $sub_total;
                $sn++;
                array_push($sales, array(
                    'receipt_number' => $item->quote['quote_number'],
                    'name' => $item->product['name'],
                    'sn' => $sn,
                    'quantity' => $item->quantity,
                    'vat' => $vat,
                    'discount' => $item->discount,
                    'discount_total' => $item->quote['cost']['discount'],
                    'price' => $item->price,
                    'amount' => $amount,
                    'sub_total' => $sub_total,
                    'grand_total' => ($item->quote['cost']['amount']) - ($item->quote['cost']['discount']),
                    'total_vat' => ($item->quote['cost']['vat']),
                    'sold_by' => $item->quote['user']['name'],
                    'customer' => $item->quote['customer']['name'],
                    'customer_tin' => $item->quote->customer->tin,
                    'created_at' => date('Y-m-d', strtotime($item->quote['date']))
                ));
            }


            foreach ($sales as $val) {
                if (array_key_exists('receipt_number', $val)) {
                    $grouped_sales[$val['receipt_number']][] = $val;
                }
            }

            $data = $grouped_sales;

            $pdf = PDF::loadView('sales.sale_quotes.tax_invoice',
                compact('data', 'pharmacy', 'page'));


            return $pdf->stream('TAX-INVOICE-' . $id . '.pdf');

        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function generateDeliveryNote($id)
    {
        try {
            $page = -22;
            $receipt_size = Setting::where('id', 119)->value('value');
            $pharmacy['name'] = Setting::where('id', 100)->value('value');
            $pharmacy['logo'] = Setting::where('id', 105)->value('value');
            $pharmacy['address'] = Setting::where('id', 106)->value('value');
            $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');
            $pharmacy['phone'] = Setting::where('id', 107)->value('value');
            $pharmacy['slogan'] = Setting::where('id', 104)->value('value');
            $pharmacy['vrn_number'] = Setting::where('id', 103)->value('value');

            $sale_quote = SalesQuoteDetail::where('quote_id', $id)->get();

            $sales = array();
            $grouped_sales = array();
            $sn = 0;
            foreach ($sale_quote as $item) {
//            $receipt_no = $item->sale['receipt_number'];
                $amount = $item->amount - $item->discount;
                if (intVal($item->vat) === 0) {
                    $vat_percent = 0;
                } else {
                    $vat_percent = $item->vat / $item->price;
                }
                $sub_total = ($amount / (1 + $vat_percent));
                $vat = $amount - $sub_total;
                $sn++;
                array_push($sales, array(
                    'receipt_number' => $item->quote['quote_number'],
                    'name' => $item->product['name'],
                    'sn' => $sn,
                    'quantity' => $item->quantity,
                    'vat' => $vat,
                    'discount' => $item->discount,
                    'discount_total' => $item->quote['cost']['discount'],
                    'price' => $item->price,
                    'amount' => $amount,
                    'sub_total' => $sub_total,
                    'grand_total' => ($item->quote['cost']['amount']) - ($item->quote['cost']['discount']),
                    'total_vat' => ($item->quote['cost']['vat']),
                    'sold_by' => $item->quote['user']['name'],
                    'customer' => $item->quote['customer']['name'],
                    'customer_tin' => $item->quote->customer->tin,
                    'created_at' => date('Y-m-d', strtotime($item->quote['date']))
                ));
            }


            foreach ($sales as $val) {
                if (array_key_exists('receipt_number', $val)) {
                    $grouped_sales[$val['receipt_number']][] = $val;
                }
            }

            $data = $grouped_sales;

            $pdf = PDF::loadView('sales.sale_quotes.delivery_note',
                compact('data', 'pharmacy', 'page'));


            return $pdf->stream('DELIVERY-NOTE-' . $id . '.pdf');

        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
