<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Store;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

ini_set( 'max_execution_time', 500 );
set_time_limit( 500 );
ini_set( 'memory_limit', '512M' );

class InventoryCountSheetController extends Controller
 {
    public function generateInventoryCountSheetPDF()
 {
        /* get default store */
        $default_store = current_store()->name ?? 'Unknown Store';
        $default_store_id = current_store_id();

        // Pharmacy info
        $pharmacy[ 'name' ]    = Setting::where( 'id', 100 )->value( 'value' );
        $pharmacy[ 'address' ] = Setting::where( 'id', 106 )->value( 'value' );
        $pharmacy[ 'logo' ]    = Setting::where( 'id', 105 )->value( 'value' );
        $pharmacy[ 'phone' ]   = Setting::where( 'id', 107 )->value( 'value' );

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
            compact( 'data', 'pharmacy', 'default_store' )
        );
        return $pdf->stream( 'inventory_count_sheet.pdf' );
    }
}
