<?php

namespace App\Http\Controllers;

use App\AdjustmentReason;
use Exception;
use Illuminate\Http\Request;

class AdjustmentReasonController extends Controller
{
    public function index()
    {
        $adjustment = AdjustmentReason::orderBy('id', 'ASC')->get();
        return view('masters.adjustment_reason.index')->with('adjustment', $adjustment);
    }

    public function store(Request $request)
    {
        $adjustment = new AdjustmentReason;
        $adjustment->reason = $request->reason;
        $adjustment->save();
        session()->flash("alert-success", "Reason added successfully!");
        return back();
    }

    public function update(Request $request)
    {
        $adjustment = AdjustmentReason::find($request->adjustment_id);
        $adjustment->reason = $request->name;
        $adjustment->save();
        session()->flash("alert-success", "Reason updated successfully!");
        return back();

    }

   public function destroy(Request $request)
    {
         try {
            AdjustmentReason::destroy($request->id);
             session()->flash("alert-danger", "Reason Deleted successfully!");
            return back();
         } catch (Exception $exception) {
             session()->flash("alert-danger", "Reason in use!");
            return back();
        }

    }

}
