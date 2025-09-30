<?php

namespace App\Http\Controllers;

use App\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerController extends Controller {

    public function index() {
        if ( !Auth()->user()->checkPermission( 'View Customers' ) ) {
            abort( 403, 'Access Denied' );
        }
        $customers = Customer::orderBy( 'id', 'ASC' )->get();
        foreach ( $customers as $customer ) {
            $customer_count = DB::table( 'sales' )->where( 'customer_id', $customer->id )->count();

            if ( $customer_count > 0 ) {
                $customer[ 'active_user' ] = 'has transactions';
            }

            if ( $customer_count == 0 ) {
                $customer[ 'active_user' ] = 'no transactions';
            }

        }

        return view( 'sales.customers.index', compact( 'customers' ) );

    }

    public function store( Request $request ) {

        $this->validate( $request, [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique( 'customers', 'name' )
                ->ignore( $request->id ),
            ],
            'email' => 'nullable|email',
        ], [
            'name.unique' => 'Customer name exist',
        ] );
        if ( $request->credit_limit>0 ) {
            $payment_term = '2';
        }

        if ( $request->credit_limit == 0 ) {
            $payment_term = '1';
        }

        $customer = new Customer;
        $customer->name = $request->name;
        $customer->credit_limit = $request->credit_limit;
        $customer->address = $request->address;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->tin = $request->tin;
        $customer->payment_term = $payment_term;

        $customer->save();

        if ( $request->ajax() ) {
            return response()->json( [ 'customer' => $customer ] );
        }

        session()->flash( 'alert-success', 'Customer Added Successfully!' );
        return back();
    }

    public function update( Request $request ) {
        $this->validate( $request, [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique( 'customers', 'name' )
                ->ignore( $request->id ),
            ],
            'email' => 'nullable|email',
        ], [
            'name.unique' => 'Customer name exist',
        ] );
        $customer = Customer::find( $request->id );
        $customer->name = $request->name;
        $customer->address = $request->address;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->tin = $request->tin;
        if ( !empty( $request->credit_limit ) ) {
            $customer->credit_limit = $request->credit_limit;
        }

        $customer->save();

        session()->flash( 'alert-success', 'Customer Updated Successfully!' );
        return back();
    }

    public function destroy( Request $request ) {
        try {
            $customer_count = DB::table( 'sales' )->where( 'customer_id', $request->id )->count();

            if ( $customer_count > 0 ) {
                session()->flash( 'alert-danger', 'Customer has pending transaction!' );
                return back();
            }
            Customer::find( $request->id )->delete();
            session()->flash( 'alert-success', 'Customer Deleted successfully!' );
            return back();
        } catch ( Exception $exception ) {
            session()->flash( 'alert-danger', 'Customer in use!' );
            return back();
        }
    }
}
