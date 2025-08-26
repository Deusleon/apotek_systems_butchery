<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\PriceList;
use App\Product;
use App\Setting;
use App\StockTracking;
use App\StockTransfer;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockTransferAcknowledgeController extends Controller
{


    public function index($transfer_no)
    {
        $stores = Store::where('name','<>','ALL')->get();
        $store_id = Auth::user()->store_id;
        $all_transfers = StockTransfer::where('status', '=', 'approved')
            ->where('transfer_no','=',$transfer_no)
            ->get();

        return view('stock_management.stock_transfer_acknowledge.index')->with([
            'stores' => $stores,
            'all_transfers' => $all_transfers
        ]);
    }

    public function store(Request $request)
    {

    }

    public function destroy(Request $request)
    {

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

    public function update(Request $request)
    {

        /*get default store*/
        $default_store = Auth::user()->store[0]->name;
        $stores = Store::where('name', $default_store)->first();

        if ($stores != null) {
            $default_store_id = $stores->id;
        } else {
            $default_store_id = 1;
        }

        $stock_update = CurrentStock::find($request->stock_id);

        $transfered_quantity = (int)str_replace(',','',$request->quantity_trn);
        $received_quantity = (int)str_replace(',','',$request->quantity_rcvd);

        $remain_stock =  $transfered_quantity  - $received_quantity;
        $present_stock = $stock_update->quantity + $remain_stock;

        $stock_update->quantity = $present_stock;
        $stock_update->save();

        /*status 2 meaning received*/
        $transfer = StockTransfer::find($request->id);
        $transfer->accepted_qty = $request->quantity_rcvd;
        $transfer->status = 2;
        $transfer->save();

        /*insert in current stock*/
        $current_stock = new CurrentStock;
        $current_stock->product_id = $stock_update->product_id;
        $current_stock->expiry_date = $stock_update->expiry_date;
        $current_stock->quantity = $request->quantity_rcvd;
        $current_stock->unit_cost = $stock_update->unit_cost;
        $current_stock->batch_number = $stock_update->batch_number;
        $current_stock->store_id = $transfer->to_store;
        $current_stock->created_by = Auth::user()->id;
        $current_stock->save();
        /*end of insert*/

        /*insert into price*/
        $prev_price = PriceList::where('stock_id', $request->stock_id)
            ->orderby('id', 'desc')
            ->first();
        $price = new PriceList;
        $price->stock_id = $current_stock->id;
        $price->price = str_replace(',', '', $prev_price->price);
        $price->price_category_id = $prev_price->price_category_id;
        $price->status = 1;
        $price->created_at = date('Y-m-d H:m:s');
        $price->save();
        /*end insert*/

        /*save in stocktracking*/
        $stock_tracking = new StockTracking;
        $stock_tracking->stock_id = $request->stock_id;
        $stock_tracking->product_id = $transfer->currentStock['product_id'];
        $stock_tracking->quantity = $remain_stock;
        $stock_tracking->store_id = $default_store_id;
        $stock_tracking->updated_by = Auth::user()->id;
        $stock_tracking->out_mode = 'Stock Transfer';
        $stock_tracking->updated_at = date('Y-m-d');
        $stock_tracking->movement = 'IN';
        $stock_tracking->save();

        $stock_tracking = new StockTracking;
        $stock_tracking->stock_id = $request->stock_id;
        $stock_tracking->product_id = $transfer->currentStock['product_id'];
        $stock_tracking->quantity = $request->quantity_rcvd;
        $stock_tracking->store_id = $transfer->to_store;
        $stock_tracking->updated_by = Auth::user()->id;
        $stock_tracking->out_mode = 'Stock Transfer Completed';
        $stock_tracking->updated_at = date('Y-m-d');
        $stock_tracking->movement = 'IN';
        $stock_tracking->save();

        session()->flash("alert-success", "Transfer updated successfully!");

        $count = StockTransfer::where('transfer_no','=',$request->transfer_no)
        ->where('status','=','1')->count();

        if($count > 0)
        {
            return back();
        }

        return redirect()->route('stock-transfer-history');
    }

    public function filterTransfer(Request $request)
    {

        $from = $request->get("from_val");
        $to = $request->get("to_val");

        if ($request->ajax()) {


            $results= StockTransfer::with(['fromStore','toStore'])
                ->select(DB::raw('*, SUM(transfer_qty) as quantity, SUM(accepted_qty) as received_quantity, COUNT(*) as total_products , MIN(status) as status'))
                ->orderBy('created_at', 'Desc')
                ->groupBy('transfer_no')->get();
            if ($from != 0 && $to != 0) {
                $results= StockTransfer::with(['fromStore','toStore'])
                    ->where('from_store','=',$from)
                    ->where('to_store','=',$to)
                    ->select(DB::raw('*, SUM(transfer_qty) as quantity, SUM(accepted_qty) as received_quantity, COUNT(*) as total_products , MIN(status) as status'))
                    ->orderBy('created_at', 'Desc')
                    ->groupBy('transfer_no')->get();
            }

            if ($from = 0 && $to != 0) {
                $results= StockTransfer::with(['fromStore','toStore'])
                    ->where('to_store','=',$to)
                    ->select(DB::raw('*, SUM(transfer_qty) as quantity, SUM(accepted_qty) as received_quantity, COUNT(*) as total_products , MIN(status) as status'))
                    ->orderBy('created_at', 'Desc')
                    ->groupBy('transfer_no')->get();
            }


            if ($from != 0 && $to = 0) {
                $results= StockTransfer::with(['fromStore','toStore'])
                    ->where('from_store','=',$from)
                    ->select(DB::raw('*, SUM(transfer_qty) as quantity, SUM(accepted_qty) as received_quantity, COUNT(*) as total_products , MIN(status) as status'))
                    ->orderBy('created_at', 'Desc')
                    ->groupBy('transfer_no')->get();
            }

        }


        Log::info('FilteredData',['Results'=>$results]);



        return json_decode($results, true);
    }

    //Used when filter occurs
    public function transferFilter(Request $request)
    {

        $from = $request->get("from_val");
        $to = $request->get("to_val");

        if ($request->ajax()) {


            if ($from != 0 && $to != 0) {

                $results = StockTransfer::with(['fromStore','toStore'])
                    ->select(DB::raw('*,transfer_no,date_format(created_at,"%d-%m-%Y") as date,sum(transfer_qty) as transfer_qty'))
                    ->where('to_store', '=', $to)
                    ->where('from_store', '=', $from)
                    ->groupby('transfer_no')
                    ->orderby('created_at', 'DESC')
                    ->get();



                Log::info('FilteredData2',['Results'=>$results]);

                return json_decode($results, true);

            } else if ($from == 0 && $to != 0) {

                $results = StockTransfer::with(['fromStore','toStore'])
                    ->select(DB::raw('*,transfer_no,date_format(created_at,"%d-%m-%Y") as date,sum(transfer_qty) as transfer_qty'))
                    ->where('to_store', '=', $to)
                    ->groupby('transfer_no')
                    ->orderby('created_at', 'DESC')
                    ->get();

                Log::info('FilteredData2',['Results'=>$results]);

                return json_decode($results, true);

            } else if ($from != 0 && $to == 0) {

                $results = StockTransfer::with(['fromStore','toStore'])
                    ->select(DB::raw('*,transfer_no,date_format(created_at,"%d-%m-%Y") as date,sum(transfer_qty) as transfer_qty'))
                    ->where('from_store', '=', $from)
                    ->groupby('transfer_no')
                    ->orderby('created_at', 'DESC')
                    ->get();

                Log::info('FilteredData2',['Results'=>$results]);

                return json_decode($results, true);

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

                $results = StockTransfer::with(['fromStore','toStore'])->
                    join('inv_current_stock','inv_current_stock.id','=','inv_stock_transfers.stock_id')
                    ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
                    ->select('*','inv_products.name as product_name')
                    ->where('transfer_no', $transfer_no)
                    ->get();

                Log::info('DataLoaded',["Results"=>$results]);

                return json_decode($results, true);

            } else {

                $results = StockTransfer::with(['fromStore','toStore'])->join('inv_current_stock','inv_current_stock.id','=','inv_stock_transfers.stock_id')
                    ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
                    ->select('*','inv_products.name as product_name')
                    ->where('transfer_no', $transfer_no)
                    ->get();

                Log::info('DataLoaded',["Results"=>$results]);

                return json_decode($results, true);

            }


        }
    }


}
