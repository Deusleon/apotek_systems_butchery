<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Store;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;

ini_set( 'max_execution_time', 500 );
set_time_limit( 500 );
ini_set( 'memory_limit', '512M' );

class InventoryCountSheetController extends Controller
 {
    public function generateInventoryCountSheetPDF(Request $request)
 {
        $showQoH = $request->query('showQoH', 1);

        $showQoH = $showQoH == 1 ? true : false;

        /* get default store */
        $default_store = current_store()->name ?? 'Unknown Store';
        $default_store_id = current_store_id();

        // Pharmacy info
    $pharmacy['name'] = Setting::where('id', 100)->value('value');
    $pharmacy['address'] = Setting::where('id', 106)->value('value');
    $pharmacy['phone'] = Setting::where('id', 107)->value('value');
    $pharmacy['email'] = Setting::where('id', 108)->value('value');
    $pharmacy['website'] = Setting::where('id', 109)->value('value');
    $pharmacy['logo'] = Setting::where('id', 105)->value('value');
    $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');

        // Fetch current stock with product and store info using join
        $current_stocks = DB::table( 'inv_current_stock as cs' )
        ->join( 'inv_products as p', 'cs.product_id', '=', 'p.id' )
        ->join( 'inv_stores as s', 'cs.store_id', '=', 's.id' )
        ->select(
            'cs.product_id',
            'p.name as product_name',
            'p.brand',
            'p.pack_size',
            'p.sales_uom',
            'cs.store_id',
            's.name as store_name',
            'cs.shelf_number',
            DB::raw( 'SUM(cs.quantity) as quantity_on_hand' )
        )
        ->when( !is_all_store(), function ( $q ) use ( $default_store_id ) {
            return $q->where( 'cs.store_id', $default_store_id );
        } )
        ->groupBy( [ 'cs.product_id', 'cs.store_id', 'cs.shelf_number', 'p.name', 'p.brand', 'p.pack_size', 'p.sales_uom', 's.name' ] )
        ->get();

        // Transform to array
        $data = [];
        foreach ( $current_stocks as $stock ) {
            $data[] = [
                'store'            => $stock->store_name,
                'shelf_no'         => $stock->shelf_number,
                'product_id'       => $stock->product_id,
                'product_name'     => $stock->product_name,
                'brand'            => $stock->brand,
                'pack_size'        => $stock->pack_size,
                'sales_uom'        => $stock->sales_uom,
                'quantity_on_hand' => ( float ) $stock->quantity_on_hand,
            ];
        }

        // Group by store
        $data = collect( $data )->groupBy( 'store' )->toArray();

        if ( empty( $data ) ) {
            return response()->view( 'error_pages.pdf_zero_data' );
        }

        // Generate PDF
        $pdf = PDF::loadView(
            'stock_management.daily_stock_count.inventory_count_sheet',
            compact( 'data',  'showQoH', 'pharmacy', 'default_store' )
        );
        return $pdf->stream( 'inventory_count_sheet.pdf' );
    }

    public function generateDailyStockCountPDF($request)
    {

        $data = $this->summation($request->sale_date);
        $new_data = array_values($data);
        // $showQoH = $request->show_qoh ? true : false;
        $showQoH = 'true';

        $pdf = PDF::loadView( 'stock_management.daily_stock_count.daily_stock_count',
        compact( 'data', 'new_data', 'showQoH' ) )
        ->setPaper( 'a4', '' );
        return $pdf->stream( 'daily_stock_count.pdf' );

    }
}
