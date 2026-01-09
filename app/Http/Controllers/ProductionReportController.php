<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Production;
use App\Setting;
use Barryvdh\DomPDF\Facade as PDF;

class ProductionReportController extends Controller
 {
    public function index()
 {
        return view( 'production_reports.index' );
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
        
        $pdf = PDF::loadView( 'production_reports.report_pdf',
        compact( 'data', 'pharmacy', 'enable_discount' ) )
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
}
