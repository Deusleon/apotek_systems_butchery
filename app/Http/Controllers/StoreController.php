<?php

namespace App\Http\Controllers;

use App\Setting;
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

        $default_store = Setting::where('id', 122)->value('value');

        try {
            $check_store = Store::find($request->id);

            if ($default_store === $check_store->name) {
                session()->flash("alert-danger", "Please change default store in settings!");
                return back();
            } else {
                Store::destroy($request->id);
                session()->flash("alert-danger", "Store Deleted successfully!");
                return back();
            }


        } catch (Exception $exception) {
            session()->flash("alert-danger", "Store in use!");
            return back();
        }

    }

    public function update(Request $request, $id)
    {
        $store = Store::find($request->id);
        $store->name = $request->name;
        try {
            $store->save();
            session()->flash("alert-success", "Store Updated Successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Store Exists!");
            return back();
        }

    }

}
