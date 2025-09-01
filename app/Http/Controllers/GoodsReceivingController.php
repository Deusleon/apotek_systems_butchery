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
use App\Store;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use View;

class GoodsReceivingController extends Controller
{

    public function index()
    {
        $batch_setting = Setting::where('id', 110)->value('value');/*batch number setting*/
        $invoice_setting = Setting::where('id', 115)->value('value');/*invoice setting*/

        /*get default store*/
        $default_store = optional(Auth::user()->store->first())->name;
        $stores = Store::where('name', $default_store)->first();

        if ($stores != null) {
            $default_store_id = $stores->id;
        } else {
            $default_store_id = 1;
        }
        $default_store_name = Store::where('id', $default_store_id)->value('name');

        $back_date = Setting::where('id', 114)->value('value');
        $expire_date = Setting::where('id', 123)->value('value');

        $orders = Order::with(['details', 'details.product', 'supplier'])
            ->where('status', '<=', '3')
            ->orderBy('ordered_at', 'desc')
            ->get();
        $order_details = OrderDetail::all();
        $suppliers = Supplier::all();
        $default_supplier = Supplier::first();
        $item_stocks = GoodsReceiving::all();
        $current_stock = $this->allProductToReceive();
        $price_categories = PriceCategory::all();
        $invoices = Invoice::all();
        $stores = Store::all();

        $selling_prices = DB::table('sales_prices');
        $order_receiving = DB::table('order_details')
            ->join('inv_products', 'inv_products.id', '=', 'order_details.product_id')
            ->select('order_details.id as id', 'name', 'order_details.ordered_qty as quantity', 'unit_price as price', 'vat', 'discount', 'amount')
            ->groupBy('order_details.id');

        return View::make('purchases.goods_receiving.index',
            (compact('orders', ['order_details', 'suppliers', 'default_supplier',
                'order_receiving', 'price_categories','stores', 'default_store_id', 'default_store_name',
                'current_stock', 'item_stocks', 'invoices', 'batch_setting', 'invoice_setting', 'back_date', 'expire_date'])));
    }
      



    public function orderReceiving()
    {
        $batch_setting = Setting::where('id', 110)->value('value');
        $invoice_setting = Setting::where('id', 115)->value('value');

        $default_store = optional(Auth::user()->store->first())->name;
        $store = Store::where('name', $default_store)->first();
        $default_store_id = $store ? $store->id : 1;
        $default_store_name = $store ? $store->name : 'Default Store';

        $back_date = Setting::where('id', 114)->value('value');
        $expire_date = Setting::where('id', 123)->value('value');

        // Get orders with their related details, products, and suppliers
        $orders = Order::with([
            'details' => function ($query) {
                // Select the original ordered_qty and alias it to quantity for consistency in the view
                $query->select('order_details.*', 'order_details.ordered_qty as quantity');
            },
            'details.product',
            'supplier'
        ])->orderBy('id', 'desc')->get();

        // Manually add order_item_id to each detail to ensure it's available in the view
        foreach ($orders as $order) {
            foreach ($order->details as $detail) {
                $detail->order_item_id = $detail->id;
            }
        }

        $suppliers = Supplier::all();
        $price_categories = PriceCategory::all();
        $stores = Store::all();

        return view('purchases.goods_receiving.order_receiving', compact(
            'orders',
            'suppliers',
            'price_categories',
            'stores',
            'default_store_id',
            'default_store_name',
            'batch_setting',
            'invoice_setting',
            'back_date',
            'expire_date'
        ));
    }

