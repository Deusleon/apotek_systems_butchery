<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\GoodsReceiving;
use App\Invoice;
use App\Order;
use App\OrderDetail;
use App\PriceCategory;
use App\PriceList;
use App\Product;
use App\Setting;
use App\StockTracking;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use View;

class GoodsReceivingController extends Controller
{

    public function index()
    {
        $orders = Order::where('status', '<=', '3')
            ->get();
        $order_details = OrderDetail::all();
        $suppliers = Supplier::all();
        $item_stocks = GoodsReceiving::all();
        $current_stock = $this->allProductToReceive();
        $price_categories = PriceCategory::all();
        $invoices = Invoice::all();
        $batch_setting = Setting::where('id', 110)->value('value');/*batch number setting*/
        $invoice_setting = Setting::where('id', 115)->value('value');/*invoice setting*/
        $back_date = Setting::where('id', 114)->value('value');

        $selling_prices = DB::table('sales_prices');
        $order_receiving = DB::table('order_details')
            ->join('inv_products', 'inv_products.id', '=', 'order_details.product_id')
            ->select('order_details.id as id', 'name', 'order_details.ordered_qty as quantity', 'unit_price as price', 'vat', 'discount', 'amount')
            ->groupBy('order_details.id');

        return View::make('purchases.goods_receiving.index',
            (compact('orders', 'order_details', 'suppliers',
                'order_receiving', 'price_categories', 'buying_prices',
                'current_stock', 'item_stocks', 'invoices', 'batch_setting', 'invoice_setting','back_date')));
    }


    public function getItemPrice(Request $request)

    {
        if ($request->ajax()) {

            $max_prices = array();
            if ($request->supplier_id != null) {

                $supplier_id = GoodsReceiving::where('supplier_id', $request->supplier_id)
                    ->where('product_id', $request->product_id)
                    ->value('supplier_id');

                if ($supplier_id === null) {
                    $supplier_id = GoodsReceiving::where('product_id', $request->product_id)
                        ->orderby('id', 'DESC')
                        ->value('supplier_id');
                }

                $products = PriceList::where('price_category_id', $request->price_category)
                    ->where('inv_incoming_stock.supplier_id', $supplier_id)
                    ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                    ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                    ->join('inv_incoming_stock', 'inv_incoming_stock.product_id', '=', 'inv_products.id')
                    ->Where('inv_products.status', '1')
                    ->Where('inv_products.id', $request->product_id)
                    ->select('inv_products.id as id', 'name', 'supplier_id')
                    ->groupBy('inv_current_stock.product_id')
                    ->get();

            }

            foreach ($products as $product) {
                $data = PriceList::select('stock_id', 'price')->where('price_category_id', $request->price_category)
                    ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                    ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                    ->orderBy('sales_prices.id', 'desc')
                    ->where('product_id', $product->id)
                    ->first('price');

                $quantity = CurrentStock::where('product_id', $product->id)->sum('quantity');

                array_push($max_prices, array(
                    'name' => $data->currentStock['product']['name'],
                    'unit_cost' => $data->currentStock['unit_cost'],
                    'price' => $data->price,
                    'quantity' => $quantity,
                    'id' => $data->stock_id,
                    'product_id' => $product->id,
                    'supplier_id' => $product->supplier_id
                ));
            }

            return $max_prices;
        }

    }

    public function getItemPrice2()
    {

    }

