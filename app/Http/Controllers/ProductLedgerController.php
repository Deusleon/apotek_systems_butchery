<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Store;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductLedgerController extends Controller
{


    public function index()
    {
        $default_store = Setting::where('id', 122)->value('value');
        $stores = Store::where('name', $default_store)->first();

        if ($stores != null) {
            $default_store_id = $stores->id;
        } else {
            $default_store_id = 1;
        }

        $products = DB::table('stock_details')
            ->select('product_id', 'product_name')
            ->where('store_id', $default_store_id)
            ->groupby('product_id', 'product_name')
            ->get();

        return view('stock_management.product_ledger.index')->with([
            'products' => $products
        ]);
    }

    public function showProductLedger(Request $request)
    {

        if ($request->ajax()) {
            $default_store = Setting::where('id', 122)->value('value');
            $stores = Store::where('name', $default_store)->first();

            if ($stores != null) {
                $default_store_id = $stores->id;
            } else {
                $default_store_id = 1;
            }

            $current_stock = DB::table('stock_details')
                ->select('product_id')
                ->where('store_id', $default_store_id)
                ->groupby('product_id')
                ->get();

            if ($request->date == null) {

                //return products only
                $ledger = DB::table('product_ledger')
                    ->select(DB::raw('*'), DB::raw('(received + outgoing) as quantity'))
                    ->join('users', 'users.id', '=', 'product_ledger.user')
                    ->where('store_id', $default_store_id)
                    ->where('product_id', $request->product_id)
                    ->get();

                $results = $this->sumProductFilterTotals($ledger, $current_stock);
                return $results;

            } else if ($request->product_id != '0' && $request->date != null) {

                //return both
                //previous row
                $previous_ledger = DB::table('product_ledger')
                    ->select(DB::raw('*'), DB::raw('(received + outgoing) as quantity'))
                    ->join('users', 'users.id', '=', 'product_ledger.user')
                    ->where('product_id', $request->product_id)
                    ->where('date', '<', $request->date)
                    ->orderby('product_ledger.id', 'desc')
                    ->limit('1');

                // $previous_ledger[0]['quantity'] = 80;

                $current_ledger = DB::table('product_ledger')
                    ->select(DB::raw('*'), DB::raw('(received + outgoing) as quantity'))
                    ->join('users', 'users.id', '=', 'product_ledger.user')
                    ->where('product_id', $request->product_id)
                    ->whereBetween('date', [$request->date, date('Y-m-d')]);

                $ledger = $previous_ledger->union($current_ledger)->get();

                $results = $this->sumProductFilterTotal($ledger, $current_stock);
                return $results;

            }

        }

    }

    public function sumProductFilterTotals($ledger, $current_stock)
    {
        $total = 0;
        $toMainView = array();

        //loop and perform addition on ins and outs to get the balance
        $balance = 0;
        foreach ($current_stock as $value) {

            foreach ($ledger as $key) {


                if ($value->product_id == $key->product_id) {

                    $total = $total + $key->received + $key->outgoing + $balance; // 0 + -20 + 0
                    $balance = 0;

                    if ($key->date == null) {

                        array_push($toMainView, array(
                            'date' => $key->date,
                            'name' => $key->product_name,
                            'method' => $key->method,
                            'quantity' => $key->quantity,
                            'movement' => $key->movement,
                            'product_id' => $key->product_id,
                            'balance' => $total,
                            'user' => $key->name
                        ));

                    } else {

                        array_push($toMainView, array(
                            'date' => date('Y-m-d', strtotime($key->date)),
                            'name' => $key->product_name,
                            'method' => $key->method,
                            'quantity' => $key->quantity,
                            'movement' => $key->movement,
                            'product_id' => $key->product_id,
                            'balance' => $total,
                            'user' => $key->name
                        ));

                    }

                }

            }

        }

        return $toMainView;

    }

    public function sumProductFilterTotal($ledger, $current_stock)
    {
        $total = 0;
        $toMainView = array();

//        dd($ledger);

        $default_store = Setting::where('id', 122)->value('value');
        $stores = Store::where('name', $default_store)->first();

        if ($stores != null) {
            $default_store_id = $stores->id;
        } else {
            $default_store_id = 1;
        }

        //check if the ledger has data
        try {
            if (isset($ledger[0])) {
                $final_ledger = DB::table('product_ledger')
                    ->select(DB::raw('product_id'), DB::raw('sum(received + outgoing) as balance'))
                    ->where('product_id', $ledger[0]->product_id)
                    ->where('store_id', $default_store_id)
                    ->where('id', '<', $ledger[0]->id)
                    ->groupBy('product_id')
                    ->get();

                //set balance for the previous product as balance brought fowardS
                if ($final_ledger[0]->product_id == null) {

                    $balance = 0;

                } else {

                    $balance = $final_ledger[0]->balance;

                }
            } else {
                //data not found empty search
                array_push($toMainView, array(
                    'date' => '-',
                    'name' => '-',
                    'method' => '-',
                    'quantity' => '-',
                    'movement' => '-',
                    'product_id' => '-',
                    'balance' => '-',
                    'user' => '-'
                ));

            }

            //loop and perform addition on ins and outs to get the balance
            foreach ($current_stock as $value) {

                foreach ($ledger as $key) {


                    if ($value->product_id == $key->product_id) {

                        $total = $total + $key->received + $key->outgoing + $balance; // 0 + -20 + 0
                        $balance = 0;

                        if ($key->date == null) {

                            array_push($toMainView, array(
                                'date' => $key->date,
                                'name' => $key->product_name,
                                'method' => $key->method,
                                'quantity' => $key->quantity,
                                'movement' => $key->movement,
                                'product_id' => $key->product_id,
                                'balance' => $total,
                                'user' => $key->name
                            ));

                        } else {

                            array_push($toMainView, array(
                                'date' => date('Y-m-d', strtotime($key->date)),
                                'name' => $key->product_name,
                                'method' => $key->method,
                                'quantity' => $key->quantity,
                                'movement' => $key->movement,
                                'product_id' => $key->product_id,
                                'balance' => $total,
                                'user' => $key->name
                            ));

                        }

                    }

                }

            }

            return $toMainView;

        } catch (Exception $exception) {
            $results = $this->sumProductFilterTotals($ledger, $current_stock);
            return $results;
        }

    }


}
