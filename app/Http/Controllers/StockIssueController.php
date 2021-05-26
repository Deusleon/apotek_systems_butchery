<?php

namespace App\Http\Controllers;

use App\CommonFunctions;
use App\CurrentStock;
use App\Location;
use App\PriceList;
use App\Setting;
use App\StockIssue;
use App\StockTracking;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockIssueController extends Controller
{

    public function index()
    {
        $stock_issues = StockIssue::all();
        $locations = Location::all();


        $products = $this->maxBuyPricePlusQuantity();

        return view('stock_management.stock_issue.index')->with([
            'products' => $products,
            'issues' => $stock_issues,
            'locations' => $locations
        ]);

    }

    public function maxBuyPricePlusQuantity()
    {
        /*get default store*/
        $default_store = Setting::where('id', 122)->value('value');
        $stores = Store::where('name', $default_store)->first();

        if ($stores != null) {
            $default_store_id = $stores->id;
        } else {
            $default_store_id = 1;
        }

        $max_prices = array();

        $products = PriceList::join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
            ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
            ->where('quantity', '>', 0)
            ->where('inv_current_stock.store_id', $default_store_id)
            ->where('inv_products.status', '=', 1)
            ->select('inv_products.id as id', 'name')
            ->groupBy('product_id')
            ->get();

        foreach ($products as $product) {
            $data = PriceList::select('stock_id', 'price')
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->orderBy('stock_id', 'desc')
                ->where('inv_current_stock.store_id', $default_store_id)
                ->where('product_id', $product->id)
                ->first('price');

            $quantity = CurrentStock::where('product_id', $product->id)
                ->where('inv_current_stock.store_id', $default_store_id)
                ->sum('quantity');

            array_push($max_prices, array(
                'product_name' => $data->currentStock['product']['name'],
                'unit_cost' => $data->currentStock['unit_cost'],
                'selling_price' => $data->price,
                'quantity' => $quantity,
                'id' => $data->stock_id,
                'product_id' => $product->id
            ));

        }

        return $max_prices;

    }

    public function store(Request $request)
    {

        $issue_no = $this->stockIssueNumberAutoGen();
        $present_stock = 0;
        $to_save_data = array();

        foreach (json_decode($request->cart, true) as $value) {

            if (!array_key_exists('quantityIn', $value)) {
                session()->flash("alert-danger", "Please quantity issued exceeds quantity available!");
                return back();
            }

            $issued_stock_id = $value['stock_id'];
            $issued_quantity = str_replace(',', '', $value['issuedQty']);
            $issued_unit_cost = floatval(preg_replace('/[^\d.]/', '', $value['buy_price']));
            $issued_sell_price = floatval(preg_replace('/[^\d.]/', '', $value['sell_price']));
            $issued_sub_total = floatval(preg_replace('/[^\d.]/', '', $value['sub_totals']));
            $issued_issue_no = $issue_no;
            $issued_issued_to = $request->from_id;
            $issued_updated_by = Auth::user()->id;
            $issued_created_at = date('Y-m-d');
            $issued_status = 1;

            array_push($to_save_data, array(
                'stock_id' => $issued_stock_id,
                'issued_qty' => $issued_quantity,
                'unit_cost' => $issued_unit_cost,
                'sell_price' => $issued_sell_price,
                'sub_total' => $issued_sub_total,
                'issue_no' => $issued_issue_no,
                'issued_to' => $issued_issued_to,
                'updated_by' => $issued_updated_by,
                'created_at' => $issued_created_at,
                'status' => $issued_status
            ));


            $stock_update = CurrentStock::where('product_id', $value['product_id'])->where('quantity', '>', 0)->get();

            foreach ($stock_update as $stock) {
                if ($stock->quantity >= str_replace(',', '', $value['issuedQty'])) {
                    $present_stock = $stock->quantity - str_replace(',', '', $value['issuedQty']);
                    $stock->quantity = $present_stock;
                    if ($present_stock > 0) {
                        $value['issuedQty'] = 0;
                    }
                    $stock->save();
                } else {
                    $present_stock = str_replace(',', '', $value['issuedQty']) - $stock->quantity;
                    if ($present_stock > 0) {
                        $stock->quantity = 0;
                        $value['issuedQty'] = $present_stock;
                    }
                    $stock->save();

                }

            }
        };


        foreach ($to_save_data as $save_data) {
            DB::table('inv_stock_issues')->insert(array(
                'stock_id' => $save_data['stock_id'],
                'quantity' => $save_data['issued_qty'],
                'unit_cost' => $save_data['unit_cost'],
                'sales_price' => $save_data['sell_price'],
                'sub_total' => $save_data['sub_total'],
                'issue_no' => $save_data['issue_no'],
                'issued_to' => $save_data['issued_to'],
                'updated_by' => $save_data['updated_by'],
                'created_at' => $save_data['created_at'],
                'status' => $save_data['status']
            ));


            /*save in stock tracking as IN*/
            $stock_tracking = new StockTracking;
            $stock_tracking->stock_id = $save_data['stock_id'];
            $stock_tracking->quantity = $save_data['issued_qty'];
            $stock_tracking->store_id = 1;
            $stock_tracking->updated_by = $save_data['updated_by'];
            $stock_tracking->out_mode = 'Stock Issue';
            $stock_tracking->updated_at = date('Y-m-d');
            $stock_tracking->movement = 'OUT';

            $stock_tracking->save();

        }

        //return to pdf gen
        return redirect()->route('stock-issue-pdf-gen', strval($issue_no));

    }

    public function stockIssueNumberAutoGen()
    {
        $number_gen = new CommonFunctions();
        $unique = $number_gen->generateNumber();
        return $unique;
    }

    public function stockIssueShow(Request $request)
    {

        $issue_no = $request->get("issue_no");

        if ($request->ajax()) {

            $results = StockIssue::where('issue_no', $issue_no)
                ->where('status', 1)
                ->get();

            $stocks = CurrentStock::all();

            foreach ($results as $result) {

                foreach ($stocks as $stock) {
                    if ($stock->id == $result->stock_id) {
                        //return product object
                        $result->currentStock->product;
                    }
                }
                //return to location object
                $result->issueLocation;

            }

            return json_decode($results, true);


        }
    }

    public function stockIssueShowReprint(Request $request)
    {

        $issue_no = $request->get("issue_no");

        if ($request->ajax()) {

            $results = StockIssue::where('issue_no', $issue_no)
                ->get();

            $stocks = CurrentStock::all();

            foreach ($results as $result) {

                foreach ($stocks as $stock) {
                    if ($stock->id == $result->stock_id) {
                        //return product object
                        $result->currentStock->product;
                    }
                }
                //return to location object
                $result->issueLocation;

            }

            return json_decode($results, true);


        }
    }

    public function stockIssueFilter(Request $request)
    {

        if ($request->ajax()) {
            if ($request->from_id == '0' && $request->date != null) {
                //return date search only
                $results = StockIssue::select('issued_to', 'issue_no', 'created_at', DB::raw('sum(quantity) as quantity'))
                    ->where(DB::raw('date(created_at)'), '=', date('Y-m-d', strtotime($request->date)))
                    ->groupby('issue_no', 'created_at')
                    ->orderby('created_at', 'DESC')
                    ->get();

                foreach ($results as $result) {
                    $result->issueLocation;
                }

                return json_decode($results, true);

            } elseif ($request->from_id != '0' && $request->date == null) {
                //return issued to only search
                $results = StockIssue::select('issued_to', 'issue_no', 'created_at', DB::raw('sum(quantity) as quantity'))
                    ->where('issued_to', $request->from_id)
                    ->groupby('issue_no', 'created_at')
                    ->orderby('created_at', 'DESC')
                    ->get();

                foreach ($results as $result) {
                    $result->issueLocation;
                }

                return json_decode($results, true);

            } else if ($request->from_id == '0' && $request->date == null) {
                $results = StockIssue::select('issued_to', 'issue_no', 'created_at', DB::raw('sum(quantity) as quantity'))
                    ->groupby('issue_no', 'created_at')
                    ->orderby('created_at', 'DESC')
                    ->get();

                foreach ($results as $result) {
                    $result->issueLocation;
                }

                return json_decode($results, true);

            }

            //make double search
            $results = StockIssue::select('issued_to', 'issue_no', 'created_at', DB::raw('sum(quantity) as quantity'))
                ->where('issued_to', $request->from_id)
                ->where(DB::raw('date(created_at)'), '=', date('Y-m-d', strtotime($request->date)))
                ->groupby('issue_no', 'created_at')
                ->orderby('created_at', 'DESC')
                ->get();

            foreach ($results as $result) {
                $result->issueLocation;
            }

            return json_decode($results, true);

        }

    }

    public function generateStockIssuePDF($issue_no)
    {

        $issues = StockIssue::where('issue_no', $issue_no)->get();


        $issued_to = '';
        $stores = array();
        $total = 0;

        foreach ($issues as $issue) {

            if ($issued_to == $issue->issued_to) {

                $issue->issued_to;

                array_push($stores, array(
                        'issued_to' => $issue->issueLocation['name'])
                );
            }

            $total = $total + $issue->sub_total;

            $issued_to = $issue->issued_to;
            $issue->issued_to;


            array_push($stores, array(
                    'issued_to' => $issue->issueLocation['name'],
                    'issue_no' => $issue_no,
                    'created_at' => date('Y-m-d', strtotime($issue->created_at)))
            );


        }

        // dd($stores);
        session()->put("issue_total", $total);

//        $transfer_detail = $stores[0];
        $inventory_report = new InventoryReportController();
        $view = 'stock_management.stock_issue.stock_issue_pdf';
        $output = 'stock_issue.pdf';
        $inventory_report->splitPdf($issues, $view, $output);

//        $pdf = PDF::loadView('stock_management.stock_issue.stock_issue_pdf',
//            compact('issues', 'transfer_detail'));
//

    }

    public function regenerateStockIssuePDF(Request $request)
    {

        return redirect()->route('stock-issue-pdf-gen', strval($request->issue_nos));

    }

}
