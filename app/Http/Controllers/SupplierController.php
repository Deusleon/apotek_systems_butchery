<?php

namespace App\Http\Controllers;

use App\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Add this import

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('id', 'DESC')->get();
        
        // Add transaction checking like in CustomerController
        foreach ($suppliers as $supplier) {
            // Check purchase orders
            $order_count = DB::table('orders')->where('supplier_id', $supplier->id)->count();
            
            // Check goods receiving
            $receiving_count = DB::table('inv_incoming_stock')->where('supplier_id', $supplier->id)->count();
            
            $total_count = $order_count + $receiving_count;
            
            if ($total_count > 0) {
                $supplier['active_user'] = 'has transactions';
            } else {
                $supplier['active_user'] = 'no transactions';
            }
        }
        
        return view('masters.suppliers.index', compact("suppliers"));
    }

    public function store(Request $request)
    {
        // Check if supplier with the same name already exists
        if (Supplier::where('name', $request->name)->exists()) {
            session()->flash("alert-danger", "Supplier with this name already exists!");
            return back();
        }

        $supplier = new Supplier;
        $supplier->name = $request->name;
        $supplier->contact_person = $request->contact_person;
        $supplier->address = $request->address;
        $supplier->mobile = $request->phone;
        $supplier->email = $request->email;
        try {
            $supplier->save();
            session()->flash("alert-success", "Supplier Added Successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Error adding supplier!");
            return back();
        }
    }

    public function update(Request $request)
    {
        // Check if another supplier with the same name already exists
        if (Supplier::where('name', $request->name)->where('id', '!=', $request->id)->exists()) {
            session()->flash("alert-danger", "Supplier with this name already exists!");
            return back();
        }

        $supplier = Supplier::find($request->id);
        $supplier->name = $request->name;
        $supplier->contact_person = $request->contact_person;
        $supplier->address = $request->address;
        $supplier->mobile = $request->mobile;
        $supplier->email = $request->email;

        try {
            $supplier->save();
            session()->flash("alert-success", "Supplier updated Successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Error updating supplier!");
            return back();
        }
    }

    public function destroy(Request $request)
    {
        try {
            // Add transaction checking before deletion like in CustomerController
            $order_count = DB::table('orders')->where('supplier_id', $request->id)->count();
            $receiving_count = DB::table('inv_incoming_stock')->where('supplier_id', $request->id)->count();
            $total_count = $order_count + $receiving_count;

            if ($total_count > 0) {
                session()->flash("alert-danger", "Supplier has pending transactions!");
                return back();
            }

            Supplier::destroy($request->id);
            session()->flash("alert-success", "Supplier Deleted Successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Supplier In Use!");
            return back();
        }
    }
}