    public function itemReceive(Request $request)
    {

        if ($request->ajax()) {

            $cart = json_decode($request->cart, true);

            $quantity = $cart['quantity'];
            $unit_sell_price = str_replace(',', '', $request->sell_price);
            // dd($request->sell_price);
            $unit_buy_price = str_replace(',', '', $request->unit_cost);
            $total_buyprice = $quantity * $unit_buy_price;
            $total_sellprice = $quantity * $unit_sell_price;
            $profit = $total_sellprice - $total_buyprice;


            /*check if there exists 0 qty of that product*/
            $current_stock = CurrentStock::where('product_id', $cart['id'])
                ->where('quantity', '=', 0)
                ->get();
            if (!($current_stock->isEmpty())) {
                //update
                $update_stock = CurrentStock::find($current_stock->first()->id);
                $update_stock->batch_number = $request->batch_number;
                if ($request->expire_date != null){
                    $update_stock->expiry_date = date('Y-m-d', strtotime($request->expire_date));
                }else{
                    $update_stock->expiry_date = null;
                }
                $update_stock->quantity = $cart['quantity'];
                $update_stock->unit_cost = str_replace(',', '', $request->unit_cost);
                $update_stock->store_id = 1;
                $update_stock->save();
                $overal_stock_id = $update_stock->id;
            } else {
                $stock = new CurrentStock;
                $stock->product_id = $cart['id'];
                $stock->batch_number = $request->batch_number;
                if ($request->expire_date != null){
                    $stock->expiry_date = date('Y-m-d', strtotime($request->expire_date));
                }else{
                    $stock->expiry_date = null;
                }
                $stock->quantity = $cart['quantity'];
                $stock->unit_cost = str_replace(',', '', $request->unit_cost);
                $stock->store_id = 1;
                $stock->save();
                $overal_stock_id = $stock->id;
            }


            /*insert into stock tracking*/
            $stock_tracking = new StockTracking;
            $stock_tracking->stock_id = $overal_stock_id;
            $stock_tracking->product_id = $cart['id'];
            $stock_tracking->quantity = $cart['quantity'];
            $stock_tracking->store_id = 1;
            $stock_tracking->updated_by = Auth::user()->id;
            $stock_tracking->out_mode = 'New Product Purchase';
            $stock_tracking->updated_at = date('Y-m-d');
            $stock_tracking->movement = 'IN';
            $stock_tracking->save();

            $price = new PriceList;
            $price->stock_id = $overal_stock_id;
            $price->price = str_replace(',', '', $request->sell_price);
            $price->price_category_id = $request->price_category;
            $price->status = 1;
            $price->created_at = date('Y-m-d H:m:s');
            $price->save();

            $incoming_stock = new GoodsReceiving;
            $incoming_stock->product_id = $cart['id'];
            $incoming_stock->supplier_id = $request->supplier;
            $incoming_stock->invoice_no = $request->invoice_no;
            $incoming_stock->batch_number = $request->batch_number;
            if ($request->expire_date != null){
                $incoming_stock->expire_date = date('Y-m-d', strtotime($request->expire_date));
            }else{
                $incoming_stock->expire_date = null;
            }
            $incoming_stock->quantity = $cart['quantity'];
            $incoming_stock->unit_cost = str_replace(',', '', $request->unit_cost);
            $incoming_stock->total_cost = $total_buyprice;
            $incoming_stock->total_sell = $total_sellprice;
            $incoming_stock->item_profit = $profit;
            $incoming_stock->created_by = Auth::user()->id;
            $incoming_stock->sell_price = str_replace(',', '', $request->sell_price);
            if ($request->purchase_date != null){
                $incoming_stock->created_at = date('Y-m-d', strtotime($request->purchase_date));
            }else{
                $incoming_stock->created_at = date('Y-m-d');

            }
            $incoming_stock->save();

            $message = array();
            array_push($message, array(
                'message' => 'success'
            ));
            return $message;

        }

    }

