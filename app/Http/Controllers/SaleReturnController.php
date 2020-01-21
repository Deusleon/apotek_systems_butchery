<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\Sale;
use App\SalesCredit;
use App\SalesDetail;
use App\SalesReturn;
use App\Setting;
use Auth;
use DB;
use Illuminate\Http\Request;
use View;

class SaleReturnController extends Controller
{
    public function index()
    {
        date_default_timezone_set('Africa/Nairobi');
        $date = date('m-d-Y');
        $vat = Setting::where('id', 120)->value('value') / 100;//Get VAT %
        $sales = Sale::where(DB::Raw("DATE_FORMAT(date,'%m-%d-%Y')"), '=', $date)->get();
        $count = $sales->count();
        return View::make('sales.sale_returns.index')
            ->with(compact('vat'))
            ->with(compact('sales'))
            ->with(compact('count'));
    }

    public function getSales(Request $request)
    {
        $from = $request->date[0];
        $to = $request->date[1];
        $user = Auth::user()->id;
        $sales = Sale::where(DB::Raw("DATE_FORMAT(date,'%m/%d/%Y')"), '>=', $from)
            ->where(DB::Raw("DATE_FORMAT(date,'%m/%d/%Y')"), '<=', $to)
            ->get();
        foreach ($sales as $sale) {
            $sale->cost;
            $sale->details;
        }
        $data = json_decode($sales, true);
        return $data;
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
                ->where(DB::Raw("DATE_FORMAT(date,'%m/%d/%Y')"), '>=', $from)
                ->where(DB::Raw("DATE_FORMAT(date,'%m/%d/%Y')"), '<=', $to)
                ->get();
        } else if ($request->status == 3) {
            $returns = SalesReturn::join('sales_details', 'sales_details.id', '=', 'sales_returns.sale_detail_id')
                ->where('sales_details.status', '=', 3)
                ->orWhere('sales_details.status', '=', 5)
                ->where(DB::Raw("DATE_FORMAT(date,'%m/%d/%Y')"), '>=', $from)
                ->where(DB::Raw("DATE_FORMAT(date,'%m/%d/%Y')"), '<=', $to)
                ->get();
        } else {
            $returns = SalesReturn::join('sales_details', 'sales_details.id', '=', 'sales_returns.sale_detail_id')
                ->where('sales_details.status', '=', 2)
                ->where(DB::Raw("DATE_FORMAT(date,'%m/%d/%Y')"), '>=', $from)
                ->where(DB::Raw("DATE_FORMAT(date,'%m/%d/%Y')"), '<=', $to)
                ->get();
        }

        foreach ($returns as $value) {
            $value->item_returned;
        }
        $data = json_decode($returns, true);

        return $data;
    }

    public function approve($request)
    {
        $creditID = SalesCredit::where('sale_id', $request['sale_id'])->value('id');
        $stock = CurrentStock::find($request['stock_id']);
        $details = SalesDetail::find($request['item_detail_id']);
        $stock->quantity += $request['rtn_qty'];
        $newqty = $request['bought_qty'] - $request['rtn_qty'];

//IF Partial return the values are re-calculated
        if ($newqty != 0) {
            $status = 5;
            $details->price = ($details->price / $details->quantity) * ($newqty);
            $details->vat = ($details->vat / $details->quantity) * ($newqty);
            $details->amount = ($details->amount / $details->quantity) * ($newqty);
            $details->discount = ($details->discount / $newqty) * ($newqty);
            $details->quantity = $newqty;
        } else {
            $status = 3;
            $details->discount = 0;
        }
        $details->status = $status;
        $details->updated_by = Auth::User()->id;

// if($creditID){
// dd('this is credit');
// }
// else{
// dd('this is not credit');
// }

        $details->save();
        $stock->save();
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
        session()->flash("alert-success", "Item Returned, transaction will be effected after approval!");
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
