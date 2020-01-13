<?php

namespace App\Http\Controllers;

use App\Store;
use Exception;
use Illuminate\Http\Request;

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
        try {
            $store = new Store;
            $store->name = $request->name;
            $store->save();
        } catch (Exception $e) {
            session()->flash("alert-danger", "Store Name Exists!");
            return back();
        }

        session()->flash("alert-success", "Store Added Successfully!");
        return back();
    }

    public function destroy(Request $request)
    {
        try {
            Store::destroy($request->id);
            session()->flash("alert-danger", "Store Deleted successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Store in use!");
            return back();
        }

    }

    public function update(Request $request, $id)
    {
        $store = Store::find($request->id);
        $store->name = $request->name;
        $store->save();

        session()->flash("alert-success", "Store Updated Successfully!");
        return back();

    }

}
