<?php

namespace App\Http\Controllers;

use App\CommonFunctions;
use App\GoodsReceiving;
use App\Product;
use App\Setting;
use App\Supplier;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use View;

class OrderController extends Controller
{
    //
    public function index()
    {
        $orders = Order::with(['supplier', 'details.product:id,name,pack_size,brand,sales_uom'])->where('status', '!=', '0')->orderBy('ordered_at', 'desc')->get();
        $suppliers = Supplier::orderBy('name', 'ASC')->get();
        $vat = Setting::where('id', 120)->value('value') / 100;//Get VAT %
        return View::make('purchases.purchase_order.index',
            compact('suppliers', 'vat', 'orders'));
    }

   public function orderList()
{
    $orders = Order::with(['supplier', 'details.product:id,name,pack_size,brand,sales_uom'])
        ->where('status', '!=', 'θ')
        ->get();
    
    return View::make('purchases.purchase_order.orderlist', compact('orders'));
}

public function approve(Request $request, $id)
{
    try {
        \Log::info('Approving order ID: ' . $id); // Add logging
        
        $order = Order::findOrFail($id);
        
        // Update order status to '2' (Approved)
        $order->status = '2';
        $order->save();

        session()->flash("alert-success", "Order approved Successfully!");

        \Log::info('Order approved successfully: ' . $id); // Add logging

        return response()->json([
            'success' => true,
            'message' => 'Order approved successfully!'
        ]);
    } catch (\Exception $e) {
        \Log::error('Error approving order: ' . $e->getMessage()); // Add logging
        
        return response()->json([
            'success' => false,
            'message' => 'Error approving order: ' . $e->getMessage()
        ], 500);
    }
}
    public function store(Request $request)
    {
        $number_gen = new CommonFunctions();
        $order_number = $number_gen->generateNumber();

        $cart = json_decode($request->cart, true);
        $total = 0;

        if (!$cart) {
            session()->flash("alert-danger", "You can not save an empty Cart!");
        } else {
            //calculating the Total Amount
            foreach ($cart as $item) {
                $total += floatval(preg_replace('/[^\d.]/', '', $item['amount']));
            }

            //Saving Sale Summary and Get its ID

            $order = DB::table('orders')->insertGetId(array(
                'order_number' => $order_number,
                'supplier_id' => $request->supplier_ids,
                'ordered_by' => Auth::user()->id,
                'ordered_at' => date('Y-m-d'),
                'total_vat' => $request->vat_total_amount,
                'total_amount' => $request->total_amount,
                'Comment' => $request->note,
                'status' => '1'

            ));

            foreach ($cart as $item) {
                $amount = floatval(preg_replace('/[^\d.]/', '', $item['amount']));
                $price = floatval(preg_replace('/[^\d.]/', '', $item['price']));
                $vat = floatval(preg_replace('/[^\d.]/', '', $item['vat']));
                DB::table('order_details')->insert(array(
                    'order_id' => $order,
                    'product_id' => $item['product_id'],
                    'ordered_qty' => str_replace(',', '', $item['quantity']),
                    'unit_price' => $price,
                    'vat' => $vat,
                    'amount' => $amount,
                ));
            }

            session()->flash("alert-success", "Order Recorded Successfully!");
        }
        return back();
    }

    public function filterSupplierProduct(Request $request)
    {

        if ($request->ajax()) {
            $max_prices = array();
            $current_stock = Product::where('status', 1)
                ->limit(500)
                ->get();

            foreach ($current_stock as $stock) {

                $data = GoodsReceiving::select('unit_cost', 'product_id')
                    ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
                    ->where('inv_products.status', 1)
                    ->where('supplier_id', $request->supplier_id)
                    ->where('product_id', $stock->id)
                    ->orderBy('inv_incoming_stock.id', 'desc')
                    ->first('unit_cost');

                if ($data === null) {
                    $data = GoodsReceiving::select('unit_cost', 'product_id')
                        ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
                        ->where('inv_products.status', 1)
                        ->where('product_id', $stock->id)
                        ->orderBy('inv_incoming_stock.id', 'desc')
                        ->first('unit_cost');
                }

                array_push($max_prices, array(
                    'name' => $stock->name . ' ' . ($stock->brand ?? '') . ' ' . ($stock->pack_size ?? '') . ($stock->sales_uom ?? ''),
                    'unit_cost' => $data['unit_cost'],
                    'product_id' => $stock->id,
                    'incoming_id' => $data['id']
                ));

            }

            $sort_column = array_column($max_prices, 'name');
            array_multisort($sort_column, SORT_ASC, $max_prices);

            return $max_prices;

        }

    }

    public function filterSupplierProductInput(Request $request)
    {

        if ($request->ajax()) {
            $max_prices = array();
            $current_stock = Product::where('status', 1)
                ->where('name', 'LIKE', "%{$request->word}%")
                ->limit(10)
                ->get();

            foreach ($current_stock as $stock) {

                $data = GoodsReceiving::select('unit_cost', 'product_id')
                    ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
                    ->where('inv_products.status', 1)
                    ->where('supplier_id', $request->supplier_id)
                    ->where('product_id', $stock->id)
                    ->orderBy('inv_incoming_stock.id', 'desc')
                    ->first('unit_cost');

                array_push($max_prices, array(
                    'name' => $stock->name . ' ' . ($stock->brand ?? '') . ' ' . ($stock->pack_size ?? '') . ' ' . ($stock->sales_uom ?? ''),
                    'unit_cost' => $data['unit_cost'],
                    'product_id' => $stock->id,
                    'incoming_id' => $data['id']
                ));

            }

            return $max_prices;

        }

    }

}




