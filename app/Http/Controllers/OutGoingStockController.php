<?php

namespace App\Http\Controllers;

use App\Setting;
use App\StockTracking;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutGoingStockController extends Controller
{


    public function index()
    {
        return view('stock_management.out_going_stock.index');
    }

    public function showOutStock(Request $request)
    {

        if ($request->ajax()) {

            /*get default store*/
            $default_store = Auth::user()->store->name ?? 'Default Store';
            $stores = Store::where('name', $default_store)->first();

            if ($stores != null) {
                $default_store_id = $stores->id;
            } else {
                $default_store_id = 1;
            }

            //return all
            $stock_tracking = StockTracking::whereBetween('updated_at', [date('Y-m-d', strtotime($request->date_from))
                , date('Y-m-d', strtotime($request->date))])
                ->where('store_id', $default_store_id)
                ->where('movement', 'OUT')
                ->get();

            //return product object
            foreach ($stock_tracking as $tracking) {
                $tracking->currentStock->product;
                $tracking->user;
                $tracking->date = date('d-m-Y', strtotime($tracking->updated_at));
            }

            return json_decode($stock_tracking, true);

        }

    }


}
