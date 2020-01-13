<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\Product;
use App\StockTracking;
use App\StockTransfer;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferAcknowledgeController extends Controller
{


    public function index()
    {

        $stores = Store::all();
        $all_transfers = StockTransfer::where('status', '=', '1')->get();

        return view('stock_management.stock_transfer_acknowledge.index')->with([
            'stores' => $stores,
            'all_transfers' => $all_transfers
        ]);
    }

    public function store(Request $request)
    {

    }

    public function update(Request $request)
    {

        $stock_update = CurrentStock::find($request->stock_id);

        $remain_stock = $request->quantity_trn - $request->quantity_rcvd;
        $present_stock = $stock_update->quantity + $remain_stock;

        $stock_update->quantity = $present_stock;
        $stock_update->save();

        $transfer = StockTransfer::find($request->id);
        $transfer->accepted_qty = $request->quantity_rcvd;
        $transfer->status = 2;
        $transfer->save();

        /*save in stocktracking*/
        $stock_tracking = new StockTracking;
        $stock_tracking->stock_id = $request->stock_id;
        $stock_tracking->quantity = $remain_stock;
        $stock_tracking->store_id = 1;
        $stock_tracking->updated_by = Auth::user()->id;
        $stock_tracking->out_mode = 'Stock Transfer';
        $stock_tracking->updated_at = date('Y-m-d');
        $stock_tracking->movement = 'IN';
        $stock_tracking->save();

        session()->flash("alert-success", "Transfer updated successfully!");
        return back();
    }

    public function destroy(Request $request)
    {

    }

    public function transferFilter(Request $request)
    {

        // $transfers = StockTransfer::all();


        $from = $request->get("from_val");
        $to = $request->get("to_val");

        if ($request->ajax()) {


            if ($from != 0 && $to != 0) {

                $results = StockTransfer::select(DB::raw('sum(transfer_qty) as transfer_qty'),
                    DB::raw('transfer_no'), DB::raw('date_format(created_at,"%d-%m-%Y") as date'))
                    ->where('to_store', '=', $to)
                    ->where('from_store', '=', $from)
                    ->groupby('transfer_no')
                    ->orderby('created_at','DESC')
                    ->get();

                return json_decode($results, true);

            } else if ($from == 0 && $to != 0) {

                $results = StockTransfer::select(DB::raw('sum(transfer_qty) as transfer_qty'),
                    DB::raw('transfer_no'), DB::raw('date_format(created_at,"%d-%m-%Y") as date'))
                    ->groupby('transfer_no')
                    ->orderby('created_at','DESC')
                    ->get();

                return json_decode($results, true);

            } else if ($from != 0 && $to == 0) {

                $results = StockTransfer::select(DB::raw('sum(transfer_qty) as transfer_qty'),
                    DB::raw('transfer_no'), DB::raw('date_format(created_at,"%d-%m-%Y") as date'))
                    ->where('from_store', '=', $from)
                    ->groupby('transfer_no')
                    ->orderby('created_at','DESC')
                    ->get();

                return json_decode($results, true);

            }

        }

    }

    public function transferFilterDetailComplete(Request $request)
    {

        $from = $request->get("from_val");
        $to = $request->get("to_val");

        if ($request->ajax()) {


            if ($from != 0 && $to != 0) {

                $results = StockTransfer::where('status', '=', '1')
                    ->where('from_store', '=', $from)
                    ->where('to_store', '=', $to)
                    ->get();

                foreach ($results as $value) {
                    $value->toStore;
                    $value->fromStore;
                    $value->currentStock['product'];

                }

                return json_decode($results, true);

            } else if ($from == 0 && $to != 0) {

                // $results = StockTransfer::where('status','=','1')->where('to_store','=',$to)->get();

                $results = StockTransfer::where('status', '=', '1')
                    ->where('to_store', '=', $to)
                    ->get();

                foreach ($results as $value) {
                    $value->toStore;
                    $value->fromStore;
                    dd($value->currentStock);
                    $value->currentStock['product'];
                }


                return json_decode($results, true);

            } else if ($from != 0 && $to == 0) {

                // $results = StockTransfer::where('status','=','1')->where('from_store','=',$from)->get();

                $results = StockTransfer::where('status', '=', '1')
                    ->where('from_store', '=', $from)
                    ->get();

                foreach ($results as $value) {
                    $value->toStore;
                    $value->fromStore;
                    $value->currentStock['product'];
                }


                return json_decode($results, true);

            }

        }

    }

    public function stockTransferComplete(Request $request)
    {

        if ($request->ajax()) {
            //update the transfer table
            if ($this->update($request) == true) {
                //return updated table
                $this->transferFilter($request);
            }
        }

    }

    public function stockTransferShow(Request $request)
    {

        $from = $request->get("from_val");
        $to = $request->get("to_val");
        $transfer_no = $request->get("transfer_no");

        $to_name = '';
        $from_name = '';
        $product_name = '';
        $stores = array();
        $info = array();
        $products = Product::all();

        if ($request->ajax()) {
            if ($from != 0 && $to != 0) {

                $results = StockTransfer::where('transfer_no', $transfer_no)->get();

                foreach ($results as $result) {

                    $result->currentStock['product'];

                    $result->currentStock;
                    //return to store object
                    $result->toStore;

                    //return to from store object
                    $result->fromStore;

                }

                return json_decode($results, true);

            } else {

                $results = StockTransfer::where('transfer_no', $transfer_no)->get();

                foreach ($results as $result) {

                    $result->currentStock['product'];

                    //return to store object
                    $result->toStore;

                    //return to from store object
                    $result->fromStore;

                }

                return json_decode($results, true);

            }


        }
    }


}
