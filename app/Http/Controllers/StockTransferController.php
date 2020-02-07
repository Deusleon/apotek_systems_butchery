<?php

namespace App\Http\Controllers;

use App\CommonFunctions;
use App\CurrentStock;
use App\StockTracking;
use App\StockTransfer;
use App\Store;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockTransferController extends Controller
{

    public function index()
    {

        $stores = Store::all();
        $products = CurrentStock::select(DB::raw('sum(quantity) as quantity'),
            DB::raw('product_id'), DB::raw('max(id) as stock_id'))
            ->where('quantity', '>', '0')
            ->groupby('product_id')
            ->get();

        return view('stock_management.stock_transfer.index')->with([
            'stores' => $stores,
            'products' => $products
        ]);

    }

    public function store(Request $request)
    {

        $transfer_no = $this->transferNumberAutoGen();
        $to_save_data = array();

        foreach (json_decode($request->cart, true) as $value) {

            if (!array_key_exists('quantityIn', $value)) {
                session()->flash("alert-danger", "Please quantity transfered exceeds quantity available!");
                return back();
            }

            $transfer_stock_id = $value['stock_id'];
            $transfer_product_id = $value['product_id'];
            $transfer_transfer_no = $transfer_no;
            $transfer_quantity = str_replace(',', '', $value['quantityTran']);
            $transfer_from_store = $request->from_id;
            $transfer_to_store = $request->to_id;
            $transfer_status = 1;
            $transfer_remarks = $request->remark;
            $transfer_updated_by = Auth::user()->id;
            $transfer_created_at = date('Y-m-d');

            array_push($to_save_data, array(
                'stock_id' => $transfer_stock_id,
                'product_id' => $transfer_product_id,
                'transfer_no' => $transfer_transfer_no,
                'transfer_qty' => $transfer_quantity,
                'from' => $transfer_from_store,
                'to' => $transfer_to_store,
                'status' => $transfer_status,
                'remarks' => $transfer_remarks,
                'updated_by' => $transfer_updated_by,
                'created_at' => $transfer_created_at
            ));

            $stock_update = CurrentStock::where('product_id', $value['product_id'])->where('quantity', '>', 0)->get();

            foreach ($stock_update as $stock) {
                if ($stock->quantity >= str_replace(',', '', $value['quantityTran'])) {
                    $present_stock = $stock->quantity - str_replace(',', '', $value['quantityTran']);
                    $stock->quantity = $present_stock;
                    if ($present_stock > 0) {
                        $value['quantityTran'] = 0;
                    }
                    $stock->save();
                } else {
                    $present_stock = str_replace(',', '', $value['quantityTran']) - $stock->quantity;
                    if ($present_stock > 0) {
                        $stock->quantity = 0;
                        $value['quantityTran'] = $present_stock;
                    }
                    $stock->save();

                }

            }

        }


        foreach ($to_save_data as $save_data) {
            DB::table('inv_stock_transfers')->insert(array(
                'stock_id' => $save_data['stock_id'],
                'transfer_qty' => $save_data['transfer_qty'],
                'from_store' => $save_data['from'],
                'to_store' => $save_data['to'],
                'status' => $save_data['status'],
                'remarks' => $save_data['remarks'],
                'updated_by' => $save_data['updated_by'],
                'created_at' => $save_data['created_at'],
                'transfer_no' => $save_data['transfer_no']
            ));

            /*save in stocktracking*/
            $stock_tracking = new StockTracking;
            $stock_tracking->stock_id = $save_data['stock_id'];
            $stock_tracking->product_id = $save_data['product_id'];
            $stock_tracking->quantity = $save_data['transfer_qty'];
            $stock_tracking->store_id = 1;
            $stock_tracking->updated_by = $save_data['updated_by'];
            $stock_tracking->out_mode = 'Stock Transfer';
            $stock_tracking->updated_at = date('Y-m-d');
            $stock_tracking->movement = 'OUT';
            $stock_tracking->save();

        }


        //redirect to the pdf part
        return redirect()->route('stock-transfer-pdf-gen', strval($transfer_no));

    }

    public function transferNumberAutoGen()
    {
        $number_gen = new CommonFunctions();
        $unique = $number_gen->generateNumber();
        return $unique;
    }

    public function generateStockTransferPDF($transfer_no)
    {

        $transfers = StockTransfer::where('transfer_no', $transfer_no)->get();


        $to = '';
        $from = '';
        $to_name = '';
        $from_name = '';
        $stores = array();

        foreach ($transfers as $transfer) {

            if ($to == $transfer->to_store && $from == $transfer->from_store) {
                $to = $transfer->to_store;
                $from = $transfer->from_store;

                if ($to == $transfer->toStore['id']) {
                    $to_name = $transfer->toStore['name'];
                }

                if ($from == $transfer->fromStore['id']) {
                    $from_name = $transfer->fromStore['name'];
                }

                array_push($stores, array(
                        'to' => $to_name,
                        'from' => $from_name,
                        'transfer_no' => $transfer->transfer_no)
                );
            }

            $to = $transfer->to_store;
            $from = $transfer->from_store;

            if ($to == $transfer->toStore['id']) {
                $to_name = $transfer->toStore['name'];
            }

            if ($from == $transfer->fromStore['id']) {
                $from_name = $transfer->fromStore['name'];
            }

            array_push($stores, array(
                    'to' => $to_name,
                    'from' => $from_name,
                    'transfer_no' => $transfer->transfer_no)
            );

        }

//        $transfer_detail = $stores[0];

        $inventory_report = new InventoryReportController();
        $view = 'stock_management.stock_transfer.stock_transfer_pdf';
        $output = 'stock_transfer.pdf';
        $inventory_report->splitPdf($transfers, $view, $output);

//        $pdf = PDF::loadView('stock_management.stock_transfer.stock_transfer_pdf',
//            compact('transfers', 'transfer_detail'));

//        return $pdf->stream('test.pdf');
    }

    public function regenerateStockTransferPDF(Request $request)
    {

        $transfer_no = $request->transfer_no;
        return redirect()->route('stock-transfer-pdf-gen', strval($transfer_no));

    }

    public function stockTransferHistory()
    {
        $all_transfers = StockTransfer::orderby('status', 'ASC')->get();

        return view('stock_management.stock_transfer.history')->with([
            'transfers' => $all_transfers
        ]);

    }

    public function filterTransferByDate(Request $request)
    {
        if ($request->ajax()) {
            $all_transfer = StockTransfer::where(DB::raw('date(created_at)'), '=', $request->date)->get();

            foreach ($all_transfer as $transfer) {
                $transfer->product;
            }

            return json_decode($all_transfer, true);

        }
    }

    public function filterByStore(Request $request)
    {
        if ($request->ajax()) {

            $products = CurrentStock::select(DB::raw('sum(quantity) as quantity'),
                DB::raw('product_id'), DB::raw('max(inv_current_stock.id) as stock_id'), 'name')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->where('quantity', '>', '0')
                ->where('status', '=', 1)
                ->where('store_id', $request->from_id)
                ->groupby('product_id', 'name')
                ->limit(10)
                ->get();

            foreach ($products as $product) {
                $product->product;
            }
            return json_decode($products, true);
        }
    }

    public function filterByWord(Request $request)
    {
        if ($request->ajax()) {

            $products = CurrentStock::select(DB::raw('sum(quantity) as quantity'),
                DB::raw('product_id'), DB::raw('max(inv_current_stock.id) as stock_id'), 'name')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->where('name', 'LIKE', "%{$request->word}%")
                ->where('quantity', '>', '0')
                ->where('store_id', $request->from_id)
                ->where('status', '=', 1)
                ->groupby('product_id', 'name')
                ->limit(10)
                ->get();
            foreach ($products as $product) {
                $product->product;
            }
            return json_decode($products, true);
        }
    }

}
