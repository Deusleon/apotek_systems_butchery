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
use View;

class SaleQuoteController extends Controller
{

    public function index()
    {
        $vat = Setting::where('id', 120)->value('value') / 100;//Get VAT %
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
            ->with(compact('current_stock'));
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Africa/Nairobi');
//some attributes declaration
        $cart = json_decode($request->cart, true);
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
                $details->vat = $details->price * 0.18;
                $details->amount = $details->price + $details->vat;
                $details->discount = $discount;
                $details->save();
            }

            session()->flash("alert-success", "Sale Quote recorded successfully!");
        }
        return back();

    }

    public function destroy(Request $request)
    {
        dd($request->all());
    }


}
