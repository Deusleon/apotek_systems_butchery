<?php

namespace App\Http\Controllers;

use App\Supplier;
use Exception;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderby('id', 'DESC')->get();

        return view('masters.suppliers.index', compact("suppliers"));
    }

    public function store(Request $request)
    {
        $supplier = new Supplier;
        $supplier->name = $request->name;
        $supplier->contact_person = $request->contact_person;
        $supplier->address = $request->address;
        $supplier->mobile = $request->phone;
        $supplier->email = $request->email;
        $supplier->save();
        session()->flash("alert-success", "Supplier Added Successfully!");
        return back();
    }

    public function update(Request $request)
    {
        $supplier = Supplier::find($request->id);
        $supplier->name = $request->name;
        $supplier->contact_person = $request->contact_person;
        $supplier->address = $request->address;
        $supplier->mobile = $request->mobile;
        $supplier->email = $request->email;

        $supplier->save();

        session()->flash("alert-success", "Supplier Updated Successfully!");
        return back();
    }

    public function destroy(Request $request)
    {
        try {
            Supplier::destroy($request->id);
            session()->flash("alert-danger", "Supplier Deleted Successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Supplier In Use!");
            return back();
        }

    }
}
