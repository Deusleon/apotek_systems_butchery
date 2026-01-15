<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Production;
use App\Setting;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class ProductionReportController extends Controller
 {
    public function index()
 {
        // Get selling prices for meat types from database
        $meatPrices = $this->getMeatPrices();
        
        // Get stores for distribution report filter
        $stores = \App\Store::where('id', '>', 1)->orderBy('name')->get();
        
        return view( 'production_reports.index', compact('meatPrices', 'stores') );
    }

    public function filter( Request $request )
 {
        $date_range = explode( '-', $request->date_range );
        $from = trim( $date_range[ 0 ] );
        $to = trim( $date_range[ 1 ] );
        $type = $request->price_type;
        $enable_discount = Setting::where( 'id', 111 )->value( 'value' );
        $pharmacy[ 'name' ] = Setting::where( 'id', 100 )->value( 'value' );
        $pharmacy[ 'logo' ] = Setting::where( 'id', 105 )->value( 'value' );
        $pharmacy[ 'address' ] = Setting::where( 'id', 106 )->value( 'value' );
        $pharmacy[ 'email' ] = Setting::where( 'id', 108 )->value( 'value' );
        $pharmacy[ 'website' ] = Setting::where( 'id', 109 )->value( 'value' );
        $pharmacy[ 'phone' ] = Setting::where( 'id', 107 )->value( 'value' );
        $pharmacy[ 'tin_number' ] = Setting::where( 'id', 102 )->value( 'value' );
        $pharmacy[ 'from_date' ] = date( 'Y-m-d', strtotime( $from ) );
        $pharmacy[ 'to_date' ] = date( 'Y-m-d', strtotime( $to ) );

        $data = $this->getProductions( $from, $to );
        if ($data->isEmpty()) {
            return response()->view('error_pages.pdf_zero_data');
        }

        // Get meat prices from database automatically
        $meatPrices = $this->getMeatPrices();
        $prices = [
            'meat' => $meatPrices['meat'] ?? 0,
            'steak' => $meatPrices['steak'] ?? 0,
            'beef_fillet' => $meatPrices['beef_fillet'] ?? 0,
            'beef_liver' => $meatPrices['beef_liver'] ?? 0,
            'tripe' => $meatPrices['tripe'] ?? 0,
        ];
        
        $pdf = PDF::loadView( 'production_reports.report_pdf',
        compact( 'data', 'pharmacy', 'enable_discount', 'prices' ) )
        ->setPaper( 'a4', 'landscape' );
        return $pdf->stream( 'Production_Report.pdf' );
    }

    private function getProductions( $from, $to )
 {
        $productions = Production::whereBetween( 'production_date', [ $from, $to ] )
        ->orderBy( 'production_date', 'asc' )
        ->get();

        return $productions;
    }

    /**
     * Get selling prices for meat types from the database
     * Maps product names to meat type keys
     */
    private function getMeatPrices()
    {
        // Map database product names to meat type keys
        $productMapping = [
            'Nyama' => 'meat',      // Swahili for Meat
            'Meat' => 'meat',
            'Steak' => 'steak',
            'Beef Fillet' => 'beef_fillet',
            'Fillet' => 'beef_fillet',
            'Beef Liver' => 'beef_liver',
            'Tripe' => 'tripe',
        ];

        // Query to get latest prices for meat products
        $prices = DB::table('sales_prices as sp')
            ->join('inv_current_stock as cs', 'sp.stock_id', '=', 'cs.id')
            ->join('inv_products as p', 'cs.product_id', '=', 'p.id')
            ->whereIn('p.name', array_keys($productMapping))
            ->select('p.name', 'sp.price')
            ->orderBy('sp.updated_at', 'desc')
            ->get();

        $meatPrices = [
            'meat' => 0,
            'steak' => 0,
            'beef_fillet' => 0,
            'beef_liver' => 0,
            'tripe' => 0,
        ];

        foreach ($prices as $price) {
            $key = $productMapping[$price->name] ?? null;
            if ($key && $meatPrices[$key] == 0) {
                $meatPrices[$key] = $price->price;
            }
        }

        return $meatPrices;
    }
}
