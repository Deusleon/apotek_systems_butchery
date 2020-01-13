<?php

namespace App\Http\Controllers;

use App\Customer;
use DB;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    public function index()
    {
        $customers = Customer::orderBy('id', 'ASC')->get();
        return view('sales.customers.index', compact("customers"));

    }

    public function store(Request $request)
    {

        $customer = new Customer;
        $customer->name = $request->name;
        $customer->credit_limit = $request->credit_limit;
        $customer->address = $request->address;
        $customer->phone = $request->phone;
        $customer->email = $request->email;

        $customer->save();

        session()->flash("alert-success", "Customer Added Successfully!");
        return back();
    }


    public function update(Request $request)
    {
        $customer = Customer::find($request->id);
        $customer->name = $request->name;
        $customer->address = $request->address;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        if (!empty($request->credit_limit)) {
            $customer->credit_limit = $request->credit_limit;
        }

        $customer->save();

        session()->flash("alert-success", "Customer Updated Successfully!");
        return back();
    }


    public function destroy(Request $request)
    {
        Customer::find($request->id)->delete();
        session()->flash("alert-danger", "Customer Deleted successfully!");
        return back();
    }
}
