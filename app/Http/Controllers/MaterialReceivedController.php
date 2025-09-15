<?php

namespace App\Http\Controllers;

use App\GoodsReceiving;
use App\Product;
use App\Setting;
use App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use View;

class MaterialReceivedController extends Controller
{

    public function index(Request $request)
    {
        $suppliers = Supplier::orderby('name', 'ASC')->get();
        $products = Product::all();
        $expire_date = Setting::where('id', 123)->value('value');


        return View::make('purchases.material_received.index',
            (compact('suppliers', 'products', 'expire_date')));

    }

    public function update(Request $request)
    {

        $update_material = GoodsReceiving::find($request->id);

        $quantity = str_replace(',', '', $request->quantity_edit);
        $unit_buy_price = floatval(preg_replace('/[^\d.]/', '', $request->price_edit));
        $total_buyprice = $quantity * $unit_buy_price;
        $total_sellprice = $quantity * $update_material->sell_price;
        $profit = $total_sellprice - $total_buyprice;

        if ($request->expire_date_edit) {
            $update_material->expire_date = date('Y-m-d', strtotime($request->expire_date_edit));
        } else {
            $update_material->expire_date = null;
        }
        $update_material->quantity = $quantity;
        $update_material->unit_cost = $unit_buy_price;
        $update_material->total_cost = $total_buyprice;
        $update_material->total_sell = $total_sellprice;
        $update_material->item_profit = $profit;
        $update_material->supplier_id = $request->supplier_id_edit;
        $update_material->created_by = Auth::user()->id;
        $originalTime = $update_material->created_at->format('H:i:s'); // ← preserve original time
        $newDate = date('Y-m-d', strtotime($request->receive_date_edit));
        $update_material->created_at = $newDate . ' ' . $originalTime;     // ← merge new date with original time


        $update_material->save();

        session()->flash("alert-success", "Material updated successfully!");
        return back();

    }

    public function destroy(Request $request)
    {

        GoodsReceiving::destroy($request->id);
        session()->flash("alert-danger", "Material deleted successfully!");
        return back();

    }

