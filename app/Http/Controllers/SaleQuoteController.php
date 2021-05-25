<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\Customer;
use App\PriceCategory;
use App\SalesQuote;
use App\SalesQuoteDetail;
use App\Setting;
use Auth;
use DB;
use Illuminate\Http\Request;
use PDF;
use View;

class SaleQuoteController extends Controller
{

    public function index()
    {
        $vat = Setting::where('id', 120)->value('value') / 100;//Get VAT %
        $enable_discount = Setting::where('id', 111)->value('value');

        $price_category = PriceCategory::orderBy('id', 'ASC')->get();
        $sale_quotes = SalesQuote::orderBy('id', 'DESC')->get();
        $customers = Customer::orderBy('id', 'ASC')->get();
        $current_stock = CurrentStock::all();
        $count = $sale_quotes->count();
        return View::make('sales.sale_quotes.index')
            ->with(compact('vat'))
            ->with(compact('count'))
            ->with(compact('sale_quotes'))
            ->with(compact('customers'))
            ->with(compact('price_category'))
            ->with(compact('current_stock'))->with(compact('enable_discount'));
    }

    public function getQuotes(Request $request)
    {
        $date_range = explode('-', $request->date);
        $from = date('Y-m-d', strtotime(trim($date_range[0])));
        $to = date('Y-m-d', strtotime(trim($date_range[1])));
        $sale_quotes = SalesQuote::with(['cost'])->orderBy('id', 'DESC')->whereBetween(DB::raw("DATE(`date`)"), [$from, $to])->get();
        foreach($sale_quotes as $sale_quote){
            $sale_quote->details;
        }
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

    public function store(Request $request)
    {
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
                'created_by' => Auth::User()->id
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

        if ($receipt_size === 'Thermal Paper') {
            $pdf = PDF::loadView('sales.cash_sales.receipt_thermal',
                compact('data', 'pharmacy', 'page'));
        } else {
            $pdf = PDF::loadView('sales.sale_quotes.quote_receipt',
                compact('data', 'pharmacy', 'page'));
        }

        return $pdf->stream($id . '.pdf');
    }

    public function receiptReprint($id)
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

        if ($receipt_size === 'Thermal Paper') {
            $pdf = PDF::loadView('sales.cash_sales.receipt_thermal',
                compact('data', 'pharmacy', 'page'));
        } else {
            $pdf = PDF::loadView('sales.sale_quotes.quote_receipt',
                compact('data', 'pharmacy', 'page'));
        }
        return $pdf->stream($id . '.pdf');

    }

}
