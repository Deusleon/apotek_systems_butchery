<?php

namespace App\Http\Controllers;

use App\StockTransfer;
use App\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RePrintTransferController extends Controller
{

    public function index()
    {
        $transfer_data = array();
        $store_id = Auth::user()->store_id;
        $all_transfers = StockTransfer::
        join('inv_stores as a','inv_stock_transfers.from_store','a.id')
        ->join('inv_stores as b','inv_stock_transfers.to_store','b.id')
        ->where('from_store','=',$store_id)
        ->orWhere('to_store','=',$store_id)
        ->select(DB::raw('sum(transfer_qty) as quantity'),
            DB::raw('transfer_no'),
            DB::raw('a.name as fromStore'),
            DB::raw('b.name as toStore'))
            ->groupby('transfer_no','created_at')
            ->orderby('created_at','DESC')
            ->get();
        $x = 1;
        foreach ($all_transfers as $transfer) {
            $transfers = StockTransfer::where('transfer_no', $transfer->transfer_no)->first();
            array_push($transfer_data, array(
                'id' => $x,
                'quantity' => $transfer->quantity,
                'transfer_no' => $transfer->transfer_no,
                'fromStore' => $transfer->fromStore,
                'toStore' => $transfer->toStore,
                'date' => date('d-m-Y', strtotime($transfers->created_at))
            ));
            $x++;
        }


        $sort_column = array_column($transfer_data, 'id');
        array_multisort($sort_column, SORT_ASC, $transfer_data);

        $stores = Store::where('name','<>','ALL')->get();

		return view('stock_management.re_print_transfer.index')->with([
            'all_transfers' => $transfer_data,
            'stores' => $stores
        ]);

	}

}
