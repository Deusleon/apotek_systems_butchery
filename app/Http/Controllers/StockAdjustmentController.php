<?php

namespace App\Http\Controllers;

use App\AdjustmentReason;
use App\CurrentStock;
use App\Product;
use App\Setting;
use App\StockAdjustment;
use App\StockTracking;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{

    public function index()
    {
        $stock_adjustments = StockAdjustment::orderBy('id', 'ASC')->get();
        $products = Product::all();
        $adjustment_reason = AdjustmentReason::all('reason');

        return view('stock_management.stock_adjustment.index')->with([
            'adjustments' => $stock_adjustments,
            'products' => $products,
            'reasons' => $adjustment_reason
        ]);
    }

    public function store(Request $request)
    {
        /*get default store*/
        $default_store = Setting::where('id', 122)->value('value');
        $stores = Store::where('name', $default_store)->first();

        if ($stores != null) {
            $default_store_id = $stores->id;
        } else {
            $default_store_id = 1;
        }

        $stock_adjustment = new StockAdjustment;
        $stock_adjustment->stock_id = $request->id;
        $stock_adjustment->quantity = $request->quantity;
        $stock_adjustment->type = $request->type;
        $stock_adjustment->reason = $request->reason;
        $stock_adjustment->description = $request->description;
        $stock_adjustment->created_by = Auth::user()->id;

        $sub = 0;
        $quantity = $request->quantity;
        $current_stock = CurrentStock::all('id', 'product_id', 'quantity', 'store_id');

        if ($request->type == "Negative") {

            foreach ($current_stock as $item) {

                if ($item->product_id == $request->product_id && $item->id == $request->id) {
                    $match_field = ['id' => $item->id, 'product_id' => $request->product_id];
                    $sub = $item->quantity - $quantity;
                    $deduct = CurrentStock::where($match_field)->first();
                    $deduct->quantity = $sub;
                    $deduct->save();
                }
            }

            /*save in stock tracking as IN*/
            $stock_tracking = new StockTracking;
            $stock_tracking->stock_id = $request->id;
            $stock_tracking->product_id = $request->product_id;
            $stock_tracking->quantity = $request->quantity;
            $stock_tracking->store_id = $default_store_id;
            $stock_tracking->updated_by = Auth::user()->id;
            $stock_tracking->out_mode = 'Stock Adjustment';
            $stock_tracking->updated_at = date('Y-m-d');
            $stock_tracking->movement = 'OUT';

            $stock_tracking->save();

            $stock_adjustment->save();

            session()->flash("alert-success", "Stock Adjusted successfully!");
            return redirect()->route('current-stock.index');
        } else {
            //positive
            foreach ($current_stock as $item) {

                if ($item->product_id == $request->product_id && $item->id == $request->id) {
                    $match_field = ['id' => $item->id, 'product_id' => $request->product_id];
                    $sub = $item->quantity + $quantity;
                    $deduct = CurrentStock::where($match_field)->first();
                    $deduct->quantity = $sub;
                    $deduct->save();
                }

            }

            /*save in stock tracking as IN*/
            $stock_tracking = new StockTracking;
            $stock_tracking->stock_id = $request->id;
            $stock_tracking->product_id = $request->product_id;
            $stock_tracking->quantity = $request->quantity;
            $stock_tracking->store_id = $default_store_id;
            $stock_tracking->updated_by = Auth::user()->id;
            $stock_tracking->out_mode = 'Stock Adjustment';
            $stock_tracking->updated_at = date('Y-m-d');
            $stock_tracking->movement = 'IN';

            $stock_tracking->save();
            $stock_adjustment->save();
            session()->flash("alert-success", "Stock Adjusted successfully!");
            return redirect()->route('current-stock.index');

        }

    }

    public function update(Request $request)
    {

    }

    public function destroy(Request $request)
    {

    }

    public function allAdjustments(Request $request)
    {

        $columns = array(
            0 => 'stock_id',
            1 => 'type',
            2 => 'quantity',
            3 => 'created_at',
            4 => 'reason',
            5 => 'stock_id',
        );

        $from = $request->from_date;
        $to = $request->to_date;

        $totalData = StockAdjustment::whereBetween(DB::raw('date(created_at)'),
            [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])
            ->get()
            ->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $adjustments = StockAdjustment::whereBetween(DB::raw('date(created_at)'),
                [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $adjustments = StockAdjustment::where('type', 'LIKE', "%{$search}%")
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'inv_stock_adjustments.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->orWhere('stock_id', 'LIKE', "%{$search}%")
                ->orWhere('reason', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->whereBetween(DB::raw('date(inv_stock_adjustments.created_at)'),
                    [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = StockAdjustment::where('type', 'LIKE', "%{$search}%")
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'inv_stock_adjustments.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->orWhere('stock_id', 'LIKE', "%{$search}%")
                ->orWhere('reason', 'LIKE', "%{$search}%")
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->whereBetween(DB::raw('date(inv_stock_adjustments.created_at)'),
                    [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])
                ->count();
        }

        $data = array();
        if (!empty($adjustments)) {
            foreach ($adjustments as $adjustment) {

                $nestedData['name'] = $adjustment->currentStock['product']['name'];
                $nestedData['quantity_adjusted'] = $adjustment->quantity;
                $nestedData['date'] = date('d-m-Y', strtotime($adjustment->created_at));
                $nestedData['type'] = $adjustment->type;
                $nestedData['reason'] = $adjustment->reason;
                $nestedData['description'] = $adjustment->description;

                $data[] = $nestedData;

            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);

    }

}