    public function allProductToReceive()
    {
        $max_prices = array();

        $products = Product::select('id', 'name', 'brand', 'pack_size', 'sales_uom')
            ->where('status', '=', 1)
            ->groupBy('id', 'name', 'brand', 'pack_size', 'sales_uom')
            ->get();

        foreach ($products as $product) {

            $data = CurrentStock::where('product_id', $product->id)
                ->orderBy('id', 'desc')
                ->first();

            if ($data != null) {
                array_push($max_prices, array(
                    'product_name' => $data->product['name'],
                    'brand'=>$product->brand,
                    'pack_size'=>$product->pack_size,
                    'sales_uom' => $product->sales_uom,
                    'unit_cost' => $data->unit_cost,
                    'selling_price' => $data->price,
                    'id' => $data->id,
                    'product_id' => $data->product_id
                ));
            } else {
                array_push($max_prices, array(
                    'product_name' => $product->name,
                    'brand'=>$product->brand,
                    'pack_size'=>$product->pack_size,
                    'sales_uom' => $product->sales_uom,
                    'unit_cost' => null,
                    'selling_price' => null,
                    'id' => null,
                    'product_id' => $product->id
                ));
            }

        }
        $sort_column = array_column($max_prices, 'product_name');
        array_multisort($sort_column, SORT_ASC, $max_prices);

        return $max_prices;

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

            } else {
                $products = PriceList::where('price_category_id', $request->price_category)
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

        /*get default store*/
        $default_store = optional(Auth::user()->store->first())->name;
        $stores = Store::where('name', $default_store)->first();

        if ($stores != null) {
            $default_store_id = $stores->id;
        } else {
            $default_store_id = 1;
        }

        if ($request->ajax()) {
            // dd($request);
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
                if ($request->expire_date != null) {
                    $update_stock->expiry_date = date('Y-m-d', strtotime($request->expire_date));
                } else {
                    $update_stock->expiry_date = null;
                }
                $update_stock->quantity = $cart['quantity'];
                $update_stock->unit_cost = str_replace(',', '', $request->unit_cost);
                $update_stock->store_id = $default_store_id;
                $update_stock->save();
                $overal_stock_id = $update_stock->id;
            } else {
                $stock = new CurrentStock;
                $stock->product_id = $cart['id'];
                $stock->batch_number = $request->batch_number;
                if ($request->expire_date != null) {
                    $stock->expiry_date = date('Y-m-d', strtotime($request->expire_date));
                } else {
                    $stock->expiry_date = null;
                }
                $stock->quantity = $cart['quantity'];
                $stock->unit_cost = str_replace(',', '', $request->unit_cost);
                $stock->store_id = $default_store_id;
                $stock->save();
                $overal_stock_id = $stock->id;
            }


            /*insert into stock tracking*/
            $stock_tracking = new StockTracking;
            $stock_tracking->stock_id = $overal_stock_id;
            $stock_tracking->product_id = $cart['id'];
            $stock_tracking->quantity = $cart['quantity'];
            $stock_tracking->store_id = $default_store_id;
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
            if ($request->expire_date != null) {
                $incoming_stock->expire_date = date('Y-m-d', strtotime($request->expire_date));
            } else {
                $incoming_stock->expire_date = null;
            }
            $incoming_stock->quantity = $cart['quantity'];
            $incoming_stock->unit_cost = str_replace(',', '', $request->unit_cost);
            $incoming_stock->total_cost = $total_buyprice;
            $incoming_stock->total_sell = $total_sellprice;
            $incoming_stock->item_profit = $profit;
            $incoming_stock->created_by = Auth::user()->id;
            $incoming_stock->sell_price = str_replace(',', '', $request->sell_price);
            if ($request->purchase_date != null) {
                $incoming_stock->created_at = date('Y-m-d', strtotime($request->purchase_date));
            } else {
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
    // Fetch settings
    $batch_setting = Setting::where('id', 110)->value('value'); // YES/NO
    $expire_date   = Setting::where('id', 123)->value('value'); // YES/NO

    DB::beginTransaction();

    try {
        // Basic validation for all orders
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'supplier_id' => 'required|exists:inv_suppliers,id',
            'items' => 'required|array',
            'items.*.purchase_order_detail_id' => 'required|exists:order_details,id',
            'items.*.product_id' => 'required|exists:inv_products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
        ]);

        // Conditional validation based on settings
        if ($batch_setting === 'YES') {
            $request->validate([
                'items.*.batch_number' => 'required|string|max:255'
            ]);
        }

        if ($expire_date === 'YES') {
            $request->validate([
                'items.*.expiry_date' => 'required|date'
            ]);
        }

        $order = Order::with('details')->findOrFail($validated['order_id']);

        if ($order->status == '3') {
            return redirect()->back()->with('error', 'This order has already been fully received.');
        }

        $default_store_id = optional(Auth::user()->store->first())->id ?? 1;

        foreach ($validated['items'] as $itemData) {
            $received_qty = (int)$itemData['quantity'];
            if ($received_qty <= 0) continue;

            $order_detail = OrderDetail::findOrFail($itemData['purchase_order_detail_id']);

            $current_received = (int)($order_detail->received_qty ?? 0);
            $remaining_qty = (int)$order_detail->ordered_qty - $current_received;

            if ($received_qty > $remaining_qty) {
                throw new \Exception("Cannot receive more than the remaining quantity for product ID {$itemData['product_id']}.");
            }

            $order_detail->increment('received_qty', $received_qty);

            // Determine batch number and expiry date based on settings
            $batch_number = ($batch_setting === 'YES') ? $itemData['batch_number'] : null;
            $expiry_date_value = ($expire_date === 'YES' && !empty($itemData['expiry_date']))
                                 ? date('Y-m-d', strtotime($itemData['expiry_date']))
                                 : null;

            // Save goods receiving
            $goods_receiving = new GoodsReceiving();
            $goods_receiving->product_id   = $itemData['product_id'];
            $goods_receiving->supplier_id  = $validated['supplier_id'];
            $goods_receiving->quantity     = $received_qty;
            $goods_receiving->unit_cost    = $itemData['cost_price'];
            $goods_receiving->total_cost   = $received_qty * $itemData['cost_price'];
            $goods_receiving->batch_number = $batch_number;
            $goods_receiving->expire_date  = $expiry_date_value;
            $goods_receiving->created_by   = Auth::id();
            $goods_receiving->save();

            // Update or create stock
            $stock = CurrentStock::firstOrNew([
                'product_id'   => $itemData['product_id'],
                'batch_number' => $batch_number,
                'store_id'     => $default_store_id,
            ]);
            $stock->quantity   += $received_qty;
            $stock->unit_cost   = $itemData['cost_price'];
            $stock->expiry_date = $expiry_date_value;
            $stock->save();

            // Track stock movement
            StockTracking::create([
                'stock_id'   => $stock->id,
                'product_id' => $itemData['product_id'],
                'quantity'   => $received_qty,
                'store_id'   => $default_store_id,
                'updated_by' => Auth::id(),
                'out_mode'   => 'Purchase Order Receiving',
                'movement'   => 'IN',
            ]);
        }

        // Recompute order status
        $total_ordered  = OrderDetail::where('order_id', $order->id)->sum('ordered_qty');
        $total_received = OrderDetail::where('order_id', $order->id)->sum('received_qty');

        if ($total_received == 0) {
            $order->status = '1'; // Pending
        } elseif ($total_received < $total_ordered) {
            $order->status = '2'; // Partially received
        } else {
            $order->status = '3'; // Completed
        }
        $order->save();

        DB::commit();

        return redirect()->back()->with('alert-success', 'Order received successfully!');

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Order Receiving Error: '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
        return redirect()->back()->with('error', 'An unexpected error occurred while processing the order.');
    }
}




    public function filterInvoice(Request $request)
    {
        if ($request->ajax()) {
            dd($request);
            $invoices = Invoice::select('invoice_no', 'id')
                ->where('supplier_id', $request->supplier_id)
                ->get();

            return json_decode($invoices, true);
        }
    }

    public function filterPrice(Request $request)
    {

        if ($request->ajax()) {
            $invoices = GoodsReceiving::select('sell_price as unit_cost')
                ->where('inv_incoming_stock.supplier_id', $request->supplier_id)
                ->where('product_id', $request->product_id)
                ->orderby('inv_incoming_stock.id', 'desc')
                ->first();

            if ($invoices == null) {
                $invoices = GoodsReceiving::select('sell_price as unit_cost')
                    ->where('product_id', $request->product_id)
                    ->orderby('inv_incoming_stock.id', 'desc')
                    ->first();
            }

            return json_decode($invoices, true);
        }
    }

    public function purchaseOrderList(Request $request)
    {
        $columns = array(
            0 => 'order_number',
            1 => 'inv_suppliers.name',
            2 => 'ordered_at',
            3 => 'total_amount',
            4 => 'status',
            5 => 'status',
            6 => 'id'
        );

        $from = $request->range[0];
        $to = $request->range[1];


        $totalData = Order::where('status', '<=', '3')
            ->whereBetween(DB::raw('date(ordered_at)'),
                [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])
            ->orderby('id', 'DESC')
            ->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $orders = Order::where('status', '<=', '3')
                ->select('orders.id', 'order_number', 'supplier_id', 'ordered_by', 'ordered_at', 'received_by', 'received_at'
                    , 'Comment', 'status', 'total_vat', 'total_amount')
                ->join('inv_suppliers', 'inv_suppliers.id', '=', 'orders.supplier_id')
                ->whereBetween(DB::raw('date(ordered_at)'),
                    [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $orders = Order::where('status', '<=', '3')
                ->select('orders.id', 'order_number', 'supplier_id', 'ordered_by', 'ordered_at', 'received_by', 'received_at'
                    , 'Comment', 'status', 'total_vat', 'total_amount')
                ->join('inv_suppliers', 'inv_suppliers.id', '=', 'orders.supplier_id')
                ->whereBetween(DB::raw('date(ordered_at)'),
                    [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])
                ->where('order_number', 'LIKE', "%{$search}%")
                ->orwhere('total_amount', 'LIKE', "%{$search}%")
                ->orWhere(DB::raw('inv_suppliers.name'), 'LIKE', "%{$search}%")
                ->orWhere(DB::raw('date(ordered_at)'), 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = Order::where('status', '<=', '3')
                ->select('orders.id', 'order_number', 'supplier_id', 'ordered_by', 'ordered_at', 'received_by', 'received_at'
                    , 'Comment', 'status', 'total_vat', 'total_amount')
                ->join('inv_suppliers', 'inv_suppliers.id', '=', 'orders.supplier_id')
                ->whereBetween(DB::raw('date(ordered_at)'),
                    [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])
                ->where('order_number', 'LIKE', "%{$search}%")
                ->orwhere('total_amount', 'LIKE', "%{$search}%")
                ->orWhere(DB::raw('inv_suppliers.name'), 'LIKE', "%{$search}%")
                ->orWhere(DB::raw('date(ordered_at)'), 'LIKE', "%{$search}%")
                ->count();
        }

        $data = array();
        if (!empty($orders)) {
            foreach ($orders as $order) {

                $order->supplier;
                $order->details;
                $order->orderDetail;

            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $orders
        );

        echo json_encode($json_data);
    }

    public function getInvoiceItemPrice(Request $request)

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

                //Get Buying Price
                $buying_price = GoodsReceiving::where('product_id', $request->product_id)->where('supplier_id', $supplier_id)->orderBy('id', 'DESC')->value("unit_cost");

                if($buying_price = "0.00") {

                    $buying_price = GoodsReceiving::where('product_id', $request->product_id)->orderBy('id', 'DESC')->value("unit_cost");
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

            } else {
                $products = PriceList::where('price_category_id', $request->price_category)
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
                    'brand' => $currentStock['product']['brand'],
                    'unit_cost' => $buying_price,
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

    public function invoiceitemReceive(Request $request)
    {

        if ($request->ajax()) {

            $cart = json_decode($request->cart, true);
            // dd($request);

            $default_store_id = $request->store;

            foreach($cart as $single_item){
                // dd($single_item);
                $quantity = str_replace(',', '', $single_item['quantity']);
                $item_product_id = str_replace(',', '', $single_item['id']);
                $unit_sell_price = str_replace(',', '', $single_item['selling_price']);
                // dd($unit_sell_price);
                $unit_buy_price = str_replace(',', '', $single_item['buying_price']);
                $total_buyprice = $quantity * $unit_buy_price;
                $total_sellprice = $quantity * $unit_sell_price;
                $profit = $total_sellprice - $total_buyprice;

                /*check if there exists 0 qty of that product*/
                $current_stock = CurrentStock::where('product_id', $item_product_id)
                ->where('quantity', '=', 0)
                ->get();

                if (!($current_stock->isEmpty())) {
                    //update
                    $update_stock = CurrentStock::find($current_stock->first()->id);
                    $update_stock->batch_number = $request->batch_number;

                    if ($request->expire_date == "YES") {
                        if ($single_item['expire_date'] != null) {
                        $update_stock->expiry_date = date('Y-m-d', strtotime($single_item['expire_date']));
                        } else {
                            $update_stock->expiry_date = null;
                        }
                    } else {
                        $update_stock->expiry_date = null;
                    }
                    $update_stock->quantity = $quantity;
                    $update_stock->unit_cost = str_replace(',', '', $single_item['buying_price']);
                    $update_stock->store_id = $default_store_id;
                    $update_stock->save();
                    $overal_stock_id = $update_stock->id;
                } else {
                    $stock = new CurrentStock;
                    $stock->product_id = $item_product_id;
                    $stock->batch_number = $request->batch_number;
                    if ($request->expire_date == "YES") {

                        if ($single_item['expire_date'] != null) {
                            $stock->expiry_date = date('Y-m-d', strtotime($single_item['expire_date']));
                        } else {
                            $stock->expiry_date = null;
                        }

                    } else {
                        $stock->expiry_date = null;
                    }
                    $stock->quantity = $quantity;
                    $stock->unit_cost = str_replace(',', '', $single_item['buying_price']);
                    $stock->store_id = $default_store_id;
                    $stock->save();
                    $overal_stock_id = $stock->id;
                }

                /*insert into stock tracking*/

                $stock_tracking = new StockTracking;
                $stock_tracking->stock_id = $overal_stock_id;
                $stock_tracking->product_id = $single_item['id'];
                $stock_tracking->quantity = $quantity;
                $stock_tracking->store_id = $default_store_id;
                $stock_tracking->updated_by = Auth::user()->id;
                $stock_tracking->out_mode = 'New Product Purchase';
                $stock_tracking->updated_at = date('Y-m-d');
                $stock_tracking->movement = 'IN';
                $stock_tracking->save();

                // Create Prce
                $price = new PriceList;
                $price->stock_id = $overal_stock_id;
                $price->price = $unit_sell_price;
                $price->price_category_id = $request->invoice_price_category;
                $price->status = 1;
                $price->created_at = date('Y-m-d H:m:s');
                $price->save();


                $incoming_stock = new GoodsReceiving;
                $incoming_stock->product_id = $single_item['id'];
                $incoming_stock->supplier_id = $request->supplier;
                $incoming_stock->invoice_no = $request->invoice_no;
                $incoming_stock->batch_number = $request->batch_number;
                if ($request->expire_date == "YES") {

                    if ($single_item['expire_date'] != null) {
                        $incoming_stock->expire_date = date('Y-m-d', strtotime($single_item['expire_date']));
                    } else {
                        $incoming_stock->expire_date = null;
                    }

                } else {
                    $incoming_stock->expire_date = null;
                }
                $incoming_stock->quantity = $quantity;
                $incoming_stock->unit_cost = str_replace(',', '', $single_item['buying_price']);
                $incoming_stock->total_cost = $total_buyprice;
                $incoming_stock->total_sell = $total_sellprice;
                $incoming_stock->item_profit = $profit;
                $incoming_stock->created_by = Auth::user()->id;
                $incoming_stock->sell_price = $unit_sell_price;
                if ($request->purchase_date != null) {
                    $incoming_stock->created_at = date('Y-m-d', strtotime($request->purchase_date));
                } else {
                    $incoming_stock->created_at = date('Y-m-d');

                }

                $incoming_stock->save();
                // dd($incoming_stock);

            }

            $message = array();
            array_push($message, array(
                'message' => 'success'
            ));
            return $message;

        }

    }

}


