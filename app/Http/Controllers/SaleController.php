<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\Customer;
use App\PriceCategory;
use App\PriceList;
use App\Sale;
use App\SalesCredit;
use App\SalesDetail;
use App\Setting;
use App\StockTracking;
use App\Store;
use Dompdf\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Concerns\ToArray;

class SaleController extends Controller
{
    public function cashSale()
    {
        if (!Auth()->user()->checkPermission('View Cash Sales')) {
            abort(403, 'Access Denied');
        }

        $vat = Setting::where('id', 120)->value('value') / 100;//Get VAT %
        $back_date = Setting::where('id', 114)->value('value');
        $fixed_price = Setting::where('id', 124)->value('value');
        $enable_discount = Setting::where('id', 111)->value('value');
        $enable_paid = Setting::where('id', 112)->value('value');

        /*get default Price Category*/
        $default_sale_type = Setting::where('id', 125)->value('value');
        $sale_type = PriceCategory::where('name', $default_sale_type)->first();

        if ($sale_type != null) {
            $default_sale_type = $sale_type->id;
        } else {
            $default_sale_type = PriceCategory::first()->value('id');
        }


        $price_category = PriceCategory::all();
        $customers = Customer::orderBy('name', 'ASC')->get();
        $default_customer = Customer::where('name', 'CASH')->value('id');
        $current_stock = CurrentStock::all();
        return View::make('sales.cash_sales.index')
            ->with(compact('customers'))
            ->with(compact('price_category'))
            ->with(compact('current_stock'))->with(compact('enable_discount'))
            ->with(compact('back_date'))->with(compact('enable_paid'))
            ->with(compact('default_sale_type'))
            ->with(compact('default_customer'))
            ->with(compact('vat'))->with(compact('fixed_price'));
    }
    public function creditSale()
    {
        if (!auth()->user()->checkPermission('View Credit Sales') && 
            !auth()->user()->checkPermission('View Credit Tracking') && 
            !auth()->user()->checkPermission('View Credit Payment')) {
            abort(403, 'Access Denied');
        }

        $vat = Setting::where('id', 120)->value('value') / 100;//Get VAT %
        $back_date = Setting::where('id', 114)->value('value');
        $fixed_price = Setting::where('id', 124)->value('value');
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
        //Check customer
        $customers = Customer::orderBy('name', 'ASC')
            ->where('payment_term',2)
            ->get();
        $current_stock = CurrentStock::all();
        return View::make('sales.credit_sales.index')
            ->with(compact('customers'))
            ->with(compact('price_category'))
            ->with(compact('back_date'))
            ->with(compact('current_stock'))->with(compact('enable_discount'))
            ->with(compact('default_sale_type'))
            ->with(compact('vat'))->with(compact('fixed_price'));
    }
    public function getCreditsCustomers()
    {
        if (!Auth()->user()->checkPermission('View Credit Sales')) {
            abort(403, 'Access Denied');
        }

        $customers = Customer::where('total_credit', '>', 0)->get();
        return View::make('sales.credit_sales.payment')
            ->with(compact('customers'));
    }
    public function getPaymentsHistory()
    {
        if (!Auth()->user()->checkPermission('View Credit Sales')) {
            abort(403, 'Access Denied');
        }

        $customers = Customer::orderBy('name', 'asc')->get();

        $payments = SalesCredit::join('sales', 'sales.id', '=', 'sales_credits.sale_id')
            ->join('customers', 'customers.id', '=', 'sales.customer_id')
            ->orderByDesc('created_at')
            ->get();
        return view('sales.payment_history.index', compact('payments', 'customers'));
    }
    public function paymentHistoryFilter(Request $request)
    {
        if (!Auth()->user()->checkPermission('View Credit Sales')) {
            abort(403, 'Access Denied');
        }

        if ($request->ajax()) {
            $dates = explode(" - ", $request->date);
            if ($request->customer_id === null) {
                /*return all by date*/
                $payments = SalesCredit::join('sales', 'sales.id', '=', 'sales_credits.sale_id')
                    ->join('customers', 'customers.id', '=', 'sales.customer_id')
                    ->whereBetween(DB::raw('date(created_at)'), [date('Y-m-d', strtotime($dates[0])),
                        date('Y-m-d', strtotime($dates[1]))])
                        ->orderBy('sales_credits.created_at', 'desc')
                    ->get();
            } else {
                if ($request->date === null) {
                    $payments = SalesCredit::join('sales', 'sales.id', '=', 'sales_credits.sale_id')
                        ->join('customers', 'customers.id', '=', 'sales.customer_id')
                        ->where('sales.customer_id', $request->customer_id)
                        ->orderBy('sales_credits.created_at', 'desc')
                        ->get();
                } else {
                    $payments = SalesCredit::join('sales', 'sales.id', '=', 'sales_credits.sale_id')
                        ->join('customers', 'customers.id', '=', 'sales.customer_id')
                        ->whereBetween(DB::raw('date(created_at)'), [date('Y-m-d', strtotime($dates[0])),
                            date('Y-m-d', strtotime($dates[1]))])
                        ->where('sales.customer_id', $request->customer_id)
                        ->orderBy('sales_credits.created_at', 'desc')
                        ->get();
                }

            }

            return $payments;

        }
    }
    public function CreditSalePayment(Request $request)
    {
        if (!Auth()->user()->checkPermission('View Credit Sales')) {
            abort(403, 'Access Denied');
        }
        //Validating amount before submitting
        if($request->paid_amount>$request->balance)
        {
            session()->flash("alert-danger", "Amount paid should not exceed amount owed!");
            return back();
        }

        $credit = new SalesCredit;
        $customer = Customer::find($request->customer_id);
        $credit->sale_id = $request->sale_id;
        $credit->paid_amount = $request->paid_amount;
        $credit->balance = $request->balance - $request->paid_amount;
        $credit->remark = $request->remark;
        $credit->created_by = Auth::User()->id;
        $credit->updated_by = Auth::User()->id;
        $customer->total_credit -= $request->paid_amount;
        $credit->save();
        $customer->save();
        session()->flash("alert-success", "Payment recorded successfully!");
        return back();

    }
    public function getCreditSale(Request $request)
    {
        if (!Auth()->user()->checkPermission('View Credit Sales')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $from = $request->date[0];
        $to = $request->date[1];

        if ($request->ajax()) {
            if ($request->id) {
                $query = Sale::join('sales_credits', 'sales_credits.sale_id', '=', 'sales.id')
                    ->join('sales_details', 'sales_details.sale_id', '=', 'sales.id')
                    ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id')
                    ->where('date', '>=', $from)
                    ->where('date', '<=', $to)
                    ->join('customers', 'customers.id', '=', 'sales.customer_id')
                    ->where('customer_id', $request->id)
                    ->groupBy('sales_credits.sale_id')
                    ->orderBy('sales.id', 'DESC');

                    if (!is_all_store()) {
                       $query->where('inv_current_stock.store_id', $store_id);
                    }

                    $sales = $query->get();
            } else {
                $query = Sale::where('date', '>=', $from)
                    ->where('date', '<=', $to)
                    ->join('sales_credits', 'sales_credits.sale_id', '=', 'sales.id')
                    ->join('customers', 'customers.id', '=', 'sales.customer_id')
                    ->join('sales_details', 'sales_details.sale_id', '=', 'sales.id')
                    ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id')
                    ->groupBy('sales_credits.sale_id')
                    ->orderBy('sales.id', 'DESC');
                    
                    if (!is_all_store()) {
                       $query->where('inv_current_stock.store_id', $store_id);
                    }
                    $sales = $query->get();
            }

            foreach ($sales as $sale) {
                $outstanding = SalesCredit::where('sale_id', $sale->sale_id)->orderBy('id', 'desc')->first('balance');
                $discount = SalesDetail::where('sale_id', $sale->sale_id)->sum('discount');
                $amount = SalesDetail::where('sale_id', $sale->sale_id)->sum('amount');
                $sale->paid_amount = SalesCredit::where('sale_id', $sale->sale_id)->sum('paid_amount');
                $sale->balance = $outstanding->balance;
                $sale->total_amount = $amount - $discount;
            }

            $data = json_decode($sales, true);

            return $data;
        }
    }
    public function selectProducts(Request $request)
    {
        /*get default store*/
        $default_store = current_store_id();

        $products = PriceList::where('price_category_id', $request->get('id'))
            ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
            ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
            ->where('quantity', '>', 0)
            ->where('inv_products.status', '=', 1)
            ->where('inv_current_stock.store_id', $default_store)
            ->select('inv_products.id as id', 'name', 'barcode', 'inv_products.brand', 'inv_products.pack_size', 'sales_uom')
            ->groupBy('product_id')
            ->orderby('name', 'asc')
            // ->limit(100)
            ->get();
            
        if ($products->count() <= 0) {
            return response()->json([
                "message" => "No Products Found",
                "data" => []
            ]);
        }

        $output = [];
        foreach ($products as $product) {
            $latest = PriceList::where('price_category_id', $request->get('id'))
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->orderBy('stock_id', 'desc')
                ->where('product_id', $product->id)
                ->first('price');

            $quantity = CurrentStock::where('product_id', $product->id)
                ->where('store_id', $default_store)
                ->sum('quantity');
                
        if ($quantity > 0) {
            $name = $product->name.' '.($product->brand ? $product->brand.' ' : '').$product->pack_size.$product->sales_uom;
            $output[] = [
                "id"       => $product->id,
                "name"     => $name,
                "price"    => $latest->price ?? 0,
                "quantity" => $quantity
            ];
        }
        }

        return response()->json([
            "message" => "Products Found",
            "data"    => $output
        ]);
    }
    public function filterProductByWord(Request $request)
    {
        $default_store_id = current_store_id();

        if ($request->ajax()) {
            $products = PriceList::where('price_category_id', $request->price_category_id)
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->where('inv_current_stock.quantity', '>', 0)
                ->where('inv_products.status', '=', 1)
                ->where('inv_current_stock.store_id', $default_store_id)
                ->where(function ($query) use ($request) {
                    $query->where('inv_products.name', 'LIKE', "%{$request->word}%")
                        ->orWhere('inv_products.barcode',$request->word)
                        ->orWhere('inv_products.brand', 'LIKE', "%{$request->word}%")
                        ->orWhere('inv_products.pack_size', 'LIKE', "%{$request->word}%")
                        ->orWhere('inv_products.sales_uom', 'LIKE', "%{$request->word}%");
                })
                ->select(
                    'inv_products.id as id',
                    'inv_products.name',
                    'inv_products.brand',
                    'inv_products.pack_size',
                    'inv_products.sales_uom',
                )
                ->groupBy('inv_products.id')
                ->orderby('name', 'asc')
                // ->limit(20)
                ->get();
                
            if ($products->count() <= 0) {
                return response()->json([
                    "message" => "No Products Found",
                    "data"    => []
                ]);
            }

            $output = [];
            foreach ($products as $product) {
                $latest = PriceList::where('price_category_id', $request->price_category_id)
                    ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                    ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                    ->orderBy('stock_id', 'desc')
                    ->where('product_id', $product->id)
                    ->first('price');

                $quantity = CurrentStock::where('product_id', $product->id)
                    ->where('store_id', $default_store_id)
                    ->sum('quantity');

                if ($quantity > 0) {
                    $name = $product->name.' '
                        .($product->brand ? $product->brand.' ' : '')
                        .$product->pack_size
                        .$product->sales_uom;

                    $output[] = [
                        "id"       => $product->id,
                        "name"     => $name,
                        "price"    => $latest->price ?? 0,
                        "quantity" => $quantity,
                    ];
                }
            }

            return response()->json([
                "message" => "Product Selected",
                "data"    => $output
            ]);
        }
    }
    public function storeCashSale(Request $request)
    {
        if (!Auth()->user()->checkPermission('View Cash Sales')) {
            abort(403, 'Access Denied');
        }

        if ($request->ajax()) {
            $this->store($request);
            
            $receipt_print = Setting::where('id', 117)->value('value');
            if($receipt_print === "YES"){
                return response()->json([
                    'redirect_to' => route('getCashReceipt', '1')
                ]);
            }else{
                $this->cashSale();
                return;
            }
        }
    }
    public function store(Request $request)
    {
        $default_store = current_store_id();

        //some attributes declaration
        $vat = Setting::where('id', 120)->value('value') / 100;//Get VAT %
        $receipt_number = strtoupper(substr(md5(microtime()), rand(0, 26), 8));
        $cart = json_decode($request->cart, true);
        $discount = $request->discount_amount;
        $total = 0;
        if ($request->sale_date) {
            $date = $request->sale_date;
        } else {
            $date = date('Y/m/d');
        }
        //Avoid submission of a null Cart
        if (!$cart) {
            session()->flash("alert-danger", "You can not save an empty Cart!");
        } else {
            //calculating the Total Amount
            foreach ($cart as $bought) {
                $total += $bought['amount'];
            }

            //Saving Sale Summary and Get its ID
            $sale = DB::table('sales')->insertGetId(array(
                'receipt_number' => $receipt_number,
                'customer_id' => $request->customer_id,
                'price_category_id' => $request->price_category_id,
                'date' => $date,
                'created_by' => Auth::User()->id
            ));

            //Saving Sale Details
            foreach ($cart as $bought) {
                $bought['quantity'] = str_replace(',', '', $bought['quantity']);
                if ($bought['quantity'] > 0) {
                    $unit_discount = (($bought['amount'] / ($total ?: 1)) * $discount) / $bought['quantity'];
                    $unit_price = $bought['price'];
                    $stocks = CurrentStock::with('product')->where('product_id', $bought['product_id'])
                        ->where('store_id', $default_store)
                        ->where('quantity', '>', 0)
                        ->get();

                    foreach ($stocks as $stock) {
                        if ($bought['quantity'] <= $stock->quantity) {
                            $qty = $bought['quantity'];
                            $price = $unit_price;
                            $sale_discount = $unit_discount * $qty;
                            $stock->quantity -= $qty;
                            $stock->created_by = Auth::User()->id;
                            $bought['quantity'] -= $qty;
                        } else {
                            $qty = $stock->quantity;
                            $sale_discount = $unit_discount * $qty;
                            $price = $unit_price;
                            $stock->quantity = 0;
                            $stock->created_by = Auth::User()->id;
                            $bought['quantity'] -= $qty;
                        }
                        if ($qty > 0) {
                            $details = new SalesDetail;
                            $details->sale_id = $sale;
                            $details->stock_id = $stock->id;
                            $details->quantity = $qty;
                            $details->price = $price;
                            $details->vat = ($details->price * $vat) * $details->quantity;
                            $details->amount = ($details->price*$details->quantity) + $details->vat;
                            $details->discount = $sale_discount;
                            $details->save();
                            $stock->save();

                            $stock_tracking = new StockTracking;
                            $stock_tracking->stock_id = $stock->id;
                            $stock_tracking->product_id = $bought['product_id'];
                            $stock_tracking->quantity = $qty;
                            $stock_tracking->store_id = $default_store;
                            $stock_tracking->created_by = Auth::user()->id;
                            $stock_tracking->updated_by = Auth::user()->id;
                            if ($request->credit == 'Yes') {
                                $stock_tracking->out_mode = 'Credit Sales';
                            }else{
                                $stock_tracking->out_mode = 'Cash Sales';
                            }
                            $stock_tracking->updated_at = date('Y/m/d');
                            $stock_tracking->movement = 'OUT';
                            $stock_tracking->save();

                        }
                    }
                }
            }
            //credit Sale
            if ($request->credit == 'Yes') {

                $credit = new SalesCredit;
                $customer = Customer::find($request->customer_id);
                $credit->sale_id = $sale;
                $credit->paid_amount = $request->paid_amount;
                $credit->balance = ($total - $discount) - $request->paid_amount;
                $credit->grace_period = $request->grace_period;
                $credit->remark = $request->remark;
                $credit->created_by = Auth::User()->id;
                $credit->updated_by = Auth::User()->id;
                $customer->total_credit += $credit->balance;
                $credit->save();
                $customer->save();
              // session()->flash("alert-success", "Sale recorded successfully!");
            }

        }

    }
    public function receiptReprint($receipt, Request $request)
    {
        try {
            $receipt_size = Setting::where('id', 119)->value('value');
            $pharmacy['name'] = Setting::where('id', 100)->value('value');
            $pharmacy['logo'] = Setting::where('id', 105)->value('value');
            $pharmacy['address'] = Setting::where('id', 106)->value('value');
            $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');
            $pharmacy['phone'] = Setting::where('id', 107)->value('value');
            $pharmacy['slogan'] = Setting::where('id', 104)->value('value');
            $pharmacy['vrn_number'] = Setting::where('id', 103)->value('value');

            // Log::info('PrintSize', ['Size' => $receipt_size]);

            $receipt_number = $receipt ?? $request->reprint_receipt;
            $id = Sale::where('receipt_number', $receipt_number)->value('id');

            /*check if receipt is credit or not*/
            $credit_sale = SalesCredit::where('sale_id', $id)->get();
            $page = null;
            $paid = null;
            $balance = null;
            $remark = null;
            if ($credit_sale->isEmpty()) {
                /*not credit*/
                $page = 1; //normal sale
            } else {
                /*credit*/
                $page = -1; //credit
                $remarks = SalesCredit::select('remark')
                    ->where('sale_id', $id)
                    ->orderby('id', 'desc')
                    ->first();

                $amounts = SalesCredit::select('sale_id', 'remark', DB::raw('sum(paid_amount) as paid'), DB::raw('sum(balance) as balance'))
                    ->where('sale_id', $id)
                    ->groupby('sale_id')
                    ->first();
                $paid = $amounts->paid;
                $balance = $amounts->balance;
                $remark = $remarks->remark;
            }

            $sale_detail = SalesDetail::where('sale_id', $id)->get();
            $sales = array();
            $grouped_sales = array();
            $sn = 0;
            foreach ($sale_detail as $item) {
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
                    'receipt_number' => $item->sale['receipt_number'],
                    'name' => $item->currentStock['product']['name'],
                    'brand' => $item->currentStock['product']['brand'],
                    'pack_size' => $item->currentStock['product']['pack_size'],
                    'sales_uom' => $item->currentStock['product']['sales_uom'],
                    'sn' => $sn,
                    'quantity' => $item->quantity,
                    'vat' => $vat,
                    'discount' => $item->discount,
                    'discount_total' => $item->sale['cost']['discount'],
                    'price' => $item->price,
                    'amount' => $amount,
                    'sub_total' => $sub_total,
                    'grand_total' => ($item->sale['cost']['amount']) - ($item->sale['cost']['discount']),
                    'total_vat' => ($item->sale['cost']['vat']),
                    'sold_by' => $item->sale['user']['name'],
                    'customer' => $item->sale['customer']['name'],
                    'customer_tin' => $item->sale['customer']['tin'],
                    'paid' => $paid,
                    'balance' => $balance,
                    'remark' => $remark,
                    'created_at' => date('Y/m/d', strtotime($item->sale['date']))
                ));
            }


            foreach ($sales as $val) {
                if (array_key_exists('receipt_number', $val)) {
                    $grouped_sales[$val['receipt_number']][] = $val;
                }
            }

            $data = $grouped_sales;


            if ($receipt_size == '58mm Thermal Paper' && $page == 1) {
                $pdf = PDF::loadView('sales.cash_sales.receipt_thermal',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper([0, 0, 163, 600], '');
                return $pdf->stream($request->reprint_receipt . '.pdf');
            } else if ($receipt_size == '58mm Thermal Paper' && $page == -1) {
                $pdf = PDF::loadView('sales.cash_sales.credit_receipt_thermal',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper([0, 0, 163, 600], '');
                return $pdf->stream($request->reprint_receipt . '.pdf');
            }else if ($receipt_size == 'A4 / Letter' && $page == 1) {
                $pdf = PDF::loadView('sales.cash_sales.receipt_A4',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper('a4', '');
                return $pdf->stream($request->reprint_receipt . '.pdf');
            }else if ($receipt_size == 'A4 / Letter' && $page == -1) {
                $pdf = PDF::loadView('sales.cash_sales.credit_receipt_A4',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper('a4', '');
                return $pdf->stream($request->reprint_receipt . '.pdf');
            } else if ($receipt_size == '80mm Thermal Paper' && $page == 1) {
                $pdf = PDF::loadView('sales.cash_sales.receipt_thermal_80',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper([0, 0, 227, 600], '');
                return $pdf->stream($request->reprint_receipt . '.pdf');
            } else if ($receipt_size == '80mm Thermal Paper' && $page == -1) {
                $pdf = PDF::loadView('sales.cash_sales.credit_receipt_thermal_80',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper([0, 0, 227, 600], '');
                return $pdf->stream($request->reprint_receipt . '.pdf');
            } else if ($receipt_size == 'A5 / Half Letter' && $page == 1) {
                $pdf = PDF::loadView('sales.cash_sales.receipt',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper('a5', '');
                return $pdf->stream($request->reprint_receipt . '.pdf');
            } else if ($receipt_size == 'A5 / Half Letter' && $page == -1) {
                $pdf = PDF::loadView('sales.cash_sales.credit_receipt',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper('a5', '');
                return $pdf->stream($request->reprint_receipt . '.pdf');
            } else {
                echo "<script>window.close();</script>";
            }

        }catch (Exception $e)
        {
            Log::info("Error",['PrintingError'=>$e]);
        }


    }
    
    public function storeCreditSale(Request $request)
{
    Log::info($request->all());

    if (!Auth()->user()->checkPermission('View Credit Sales')) {
        abort(403, 'Access Denied');
    }

    if ($request->ajax()) {
        $decoded = null;
        if (is_array($request->customer_id)) {
            $decoded = $request->customer_id;
        } else {
            $decoded = @json_decode($request->customer_id, true);
        }

        if (is_array($decoded) && isset($decoded['id'])) {
            $request->merge(['customer_id' => $decoded['id']]);
            $this->store($request);

            $receipt_print = Setting::where('id', 117)->value('value');
            if($receipt_print === "YES"){
                return response()->json([
                    'to' => 'receipt',
                    'redirect_to' => route('getCashReceipt', '-1')
                ]);
            }else{
                return response()->json([
                    'to' => 'sale',
                    'redirect_to' => route('credit-sales.creditSale') 
                ]);
            }
        }

        if (is_numeric($request->customer_id) || is_int($decoded)) {
            $request->merge(['customer_id' => (int) $request->customer_id]);
            $this->store($request);
            $receipt_print = Setting::where('id', 117)->value('value');
            if($receipt_print === "YES"){
                return response()->json([
                    'to' => 'receipt',
                    'redirect_to' => route('getCashReceipt', '-1')
                ]);
            }else{
                return response()->json([
                    'to' => 'sale',
                    'redirect_to' => route('credit-sales.creditSale') 
                ]);
            }
        }

        session()->flash("alert-danger", "Customer Name is Required");
        return back();
    }
}


    public function getSalesHistory(Request $request)
    {
        if (!Auth()->user()->checkPermission('View Sales History')) {
            abort(403, 'Access Denied');
        }

        $range = $request->range ?? null;
        $from = $range[0] ?? null;
        $to = $range[1] ?? null;
        $storeId = current_store_id();

        // Parse dates safely using Carbon (if provided)
        $fromDate = $toDate = null;
        if ($from && $to) {
            try {
                $fromDate = \carbon\Carbon::parse($from)->startOfDay();
                $toDate   = \carbon\Carbon::parse($to)->endOfDay();
            } catch (\Exception $e) {
                // invalid date format -> return empty or handle as you like
                return response()->json(['data' => []]);
            }
        }

        // Build the sales_details aggregation subquery
        $salesDetailsSubquery = DB::table('sales_details')
            ->select([
                'sale_id',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(discount) as total_discount'),
                DB::raw('SUM(vat) as total_vat'),
                DB::raw('COUNT(*) as items_count'),
            ])
            // Apply store filter inside subquery so joinSub uses it
            ->when(!is_all_store(), function ($q) use ($storeId) {
                $q->join('inv_current_stock', 'sales_details.stock_id', '=', 'inv_current_stock.id')
                ->where('inv_current_stock.store_id', $storeId);
            })
            ->groupBy('sale_id');

        // Main query: join aggregated subquery
        $query = Sale::with(['customer', 'cost'])
            ->joinSub($salesDetailsSubquery, 'sales_summary', function ($join) {
                $join->on('sales.id', '=', 'sales_summary.sale_id');
            });

        // Date filter on sales.date column (use full datetime range)
        if ($fromDate && $toDate) {
            $query->whereBetween('sales.date', [$fromDate->toDateTimeString(), $toDate->toDateTimeString()]);
            // alternatively: ->whereDate('sales.date', '>=', $fromDate)->whereDate('sales.date', '<=', $toDate);
        }

        // Select columns (ensure sales.* so Eloquent can hydrate model)
        $sales = $query->select([
                'sales.*',
                'sales_summary.total_amount',
                'sales_summary.total_discount',
                'sales_summary.total_vat',
                'sales_summary.items_count'
            ])
            ->orderBy('sales.id', 'desc')
            ->get();

        return response()->json([
            "data" => $sales
        ]);
    }
    public function getSalesHistoryData(Request $request)
    {
        if (!Auth()->user()->checkPermission('View Sales History')) {
            abort(403, 'Access Denied');
        }
        $saleReceipt = $request->receipt;
        $storeId = current_store_id();

        // Get aggregated data for sales_details
        $salesDetails = DB::table('sales_details')
                    ->join('inv_current_stock', 'sales_details.stock_id', '=', 'inv_current_stock.id')
                    ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                    ->where('sale_id', $saleReceipt);

        // Store filter
        if (!is_all_store()) {
        $salesDetails->where('inv_current_stock.store_id', $storeId);
        }

        $results = $salesDetails->select('sales_details.*', 'inv_products.name', 'inv_products.brand', 'inv_products.pack_size', 'inv_products.sales_uom')->get();
            
        return response()->json([
            "data" => $results
        ]);
    } 
    public function salesDetailsData(Request $request)
    {

        $data = DB::table('sales')
            ->join('customers','customers.id','=','sales.customer_id')
            ->join('sales_details','sales.id','=','sales_details.sale_id')
            ->join('inv_current_stock','sales_details.stock_id','=','inv_current_stock.id')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->select('inv_products.name','sales_details.price','sales_details.quantity','customers.name as customer_name')
            ->where('sales.receipt_number','=',$request->receipt_number)
            ->get();

        return $data;
    }
    public function SalesHistory()
    {
        if (!Auth()->user()->checkPermission('View Sales History')) {
            abort(403, 'Access Denied');
        }

        $vat = Setting::where('id', 120)->value('value') / 100; 
        $customers = Customer::orderBy('name', 'ASC')->get();
        return View::make('sales.sales_history.index')
            ->with(compact('vat'))->with(compact('customers'));
    }
    public function creditsTracking()
    {
        if (!Auth()->user()->checkPermission('View Credit Sales')) {
            abort(403, 'Access Denied');
        }
        $customers = Customer::where('total_credit', '>', 0)->get();
        return View::make('sales.credit_tracking.index')
            ->with(compact('customers'));
    }
    public function getCashReceipt($page)
    {
        if (!Auth()->user()->checkPermission('View Cash Sales')) {
            abort(403, 'Access Denied');
        }
        $receipt_size = Setting::where('id', 119)->value('value');
        $pharmacy['name'] = Setting::where('id', 100)->value('value');
        $pharmacy['logo'] = Setting::where('id', 105)->value('value');
        $pharmacy['address'] = Setting::where('id', 106)->value('value');
        $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');
        $pharmacy['phone'] = Setting::where('id', 107)->value('value');
        $pharmacy['slogan'] = Setting::where('id', 104)->value('value');
        $pharmacy['vrn_number'] = Setting::where('id', 103)->value('value');


        $id = SalesDetail::orderBy('id', 'desc')->value('sale_id');

        $paid = null;
        $balance = null;
        $remark = null;
        if (intVal($page) === -1) {
            /*get paid amount*/
            $amounts = SalesCredit::select('sale_id', 'remark', DB::raw('sum(paid_amount) as paid'), DB::raw('sum(balance) as balance'))
                ->where('sale_id', $id)
                ->groupby('sale_id')
                ->first();

            $paid = $amounts->paid;
            $balance = $amounts->balance;
            $remark = $amounts->remark;
        }

        $sale_detail = SalesDetail::where('sale_id', $id)->get();
        // $sale_date = Sale::select('date')->where('id', $id)->get();
            // dd($id);
        $sales = array();
        $grouped_sales = array();
        $sn = 0;
        foreach ($sale_detail as $item) {
            $receipt_no = $item->sale['receipt_number'];
            $amount = $item->amount - $item->discount;
            if (intVal($item->vat) === 0) {
                $vat_percent = 0;
            } else {
                $vat_percent = $item->vat / $item->price;
            }
            $sub_total = ($amount / (1 + $vat_percent));
            $vat = $item->vat * $item->quantity;
            $sn++;
            // dd($item->sale['cost']);
            array_push($sales, array(
                'receipt_number' => $item->sale['receipt_number'],
                'name' => $item->currentStock['product']['name'],
                'brand' => $item->currentStock['product']['brand'],
                'pack_size' => $item->currentStock['product']['pack_size'],
                'sales_uom' => $item->currentStock['product']['sales_uom'],
                'sn' => $sn,
                'quantity' => $item->quantity,
                'vat' => $vat,
                'discount' => $item->discount,
                'discount_total' => $item->sale['cost']['discount'],
                'price' => $item->price,
                'amount' => $amount,
                'sub_total' => $sub_total,
                'grand_total' => ($item->sale['cost']['amount']) - ($item->sale['cost']['discount']),
                'total_vat' => ($item->sale['cost']['vat']),
                'sold_by' => $item->sale['user']['name'],
                'customer' => $item->sale['customer']['name'],
                'customer_tin' => $item->sale['customer']['tin'],
                'paid' => $paid,
                'balance' => $balance,
                'remark' => $remark,
                'created_at' => $item->sale['date']
            ));
        }
        Log::info('Details', $sales);


        foreach ($sales as $val) {
            if (array_key_exists('receipt_number', $val)) {
                $grouped_sales[$val['receipt_number']][] = $val;
            }
        }

        $data = $grouped_sales;

        if ($receipt_size === '58mm Thermal Paper') {
            if ($page === "-1") {
                $pdf = PDF::loadView('sales.cash_sales.credit_receipt_thermal',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper([0, 0, 163, 600], '');
            } else {
                $pdf = PDF::loadView('sales.cash_sales.receipt_thermal',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper([0, 0, 163, 600], '');
            }

            return $pdf->stream($receipt_no . '.pdf');

        }
        else if ($receipt_size === '80mm Thermal Paper') {
            if ($page === "-1") {
                $pdf = PDF::loadView('sales.cash_sales.credit_receipt_thermal_80',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper([0, 0, 227, 600], '');
            } else {
                $pdf = PDF::loadView('sales.cash_sales.receipt_thermal_80',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper([0, 0, 227, 600], '');
            }

            return $pdf->stream($receipt_no . '.pdf');

        }
        else if ($receipt_size === 'A4 / Letter') {
            if ($page === "-1") {
                $pdf = PDF::loadView('sales.cash_sales.credit_receipt_A4',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper( 'a4', '' );
            } else {
                $pdf = PDF::loadView('sales.cash_sales.receipt_A4',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper( 'a4', '' );
            }

            return $pdf->stream($receipt_no . '.pdf');

        }
        else if ($receipt_size === 'A5 / Half Letter') {
            if ($page === "-1") {
                $pdf = PDF::loadView('sales.cash_sales.credit_receipt',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper( 'a5', '' );
            } else {
                $pdf = PDF::loadView('sales.cash_sales.receipt',
                    compact('data', 'pharmacy', 'page'))
                    ->setPaper( 'a5', '' );
            }

            return $pdf->stream($receipt_no . '.pdf');

        }

        else {
            echo "<script>window.close();</script>";
        }

    }
    public function getCreditReceipt()
    {
        if (!Auth()->user()->checkPermission('View Credit Sales')) {
            abort(403, 'Access Denied');
        }
        $receipt_size = Setting::where('id', 119)->value('value');
        $pharmacy['name'] = Setting::where('id', 100)->value('value');
        $pharmacy['logo'] = Setting::where('id', 105)->value('value');
        $pharmacy['address'] = Setting::where('id', 106)->value('value');
        $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');
        $pharmacy['vrn_number'] = Setting::where('id', 103)->value('value');

        $id = SalesDetail::orderBy('id', 'desc')->value('sale_id');
        $sale_detail = SalesDetail::join('sales_credits', 'sales_credits.sale_id', '=', 'sales_details.sale_id')
            ->where('sales_credits.sale_id', $id)->get();
        $sales = array();
        $grouped_sales = array();
        $sn = 0;
        foreach ($sale_detail as $item) {

            $amount = $item->amount - $item->discount;
            $vat_percent = $item->vat / $item->price;
            $sub_total = ($amount / (1 + $vat_percent));
            $vat = $amount - $sub_total;
            $sn++;
            array_push($sales, array(
                'receipt_number' => $item->sale['receipt_number'],
                'name' => $item->currentStock['product']['name'],
                'paid' => $item->paid_amount,
                'balance' => $item->balance,
                'sn' => $sn,
                'quantity' => $item->quantity,
                'vat' => $vat,
                'discount' => $item->discount,
                'price' => $item->price,
                'amount' => $amount,
                'sub_total' => $sub_total,
                'total_discount' => $item->sale['cost']['discount'],
                'grand_total' => ($item->sale['cost']['amount']) - ($item->sale['cost']['discount']),
                'total_vat' => ($item->sale['cost']['vat']),
                'sold_by' => $item->sale['user']['name'],
                'customer' => $item->sale['customer']['name'],
                'customer_tin' => $item->sale['customer']['tin'],
                'created_at' => date('Y/m/d', strtotime($item->sale['date']))
            ));
        }


        foreach ($sales as $val) {
            if (array_key_exists('receipt_number', $val)) {
                $grouped_sales[$val['receipt_number']][] = $val;
            }
        }
        $data = $grouped_sales;
        $pdf = PDF::loadView('sales.credit_sales.receipt',
            compact('data', 'pharmacy'));
        return $pdf->download('Recept.pdf');
    }
}
