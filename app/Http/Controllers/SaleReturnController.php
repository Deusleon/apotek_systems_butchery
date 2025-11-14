<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\Customer;
use App\Sale;
use App\SalesCredit;
use App\SalesDetail;
use App\SalesReturn;
use App\StockTracking;
use App\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class SaleReturnController extends Controller
{
    public function index()
    {
        date_default_timezone_set('Africa/Nairobi');
        $date = date('m-d-Y');
        $vat = Setting::where('id', 120)->value('value') / 100;//Get VAT %
        $sales = Sale::where(DB::Raw("DATE_FORMAT(date,'%m-%d-%Y')"), '=', $date)->orderBy('id', 'desc')->get();
        $count = $sales->count();
        $enable_discount = Setting::where( 'id', 111 )->value( 'value' );
        return View::make('sales.sale_returns.index')
            ->with(compact('vat'))
            ->with(compact('sales'))
            ->with(compact('count'))
            ->with(compact('enable_discount'));
    }
    public function getSales(Request $request)
    {

        $columns = array(
            0 => 'receipt_number',
            1 => 'date',
            2 => 'price_category_id'
        );

        $from = $request->range[0];
        $to = $request->range[1];

        $totalData = Sale::whereBetween(DB::raw('date(date)'),
            [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])
            ->orderby('id', 'DESC')
            ->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $sales = Sale::whereBetween(DB::raw('date(date)'),
                [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])
                ->offset($start)
                ->limit($limit)
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $search = $request->input('search.value');

            $sales = Sale::whereBetween(DB::raw('date(date)'),
                [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])
                ->where('receipt_number', 'LIKE', "%{$search}%")
                ->orWhere(DB::raw('date(date)'), 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = Sale::whereBetween(DB::raw('date(date)'),
                [date('Y-m-d', strtotime($from)), date('Y-m-d', strtotime($to))])
                ->where('receipt_number', 'LIKE', "%{$search}%")
                ->orWhere(DB::raw('date(date)'), 'LIKE', "%{$search}%")
                ->count();
        }

        $data = array();
        if (!empty($sales)) {
            foreach ($sales as $sale) {

                $sale->cost;
                $sale->details;
                $sale->customer;

            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $sales
        );

        echo json_encode($json_data);

    }
    public function getRetunedProducts(Request $request)
    {
        if ($request->action == "approve") {
            $this->approve($request->product);
        }
        if ($request->action == "reject") {
            $this->reject($request->product);
        }
        $from = $request->date[0];
        $to = $request->date[1];
        if ($request->status == 4) {
            $returns = SalesReturn::join('sales_details', 'sales_details.id', '=', 'sales_returns.sale_detail_id')
                ->where('sales_details.status', '=', 4)
                ->where(DB::Raw("DATE_FORMAT(date,'%Y/%m/%d')"), '>=', $from)
                ->where(DB::Raw("DATE_FORMAT(date,'%Y/%m/%d')"), '<=', $to)
                ->orderBy('sales_details.id', 'desc')
                ->get();
        } else if ($request->status == 3) {
            $returns = SalesReturn::join('sales_details', 'sales_details.id', '=', 'sales_returns.sale_detail_id')
                ->where('sales_details.status', '=', 3)
                ->orWhere('sales_details.status', '=', 5)
                ->where(DB::Raw("DATE_FORMAT(date,'%Y/%m/%d')"), '>=', $from)
                ->where(DB::Raw("DATE_FORMAT(date,'%Y/%m/%d')"), '<=', $to)
                ->orderBy('sales_details.id', 'desc')
                ->get();
        } else {
            $returns = SalesReturn::join('sales_details', 'sales_details.id', '=', 'sales_returns.sale_detail_id')
                ->where('sales_details.status', '=', 2)
                ->where(DB::Raw("DATE_FORMAT(date,'%Y/%m/%d')"), '>=', $from)
                ->where(DB::Raw("DATE_FORMAT(date,'%Y/%m/%d')"), '<=', $to)
                ->orderBy('sales_details.id', 'desc')
                ->get();
        }

        foreach ($returns as $value) {
            $value->item_returned;
        }
        $data = json_decode($returns, true);
        Log::info($data);
        return $data;
    }
    public function approve($request)
    {
        Log::info($request);
        $creditID = SalesCredit::where('sale_id', $request['sale_id'])->value('id');
        $stock = CurrentStock::find($request['stock_id']);
        $details = SalesDetail::find($request['item_detail_id']);
        $stock->quantity += $request['rtn_qty'];
        $newqty = $request['remained_qty']-$request['rtn_qty'];
        $old_vat = $details->vat; 

        //IF Partial return the values are re-calculated
        if ($newqty != 0) {
            $original_amount = $details->amount;
            $status = 5;
            $details->vat = ($old_vat / $details->quantity) * ($newqty);
            $details->amount = ((($details->amount-$old_vat) / $details->quantity) * $newqty)+$details->vat;
            $details->discount = ($details->discount / $details->quantity) * ($newqty);
            $details->quantity = $newqty;

            if ($creditID) {
                $returned_amount = $original_amount - $details->amount;
                $credit_sale = Sale::find($request['sale_id']);
                $re_sum_total_credit = $credit_sale->customer['total_credit'] - $returned_amount;
                $customer = Customer::find($credit_sale->customer_id);
                $customer->total_credit = $re_sum_total_credit;
                $customer->save();
            }

        } else {
            $status = 3;
            $details->discount = 0;

            if ($creditID) {
                $credit_sale = Sale::find($request['sale_id']);
                $re_sum_total_credit = $credit_sale->customer['total_credit'] - $details->amount;
                $customer = Customer::find($credit_sale->customer_id);
                $customer->total_credit = $re_sum_total_credit;
                $customer->save();
            }
        }
        $details->status = $status;
        $details->updated_by = Auth::User()->id;

        $details->save();
        $stock->save();
        
        StockTracking::create([
            'stock_id' => $stock->id,
            'product_id' => $stock->product_id,
            'out_mode' => 'Sales Return',
            'quantity' => $request['rtn_qty'],
            'store_id' => current_store_id(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'updated_at' => now()->format('Y-m-d'),
            'movement' => 'IN',
        ]);

        return back();
    }

    public function reject($request)
    {
        $details = SalesDetail::find($request['item_detail_id']);
        $details->status = 4;
        $details->updated_by = Auth::User()->id;
        $details->save();

        return back();
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Africa/Nairobi');
        $date = date('Y-m-d,H:i:s');
        $details = SalesDetail::find($request->item_id);
        $sales_return = new SalesReturn;
        $sales_return->sale_detail_id = $request->item_id;
        $sales_return->quantity = $request->quantity;
        $sales_return->reason = $request->reason;
        $sales_return->date = $date;
        $sales_return->created_by = Auth::User()->id;
        $details->status = 2;
        $details->updated_by = Auth::User()->id;
        $details->save();
        $sales_return->save();
        session()->flash("alert-success", "Product returned, transaction will be effected after approval!");
        return back();
    }

    public function getSalesReturn()
    {
        return View::make('sales.sale_returns_approval.index');
    }

    public function getDetails(Request $request)
    {
        $sale = Sale::where('id', $request->id)->get();
        foreach ($sale as $value) {
            $value->details;
        }
        $data = json_decode($sale, true);
        return $data;

    }
}
