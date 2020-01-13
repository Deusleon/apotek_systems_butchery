<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\IssueReturn;
use App\Location;
use App\StockIssue;
use App\StockTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IssueReturnController extends Controller
{


    public function index()
    {

        $all_issues = StockIssue::select(DB::raw('sum(quantity) as quantity'), 'issue_no', 'issued_to')
            ->where('status', 1)
            ->groupby('issue_no', 'issued_to')
            ->get();

        $locations = Location::all();

        return view('stock_management.issue_return.index')->with([
            'all_issues' => $all_issues,
            'locations' => $locations
        ]);


    }

    public function store(Request $request)
    {

        $sub_total = ($request->quantity_rtn) * ($request->unit_cost);

        //on return substract in stock issue
        $remain = ($request->quantity) - ($request->quantity_rtn);

        //on return add in current stock
        $remain_in_stock = ($request->current_stock) + ($request->quantity_rtn);


        $issue_return = new IssueReturn;
        $issue_return->issue_id = $request->id;
        $issue_return->issue_qty = $request->quantity;
        $issue_return->return_qty = $request->quantity_rtn;
        $issue_return->return_value = $sub_total;
//        $issue_return->issued_to = $request->issued_to;
        $issue_return->issed_at = $request->issued_date;
        //to be changed the 2
        $issue_return->returned_by = Auth::user()->id;
        $issue_return->returned_at = date('Y-m-d');

        $issue_return->Reason = $request->reason;

        //also subtract in stock issue
        $issue_update = StockIssue::find($request->id);
//        $issue_update->quantity = $remain;
        $issue_update->status = 2;

        //also add in current stock
        $current_stock = CurrentStock::find($request->stock_id);
        $current_stock->quantity = $remain_in_stock;

        /*save in stock tracking as IN*/
        $stock_tracking = new StockTracking;
        $stock_tracking->stock_id = $request->stock_id;
        $stock_tracking->quantity = $request->quantity_rtn;
        $stock_tracking->store_id = 1;
        $stock_tracking->updated_by = Auth::user()->id;
        $stock_tracking->out_mode = 'Stock Issue';
        $stock_tracking->updated_at = date('Y-m-d');
        $stock_tracking->movement = 'IN';

        $stock_tracking->save();

//        if ($remain == 0) {
//            StockIssue::destroy($request->id);
//        }

        $issue_return->save();
        $issue_update->save();
        $current_stock->save();

        session()->flash("alert-success", "Product issue returned successfully!");
        return back();

    }

    public function issueHistory()
    {
        $issues = StockIssue::leftJoin('inv_issue_returns', function ($join) {
            $join->on('inv_stock_issues.id', '=', 'inv_issue_returns.issue_id');
        })->get();

        return view('stock_management.stock_issue.history')->with([
            'issues' => $issues
        ]);
    }

}
