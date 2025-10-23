<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Store;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductLedgerController extends Controller
{


    public function index()
    {
        $default_store = Auth::user()->store->name ?? 'Default Store';
        $stores = Store::where('name', $default_store)->first();

        if ($stores != null) {
            $default_store_id = $stores->id;
        } else {
            $default_store_id = 1;
        }

        try {
            $products = DB::table('stock_details')
                ->select('product_id', 'product_name')
                ->where('store_id', $default_store_id)
                ->groupby('product_id', 'product_name')
                ->get();
        } catch (\Exception $e) {
            Log::warning('ProductLedger index stock_details query failed: ' . $e->getMessage());
            $products = collect(); // Return empty collection if table doesn't exist
        }

        return view('stock_management.product_ledger.index')->with([
            'products' => $products
        ]);
    }

    public function showProductLedger(Request $request)
    {

        if ($request->ajax()) {
            $default_store = Auth::user()->store->name ?? 'Default Store';
            $stores = Store::where('name', $default_store)->first();

            if ($stores != null) {
                $default_store_id = $stores->id;
            } else {
                $default_store_id = 1;
            }

            try {
                $current_stock = DB::table('stock_details')
                    ->select('product_id')
                    ->where('store_id', $default_store_id)
                    ->groupby('product_id')
                    ->get();
            } catch (\Exception $e) {
                Log::warning('ProductLedger showProductLedger stock_details query failed: ' . $e->getMessage());
                $current_stock = collect(); // Return empty collection if table doesn't exist
            }

            if ($request->date == null) {

                //return products only
                try {
                    $ledger = DB::table('product_ledger')
                        ->select(DB::raw('*'), DB::raw('(received + outgoing) as quantity'))
                        ->join('users', 'users.id', '=', 'product_ledger.user')
                        ->where('store_id', $default_store_id)
                        ->where('product_id', $request->product_id)
                        ->orderBy('product_ledger.id', 'ASC')
                        ->get();
                } catch (\Exception $e) {
                    Log::warning('ProductLedger showProductLedger product_ledger query failed: ' . $e->getMessage());
                    $ledger = collect(); // Return empty collection if table doesn't exist
                }

                $results = $this->sumProductFilterTotals($ledger, $current_stock);
                return $results;

            } else if ($request->product_id != '0' && $request->date != null) {

                //return both
                //previous row
                try {
                    $previous_ledger = DB::table('product_ledger')
                        ->select(DB::raw('*'), DB::raw('(received + outgoing) as quantity'))
                        ->join('users', 'users.id', '=', 'product_ledger.user')
                        ->where('product_id', $request->product_id)
                        ->where('date', '<', $request->date)
                        ->orderby('product_ledger.id', 'desc')
                        ->limit('1');
                } catch (\Exception $e) {
                    Log::warning('ProductLedger showProductLedger previous_ledger query failed: ' . $e->getMessage());
                    $previous_ledger = collect(); // Return empty collection if table doesn't exist
                }

                // $previous_ledger[0]['quantity'] = 80;

                try {
                    $current_ledger = DB::table('product_ledger')
                        ->select(DB::raw('*'), DB::raw('(received + outgoing) as quantity'))
                        ->join('users', 'users.id', '=', 'product_ledger.user')
                        ->where('product_id', $request->product_id)
                        ->orderBy('product_ledger.id', 'ASC')
                        ->whereBetween('date', [$request->date, date('Y-m-d')]);
                } catch (\Exception $e) {
                    Log::warning('ProductLedger showProductLedger current_ledger query failed: ' . $e->getMessage());
                    $current_ledger = collect(); // Return empty collection if table doesn't exist
                }

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

        $default_store = Auth::user()->store->name ?? 'Default Store';
        $stores = Store::where('name', $default_store)->first();

        if ($stores != null) {
            $default_store_id = $stores->id;
        } else {
            $default_store_id = 1;
        }

        //check if the ledger has data
        try {
            if (isset($ledger[0])) {
                try {
                    $final_ledger = DB::table('product_ledger')
                        ->select(DB::raw('product_id'), DB::raw('sum(received + outgoing) as balance'))
                        ->where('product_id', $ledger[0]->product_id)
                        ->where('store_id', $default_store_id)
                        ->where('id', '<', $ledger[0]->id)
                        ->groupBy('product_id')
                        ->get();
                } catch (\Exception $e) {
                    Log::warning('ProductLedger sumProductFilterTotal final_ledger query failed: ' . $e->getMessage());
                    $final_ledger = collect(); // Return empty collection if table doesn't exist
                }

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