    public function orderReceive(Request $request)
    {

//        dd($request->all());

        $quantity = str_replace(',', '', $request->quantity);
        $unit_sell_price = str_replace(',', '', $request->sell_price);
        $unit_buy_price = str_replace(',', '', $request->price);
        $total_buyprice = $quantity * $unit_buy_price;
        $total_sellprice = $quantity * $unit_sell_price;
        $profit = $total_sellprice - $total_buyprice;

        $date = date('Y-m-d');
        $stock = new CurrentStock;
        $stock->product_id = $request->product_id;
        $stock->batch_number = $request->batch_number;
        if ($request->expire_date != null){
            $stock->expiry_date = $request->expire_date;
        }else{
            $stock->expiry_date = null;
        }
        $stock->quantity = $quantity;
        $stock->unit_cost = str_replace(',', '', $request->price);
        $stock->store_id = 1;
        $stock->save();


        /*insert into stock tracking*/
        $stock_tracking = new StockTracking;
        $stock_tracking->stock_id = $stock->id;
        $stock_tracking->product_id = $request->product_id;
        $stock_tracking->quantity = $quantity;
        $stock_tracking->store_id = 1;
        $stock_tracking->updated_by = Auth::user()->id;
        $stock_tracking->out_mode = 'New Product Purchase';
        $stock_tracking->updated_at = date('Y-m-d');
        $stock_tracking->movement = 'IN';
        $stock_tracking->save();


        $order_details = OrderDetail::find($request->order_details_id);
        $order_details->received_by = Auth::user()->id;
        $order_details->received_at = $date;
        $order_details->received_qty = $quantity;
        $order_details->item_status = 'Received';
        $order_details->save();

        $price = new PriceList;
        $price->stock_id = $stock->id;
        $price->price = str_replace(',', '', $request->sell_price);
        $price->price_category_id = $request->price_category;
        $price->save();

        $order_id = OrderDetail::where('id', $request->order_details_id)->value('order_id');
        $number_of_items = OrderDetail::where('order_id', $order_id)->count();
        $number_of_received_item = OrderDetail::where('order_id', $order_id)
            ->where('item_status', 'Received')->count();

        $order = Order::find($order_id);
        $order->received_at = $date;
        $order->received_by = Auth::user()->id;
        if ($number_of_items > $number_of_received_item) {
            $order->status = '2';
        } else {
            $order->status = '3';
        }

        $order->save();

        $incoming_stock = new GoodsReceiving;
        $incoming_stock->product_id = $request->product_id;
        $incoming_stock->supplier_id = $request->supplier_id;
        $incoming_stock->invoice_no = $request->invoice;
        $incoming_stock->batch_number = $request->batch_number;
        if ($request->expire_date != null){
            $incoming_stock->expire_date = $request->expire_date;
        }else{
            $incoming_stock->expire_date = null;
        }
        $incoming_stock->quantity = $quantity;
        $incoming_stock->unit_cost = str_replace(',', '', $request->price);
        $incoming_stock->sell_price = str_replace(',', '', $request->sell_price);
        $incoming_stock->order_details_id = $request->order_details_id;
        $incoming_stock->total_cost = $total_buyprice;
        $incoming_stock->total_sell = $total_sellprice;
        $incoming_stock->item_profit = $profit;
        $incoming_stock->save();


        session()->flash("alert-success", "Item Received Successfully!");
        return redirect()->route('goods-receiving.index');

    }

    public function allProductToReceive()
    {
        $max_prices = array();

        $products = Product::select('id', 'name')
            ->where('status', '=', 1)
            ->groupBy('id', 'name')
            ->get();

        foreach ($products as $product) {

            $data = CurrentStock::where('product_id', $product->id)
                ->orderBy('id', 'desc')
                ->first();

            if ($data != null) {
                array_push($max_prices, array(
                    'product_name' => $data->product['name'],
                    'unit_cost' => $data->unit_cost,
                    'selling_price' => $data->price,
                    'id' => $data->id,
                    'product_id' => $data->product_id
                ));
            } else {
                array_push($max_prices, array(
                    'product_name' => $product->name,
                    'unit_cost' => null,
                    'selling_price' => null,
                    'id' => null,
                    'product_id' => $product->id
                ));
            }

        }

        return $max_prices;

    }

    public function filterInvoice(Request $request)
    {
        if ($request->ajax()) {
            $invoices = Invoice::select('invoice_no', 'id')
                ->where('supplier_id', $request->supplier_id)
                ->get();

            return json_decode($invoices, true);
        }
    }


}


