<?php

namespace App\Http\Controllers;

use App\AdjustmentReason;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdjustmentReasonController extends Controller
{
    public function index()
    {
        $adjustment = AdjustmentReason::orderBy('id', 'ASC')->get();
        foreach ( $adjustment as $reason ) {
            $reason_count = DB::table( 'inv_stock_adjustments' )->where( 'reason', $reason->reason )->count();

            if ( $reason_count > 0 ) {
                $reason[ 'is_used' ] = 'yes';
            }

            if ( $reason_count == 0 ) {
                $reason[ 'is_used' ] = 'no';
            }

        }
        return view('masters.adjustment_reason.index')->with('adjustment', $adjustment);
    }

    public function store(Request $request)
    {
        $existing = AdjustmentReason::where('reason',$request->reason)->count();

        if($existing > 0)
        {
            session()->flash("alert-danger", "Reason Exists!");
            return back();
        }
        try {
            $adjustment = new AdjustmentReason;
            $adjustment->reason = $request->reason;
            $adjustment->save();
            session()->flash("alert-success", "Reason added successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Reason Exists!");
            return back();
        }
    }

    public function update(Request $request)
    {
        $existing = AdjustmentReason::where('reason',$request->name)->count();

        if($existing > 0)
        {
            session()->flash("alert-danger", "Reason Exists!");
            return back();
        }

        $adjustment = AdjustmentReason::find($request->adjustment_id);
        $adjustment->reason = $request->name;
        try {
            $adjustment->save();
            session()->flash("alert-success", "Reason updated successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Reason exists!");
            return back();
        }

    }

   public function destroy(Request $request)
    {
         try {
             AdjustmentReason::destroy($request->adjustment_id);
             session()->flash("alert-danger", "Reason Deleted successfully!");
            return back();
         } catch (Exception $exception) {
             session()->flash("alert-danger", "Reason in use!");
            return back();
        }

    }

}
