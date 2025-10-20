<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Support\Facades\DB;

class StockDetailsController extends Controller
{
    public function stockDetails()
    {
        if (!Auth()->user()->checkPermission('View Current Stock')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $expireSettings = Setting::where('id', 123)->value('value');
        $expireEnabled = $expireSettings === 'YES';

        $detailed = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->join('inv_categories','inv_products.category_id','=','inv_categories.id')
            ->select('inv_current_stock.id', 'inv_current_stock.product_id','inv_products.name', 'inv_products.sales_uom', 'inv_current_stock.unit_cost',
                'inv_products.brand', 'inv_products.pack_size', 'inv_categories.name as cat_name',
                'inv_current_stock.quantity',
                'inv_current_stock.batch_number',
                'inv_current_stock.expiry_date')
            ->when(!is_all_store(), function ($query) use ($store_id) {
                return $query->where('inv_current_stock.store_id', $store_id);
            })
            ->where('inv_current_stock.quantity','>',0)
            ->get();

        return view('stock_management.stock_details.index', compact('detailed', 'expireEnabled'));
    }
}