    public function getMaterialsReceived(Request $request)
{
    $columns = array(
        0 => 'inv_products.name',
        1 => 'inv_products.name',
        2 => 'quantity',
        3 => 'unit_cost',
        4 => 'expire_date',
        5 => 'total_cost',
        6 => 'inv_incoming_stock.created_at',
        7 => 'users.name',
        8 => 'inv_products.name'
    );

    $from = date('Y-m-d', strtotime($request->date[0]));
    $to = date('Y-m-d', strtotime($request->date[1]));

    if ($request->supplier_id) {
        $totalData = GoodsReceiving::select(
                'inv_incoming_stock.id',
                'product_id', 'quantity', 'unit_cost', 'total_cost',
                'expire_date', 'inv_incoming_stock.created_at',
                'supplier_id', 'created_by'
            )
            ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
            ->join('users', 'users.id', '=', 'inv_incoming_stock.created_by')
            ->whereBetween(DB::raw('date(inv_incoming_stock.created_at)'), [$from, $to])
            ->where('supplier_id', $request->supplier_id)
            ->orderby('created_at', 'DESC')
            ->count();
    } else {
        $totalData = GoodsReceiving::select(
                'inv_incoming_stock.id',
                'product_id', 'quantity', 'unit_cost', 'total_cost',
                'expire_date', 'inv_incoming_stock.created_at',
                'supplier_id', 'created_by'
            )
            ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
            ->join('users', 'users.id', '=', 'inv_incoming_stock.created_by')
            ->whereBetween(DB::raw('date(inv_incoming_stock.created_at)'), [$from, $to])
            ->orderby('created_at', 'DESC')
            ->orderby('id', 'DESC')
            ->count();
    }

    $totalFiltered = $totalData;

    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');

    if (empty($request->input('search.value'))) {
        if ($request->supplier_id) {
            $material_received = GoodsReceiving::select(
                    'inv_incoming_stock.id',
                    'product_id', 'quantity', 'unit_cost', 'total_cost',
                    'expire_date', 'inv_incoming_stock.created_at',
                    'supplier_id', 'created_by'
                )
                ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
                ->join('users', 'users.id', '=', 'inv_incoming_stock.created_by')
                ->whereBetween(DB::raw('date(inv_incoming_stock.created_at)'), [$from, $to])
                ->where('supplier_id', $request->supplier_id)
                ->offset($start)
                ->limit($limit)
                ->orderby('created_at', 'DESC')
                ->orderby('id', 'DESC')
                ->get();
        } else {
            $material_received = GoodsReceiving::select(
                    'inv_incoming_stock.id',
                    'product_id', 'quantity', 'unit_cost', 'total_cost',
                    'expire_date', 'inv_incoming_stock.created_at',
                    'supplier_id', 'created_by'
                )
                ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
                ->join('users', 'users.id', '=', 'inv_incoming_stock.created_by')
                ->whereBetween(DB::raw('date(inv_incoming_stock.created_at)'), [$from, $to])
                ->offset($start)
                ->limit($limit)
                ->orderby('created_at', 'DESC')
                ->get();
        }
    } else {
        $search = $request->input('search.value');

        if ($request->supplier_id) {
            $material_received = GoodsReceiving::select(
                    'inv_incoming_stock.id',
                    'product_id', 'quantity', 'unit_cost', 'total_cost',
                    'expire_date', 'inv_incoming_stock.created_at',
                    'supplier_id', 'created_by'
                )
                ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
                ->join('users', 'users.id', '=', 'inv_incoming_stock.created_by')
                ->whereBetween(DB::raw('date(inv_incoming_stock.created_at)'), [$from, $to])
                ->where('inv_products.name', 'LIKE', "%{$search}%")
                ->orwhere('quantity', 'LIKE', "%{$search}%")
                ->orwhere('unit_cost', 'LIKE', "%{$search}%")
                ->orwhere('expire_date', 'LIKE', "%{$search}%")
                ->orwhere('total_cost', 'LIKE', "%{$search}%")
                ->orwhere(DB::raw('date(inv_incoming_stock.created_at)'), 'LIKE', "%{$search}%")
                ->orwhere('users.name', 'LIKE', "%{$search}%")
                ->where('supplier_id', $request->supplier_id)
                ->offset($start)
                ->limit($limit)
                ->orderby('created_at', 'DESC')
                ->get();

            $totalFiltered = GoodsReceiving::select('inv_incoming_stock.id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
                ->join('users', 'users.id', '=', 'inv_incoming_stock.created_by')
                ->whereBetween(DB::raw('date(inv_incoming_stock.created_at)'), [$from, $to])
                ->where('inv_products.name', 'LIKE', "%{$search}%")
                ->orwhere('quantity', 'LIKE', "%{$search}%")
                ->orwhere('unit_cost', 'LIKE', "%{$search}%")
                ->orwhere('expire_date', 'LIKE', "%{$search}%")
                ->orwhere('total_cost', 'LIKE', "%{$search}%")
                ->orwhere(DB::raw('date(inv_incoming_stock.created_at)'), 'LIKE', "%{$search}%")
                ->orwhere('users.name', 'LIKE', "%{$search}%")
                ->where('supplier_id', $request->supplier_id)
                ->count();
        } else {
            $material_received = GoodsReceiving::select(
                    'inv_incoming_stock.id',
                    'product_id', 'quantity', 'unit_cost', 'total_cost',
                    'expire_date', 'inv_incoming_stock.created_at',
                    'supplier_id', 'created_by'
                )
                ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
                ->join('users', 'users.id', '=', 'inv_incoming_stock.created_by')
                ->whereBetween(DB::raw('date(inv_incoming_stock.created_at)'), [$from, $to])
                ->where('inv_products.name', 'LIKE', "%{$search}%")
                ->orwhere('quantity', 'LIKE', "%{$search}%")
                ->orwhere('unit_cost', 'LIKE', "%{$search}%")
                ->orwhere('expire_date', 'LIKE', "%{$search}%")
                ->orwhere('total_cost', 'LIKE', "%{$search}%")
                ->orwhere(DB::raw('date(inv_incoming_stock.created_at)'), 'LIKE', "%{$search}%")
                ->orwhere('users.name', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderby('created_at', 'DESC')
                ->orderby('id', 'DESC')
                ->get();

            $totalFiltered = GoodsReceiving::select('inv_incoming_stock.id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
                ->join('users', 'users.id', '=', 'inv_incoming_stock.created_by')
                ->whereBetween(DB::raw('date(inv_incoming_stock.created_at)'), [$from, $to])
                ->where('inv_products.name', 'LIKE', "%{$search}%")
                ->orwhere('quantity', 'LIKE', "%{$search}%")
                ->orwhere('unit_cost', 'LIKE', "%{$search}%")
                ->orwhere('expire_date', 'LIKE', "%{$search}%")
                ->orwhere('total_cost', 'LIKE', "%{$search}%")
                ->orwhere(DB::raw('date(inv_incoming_stock.created_at)'), 'LIKE', "%{$search}%")
                ->orwhere('users.name', 'LIKE', "%{$search}%")
                ->count();
        }
    }

    if (!empty($material_received)) {
        foreach ($material_received as $value) {
            $value->product;
            $value->supplier;
            $value->user;

            // --- Calculate remaining quantity (existing logic preserved)
            $orderDetailQuery = DB::table('order_details')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->where('order_details.product_id', $value->product_id);

            if ($value->supplier_id) {
                $orderDetailQuery->where('orders.supplier_id', $value->supplier_id);
            }

            $orderDetail = $orderDetailQuery
                ->select('order_details.ordered_qty', 'order_details.received_quantity')
                ->orderBy('orders.ordered_at', 'desc')
                ->first();

            if ($orderDetail) {
                $value->ordered_qty = $orderDetail->ordered_qty;
                $value->total_received_qty = $orderDetail->received_quantity;
                $value->remaining_qty = $orderDetail->ordered_qty - $orderDetail->received_quantity;
            } else {
                $value->ordered_qty = $value->quantity;
                $value->total_received_qty = $value->quantity;
                $value->remaining_qty = 0;
            }

            // --- NEW FALLBACK: Avoid showing 0.00 for price/amount
            if (empty($value->unit_cost) || floatval($value->unit_cost) == 0) {
                // Try latest current stock
                $lastCurrent = DB::table('inv_current_stock')
                    ->where('product_id', $value->product_id)
                    ->orderBy('id', 'desc')
                    ->first(['unit_cost']);

                if ($lastCurrent && floatval($lastCurrent->unit_cost) != 0) {
                    $value->unit_cost = $lastCurrent->unit_cost;
                } else {
                    // Try latest incoming non-zero
                    $lastIncoming = GoodsReceiving::where('product_id', $value->product_id)
                        ->where('unit_cost', '>', 0)
                        ->orderBy('id', 'desc')
                        ->first(['unit_cost', 'total_cost']);

                    if ($lastIncoming) {
                        $value->unit_cost = $lastIncoming->unit_cost;
                        if (empty($value->total_cost) || floatval($value->total_cost) == 0) {
                            $value->total_cost = floatval($lastIncoming->unit_cost) * floatval($value->quantity);
                        }
                    } else {
                        // Fallback to last order_details price
                        $orderDet = DB::table('order_details')
                            ->join('orders', 'order_details.order_id', '=', 'orders.id')
                            ->where('order_details.product_id', $value->product_id)
                            ->when($value->supplier_id, function ($q) use ($value) {
                                $q->where('orders.supplier_id', $value->supplier_id);
                            })
                            ->orderBy('orders.ordered_at', 'desc')
                            ->select('order_details.unit_price', 'order_details.ordered_qty')
                            ->first();

                        if ($orderDet && floatval($orderDet->unit_price) != 0) {
                            $value->unit_cost = $orderDet->unit_price;
                            $value->total_cost = floatval($orderDet->unit_price) * floatval($value->quantity);
                        }
                    }
                }
            }

            // If unit_cost is present but total_cost missing, compute it
            if (!empty($value->unit_cost) && (empty($value->total_cost) || floatval($value->total_cost) == 0)) {
                $value->total_cost = floatval($value->unit_cost) * floatval($value->quantity);
            }
        }
    }

    $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $material_received
    );

    echo json_encode($json_data);
}

}
