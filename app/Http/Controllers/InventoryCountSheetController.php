<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use Illuminate\Support\Facades\DB;

class InventoryCountSheetController extends Controller
{


    public function generateInventoryCountSheetPDF()
    {
        $data_og = array();
        $current_stocks = CurrentStock::select(DB::raw('product_id'), 'store_id', 'shelf_number',
            DB::raw('sum(quantity) as quantity_on_hand'))
            ->groupby('product_id')
            ->get();

        foreach ($current_stocks as $current_stock) {
            array_push($data_og, array(
                'store' => $current_stock->store['name'],
                'shelf_no' => $current_stock->shelf_number,
                'product_id' => $current_stock->product['id'],
                'product_name' => $current_stock->product['name'],
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
