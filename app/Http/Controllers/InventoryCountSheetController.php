<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\Setting;
use App\Store;
use Illuminate\Support\Facades\DB;
use PDF;

ini_set('max_execution_time', 500);
set_time_limit(500);
ini_set('memory_limit', '512M');

class InventoryCountSheetController extends Controller
{


    public function generateInventoryCountSheetPDF()
    {
        /*get default store*/
        $default_store = Setting::where('id', 122)->value('value');
        $stores = Store::where('name', $default_store)->first();

        if ($stores != null) {
            $default_store_id = $stores->id;
        } else {
            $default_store_id = 1;
        }

        $pharmacy['name'] = Setting::where('id', 100)->value('value');
        $pharmacy['address'] = Setting::where('id', 106)->value('value');
        $pharmacy['logo'] = Setting::where('id', 105)->value('value');
        $pharmacy['phone'] = Setting::where('id', 107)->value('value');

        $data = array();
        $current_stocks = CurrentStock::select(DB::raw('product_id'), 'store_id', 'shelf_number',
            DB::raw('sum(quantity) as quantity_on_hand'))
            ->where('store_id', $default_store_id)
            ->groupby('product_id', 'store_id')
            ->get();

        foreach ($current_stocks as $current_stock) {
            array_push($data, array(
                'store' => $current_stock->store['name'],
                'shelf_no' => $current_stock->shelf_number,
                'product_id' => $current_stock->product['id'],
                'product_name' => $current_stock->product['name'],
                'quantity_on_hand' => $current_stock->quantity_on_hand
            ));
        }

        /*group by store*/
        $grouped_by_store = [];
        foreach ($data as $val) {
            if (array_key_exists('store', $val)) {
                $grouped_by_store[$val['store']][] = $val;
            }
        }

        $data = $grouped_by_store;

//        dd($data);

        if ($data == []) {
            return response()->view('error_pages.pdf_zero_data');
        }

        $pdf = PDF::loadView('stock_management.daily_stock_count.inventory_count_sheet',
            compact('data', 'pharmacy'));
        return $pdf->stream('inventory_count_sheet.pdf');

    }


}
