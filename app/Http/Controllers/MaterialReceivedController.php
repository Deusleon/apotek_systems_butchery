<?php

namespace App\Http\Controllers;

use App\GoodsReceiving;
use App\Product;
use App\Supplier;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use View;

class MaterialReceivedController extends Controller
{

    public function index(Request $request)
    {
        $suppliers = Supplier::orderby('name', 'ASC')->get();
        $products = Product::all();

        return View::make('purchases.material_received.index', (compact('suppliers', 'products')));

    }

    public function update(Request $request)
    {

        $update_material = GoodsReceiving::find($request->id);

        $quantity = str_replace(',', '', $request->quantity_edit);
        $unit_buy_price = floatval(preg_replace('/[^\d.]/', '', $request->price_edit));
        $total_buyprice = $quantity * $unit_buy_price;
        $total_sellprice = $quantity * $update_material->sell_price;
        $profit = $total_sellprice - $total_buyprice;

        $update_material->expire_date = date('Y-m-d', strtotime($request->expire_date_edit));
        $update_material->quantity = $quantity;
        $update_material->unit_cost = $unit_buy_price;
        $update_material->total_cost = $total_buyprice;
        $update_material->total_sell = $total_sellprice;
        $update_material->item_profit = $profit;
        $update_material->created_by = Auth::user()->id;
        $update_material->created_at = date('Y-m-d', strtotime($request->receive_date_edit));

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
        $from = $request->date[0];
        $to = $request->date[1];
        $total_bp = 0;
        $total_sp = 0;
        $total_pf = 0;
        $data = array();
        $material_received = GoodsReceiving::where(DB::Raw("DATE_FORMAT(created_at,'%m/%d/%Y')"), '>=', $from)
            ->where(DB::Raw("DATE_FORMAT(created_at,'%m/%d/%Y')"), '<=', $to)
            ->orderby('id','DESC')
            ->get();

        if ($request->supplier_id) {
            $material_received = GoodsReceiving::where(DB::Raw("DATE_FORMAT(created_at,'%m/%d/%Y')"), '>=', $from)
                ->where(DB::Raw("DATE_FORMAT(created_at,'%m/%d/%Y')"), '<=', $to)
                ->where('supplier_id', $request->supplier_id)
                ->orderby('id','DESC')
                ->get();
        }
        if ($request->product_id) {
            $material_received = GoodsReceiving::where(DB::Raw("DATE_FORMAT(created_at,'%m/%d/%Y')"), '>=', $from)
                ->where(DB::Raw("DATE_FORMAT(created_at,'%m/%d/%Y')"), '<=', $to)
                ->where('product_id', $request->product_id)
                ->orderby('id','DESC')
                ->get();
        }
        if (($request->product_id) && ($request->supplier_id)) {
            $material_received = GoodsReceiving::where(DB::Raw("DATE_FORMAT(created_at,'%m/%d/%Y')"), '>=', $from)
                ->where(DB::Raw("DATE_FORMAT(created_at,'%m/%d/%Y')"), '<=', $to)
                ->where('product_id', $request->product_id)
                ->where('supplier_id', $request->supplier_id)
                ->orderby('id','DESC')
                ->get();
        }

        foreach ($material_received as $material) {
            $total_bp = $total_bp + $material->total_cost;
            $total_sp = $total_sp + $material->sell_price;
            $total_pf = $total_pf + $material->item_profit;
        }

        foreach ($material_received as $value) {
            $value->product;
            $value->supplier;
        }

        array_push($data, array(
            $material_received, $total_bp, $total_sp, $total_pf
        ));

//        $data = json_decode($material_received, true);
        return $data;
    }
}
