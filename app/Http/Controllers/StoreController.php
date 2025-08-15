<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Store;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    //
    public function index()
    {
        $stores = Store::orderBy('id', 'DESC')->get();
        $count = $stores->count();
        return view('masters.stores.index', compact("stores", 'count'));
    }

    public function store(Request $request)
    {
        $exist = Store::where('name','=',strtoupper($request->name))->count();

        if($exist>0)
        {
            session()->flash("alert-danger", "Branch Name Exists!");
            return back();
        }

        try {
            $store = new Store;
            $store->name = strtoupper($request->name);
            $store->save();
        } catch (Exception $e) {
            session()->flash("alert-danger", "Branch Name Exists!");
            return back();
        }

        session()->flash("alert-success", "Branch Added Successfully!");
        return back();
    }

    public function destroy(Request $request)
    {
        $default_store = Auth::user()->store->name ?? 'Default Store';



        try {
            $check_store = Store::find($request->id);

            if ($default_store === $check_store->name) {
                session()->flash("alert-danger", "Please change default branch in settings!");
                return back();
            } else {
                Store::destroy($request->id);
                session()->flash("alert-danger", "Branch Deleted successfully!");
                return back();
            }


        } catch (Exception $exception) {
            session()->flash("alert-danger", "Branch in use!");
            return back();
        }

    }

    public function update(Request $request, $id)
    {
        $store = Store::find($request->id);
        $store->name = $request->name;
        try {
            $store->save();
            session()->flash("alert-success", "Branch Updated Successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Branch Exists!");
            return back();
        }

    }

}
