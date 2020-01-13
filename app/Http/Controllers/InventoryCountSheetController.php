<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class InventoryCountSheetController extends Controller
{


    public function generateInventoryCountSheetPDF()
    {
        $data_og = array();
        $current_stocks = DB::table('inv_current_stock as g')
            ->select(DB::raw('g.product_id'), DB::raw('g1.product_name'),
                DB::raw('sum(g.quantity) as quantity_on_hand'), DB::raw('g.shelf_number')
                , DB::raw('g1.store'))
            ->join(DB::raw('(
            SELECT 
            `product_name`,
            `product_id`,
            `store`
            FROM
                `stock_details`
            GROUP BY `product_id`) as g1'),
                function ($join) {
                    $join->on('g1.product_id', '=', 'g.product_id');
                })
            ->groupBy(DB::raw('g1.product_id'))
            ->get();

        foreach ($current_stocks as $current_stock) {
            array_push($data_og, array(
                'store' => $current_stock->store,
                'shelf_no' => $current_stock->shelf_number,
                'product_id' => $current_stock->product_id,
                'product_name' => $current_stock->product_name,
                'quantity_on_hand' => $current_stock->quantity_on_hand
            ));
        }

        if ($data_og == []) {
            return response()->view('error_pages.pdf_zero_data');
        }

        $view = 'stock_management.daily_stock_count.inventory_count_sheet';
        $output = 'inventory_count_sheet.pdf';
        $inventory_report = new InventoryReportController();
        $inventory_report->splitPdf($data_og, $view, $output);

    }


}
