<?php

namespace App\Http\Controllers;

use App\CommonFunctions;
use App\CurrentStock;
use App\Expense;
use App\GoodsReceiving;
use App\SalesDetail;
use App\Sale;
use App\Product;
use App\Setting;
use App\Store;
use Dompdf\Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\TransportOrder;

class HomeController extends Controller {
    /**
    * Create a new controller instance.
    *
    * @return void
    */

    public function __construct() {
        $this->middleware( 'auth' );
    }

    /*Updating the store ID based on selection
    /made by logged in user
    */

    public function changeStore( Request $request ) {

        $user = auth()->user();

        if ( !$user || $user->store->name !== 'ALL' ) {
            return response()->json( [ 'error' => 'Not allowed' ], 403 );
        }

        $storeId = $request->input( 'store_id' );

        $store = Store::find( $storeId );
        if ( !$store ) {
            return response()->json( [ 'error' => 'Invalid store' ], 422 );
        }

        session( [ 'current_store_id' => $store->id, 'store' => $store->name ] );

        return redirect()->back()->with( 'success', "Branch changed to {$store->name}" );
    }

    //login form

    public function login() {
        return view( 'auth.login' );
    }

    /**
    * Show the application dashboard.
    *
    * @return Renderable
    */

    public function index() {

        /*return default store*/

        $store_id = current_store_id();
        $all_stores = Store::all();

        //Admin Users
        if ( auth()->user()->checkPermission( 'Manage All Branches' ) && $store_id == 1 ) {
            session()->put( 'store', 'ALL' );
            $outOfStock = CurrentStock::where( 'quantity', 0 )
            ->groupby( 'product_id' )->get();
            $outOfStock = $outOfStock->count();
            $outOfStockList = CurrentStock::where( 'quantity', 0 )
            ->groupby( 'product_id' )->get();

            $fast_moving = DB::table( 'sales_details' )->select( 'sales.receipt_number as receipt_number', 'inv_products.name as product_name', 'inv_products.brand as brand', 'inv_products.pack_size as pack_size', 'inv_products.sales_uom as sales_uom',
            DB::raw( 'count(inv_products.name) as occurrence' ), 'inv_products.id as product_id' )
            ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
            ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
            ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
            ->whereRaw( 'date(sales.date) >= date(now()) - interval 90 day' )
            ->groupBy( 'receipt_number', 'product_name' )
            ->get();

            $fast_moving = $this->fastMovingCalculation( $fast_moving );

            if ( $fast_moving != [] ) {
                $fast_moving = sizeof( $fast_moving );
            } else {
                $fast_moving = 0;
            }

            $expired = CurrentStock::where( 'quantity', '>', 0 )
            ->whereRaw( 'expiry_date <  date(now())' )
            ->count();

            $pharmacy_data = $this->pharmacyDashboard();
            $purchase_data = $this->purchaseDashboard();
            $expense_data = $this->expenseDashboard();
            $transport_data = $this->transportDashboard();

            return view( 'home', compact( [ 'outOfStock', 'outOfStockList', 'expired', 'fast_moving', 'pharmacy_data'
            , 'purchase_data', 'expense_data', 'all_stores', 'store_id', 'transport_data' ] ) );
        }

        //Others Users
        $default_store = Auth::user()->store->name ?? 'Default Store';
        $stores = Store::where( 'name', $default_store )->first();

        if ( $stores != null ) {
            $default_store_id = $stores->id;
        } else {
            $default_store_id = 0;
        }
        session()->put( 'store', $default_store_id );

        $outOfStock = CurrentStock::where( 'quantity', 0 )
        ->where( 'store_id', $store_id )->groupby( 'product_id' )->get();
        $outOfStock = $outOfStock->count();
        $outOfStockList = CurrentStock::where( 'quantity', 0 )
        ->where( 'store_id', $store_id )->groupby( 'product_id' )->get();

        $fast_moving = DB::table( 'sales_details' )->select( 'sales.receipt_number as receipt_number', 'inv_products.name as product_name', 'inv_products.brand as brand', 'inv_products.pack_size as pack_size', 'inv_products.sales_uom as sales_uom',
        DB::raw( 'count(inv_products.name) as occurrence' ), 'inv_products.id as product_id' )
        ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
        ->whereRaw( 'date(sales.date) >= date(now()) - interval 90 day' )
        ->where( 'inv_current_stock.store_id', $store_id )
        ->groupBy( 'receipt_number', 'product_name' )
        ->get();

        $fast_moving = $this->fastMovingCalculation( $fast_moving );

        if ( $fast_moving != [] ) {
            $fast_moving = sizeof( $fast_moving );
        } else {
            $fast_moving = 0;
        }

        $expired = CurrentStock::where( 'quantity', '>', 0 )
        ->where( 'store_id', $store_id )
        ->whereRaw( 'expiry_date <  date(now())' )
        ->count();

        $pharmacy_data = $this->pharmacyDashboard();
        $purchase_data = $this->purchaseDashboard();
        $expense_data = $this->expenseDashboard();
        $transport_data = $this->transportDashboard();

        return view( 'home', compact( [ 'outOfStock', 'outOfStockList', 'expired', 'fast_moving', 'pharmacy_data'
        , 'purchase_data', 'expense_data', 'all_stores', 'store_id', 'transport_data' ] ) );

    }

    private function fastMovingCalculation( $test ) {
        /*grouped data*/
        $ungrouped_result = [];
        $grouped_result = [];
        foreach ( $test as $value ) {
            array_push( $ungrouped_result, array(
                'receipt_number' => $value->receipt_number,
                'product_id' => $value->product_id,
                'product_name' => $value->product_name.' '.($value->brand.' ' ?? '').($value->pack_size ?? '').($value->sales_uom ?? ''),
                'occurrence' => $value->occurrence
            ) );
        }

        foreach ( $ungrouped_result as $val ) {
            if ( array_key_exists( 'receipt_number', $val ) ) {
                $grouped_result[ $val[ 'receipt_number' ] ][] = $val;
            }
        }

        $sum_by_product_name = array();
        $sum_by_key = new CommonFunctions();
        foreach ( $grouped_result as $value ) {
            foreach ( $value as $item ) {
                $index = $sum_by_key->sumByKey( $item[ 'product_name' ], $sum_by_product_name, 'product_name' );
                if ( $index < 0 ) {
                    $sum_by_product_name[] = $item;
                } else {
                    $sum_by_product_name[ $index ][ 'occurrence' ] += $item[ 'occurrence' ];
                }
            }
        }

        return $sum_by_product_name;

    }

    private function pharmacyDashboard() {
        $data = array();

        //Applying dashboard details per store
        $store_id = current_store_id();

        //Admin User
        if ( auth()->user()->checkPermission( 'Manage All Branches' ) && $store_id == 1 ) {
            $totalSales = DB::table( 'sales_details' )
            ->sum( 'amount' );

            $days = DB::table( 'sales_details' )
            ->select( DB::raw( 'date(sales.date)' ) )
            ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
            ->distinct()
            ->get();

            if ( $days->count() == 0 ) {
                $avgDailySales = 0;
            } else {
                $avgDailySales = $totalSales / $days->count();
            }

            $todaySales = DB::table( 'sales_details' )
            ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
            ->whereRaw( 'date(sales.date) = date(now()) and (status != 3 or status is null)' )
            ->sum( 'amount' );

            $totalDailySales = DB::table( 'sales_details' )
            ->select( DB::raw( 'date(sales.date) date, sum(amount) value' ) )
            ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
            ->where(function ($q) {
                $q->whereNull('status')
                ->orWhere('status', '!=', 3);
            })
            ->groupBy( DB::raw( 'date(sales.date)' ) )
            // ->limit( '60' )
            ->get();

            $totalMonthlySales = DB::table( 'sales_details' )

            ->select( DB::raw( "DATE_FORMAT(sales.date, '%b %y') month,sum(amount) amount" ) )
            ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
            ->where(function ($q) {
                $q->whereNull('status')
                  ->orWhere('status', '!=', 3);
            })
            ->groupBy( DB::raw( "DATE_FORMAT(sales.date, '%Y%m')" ) )
            ->get();

            $salesByCategory = DB::table( 'sales_details' )
            ->select( DB::raw( 'inv_categories.name as category,sum(amount) amount' ) )
            ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
            ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
            ->join( 'inv_categories', 'inv_categories.id', '=', 'inv_products.category_id' )
            ->wherenull( 'sales_details.status' )
            ->orwhere( 'sales_details.status', '!=', 3 )
            ->groupBy( 'category' )
            ->get();

            $data[ 'avgDailySales' ] = $avgDailySales;
            $data[ 'todaySales' ] = $todaySales;
            $data[ 'totalDailySales' ] = $totalDailySales;
            $data[ 'salesByCategory' ] = $salesByCategory;
            $data[ 'total_monthly' ] = $totalMonthlySales;

            return $data;
        }

        $totalSales = DB::table( 'sales_details' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->where( 'inv_current_stock.store_id', '=', $store_id )
        ->sum( 'amount' );

        $days = DB::table( 'sales_details' )
        ->select( DB::raw( 'date(sales.date)' ) )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->where( 'inv_current_stock.store_id', '=', $store_id )
        ->distinct()
        ->get();

        if ( $days->count() == 0 ) {
            $avgDailySales = 0;
        } else {
            $avgDailySales = $totalSales / $days->count();
        }

        $todaySales = DB::table( 'sales_details' )
        ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->whereRaw( 'date(sales.date) = date(now()) and (status != 3 or status is null)' )
        ->where( 'inv_current_stock.store_id', '=', $store_id )
        ->sum( 'amount' );

        $totalDailySales = DB::table( 'sales_details' )
        ->select( DB::raw( 'date(sales.date) date, sum(amount) value' ) )
        ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->where(function ($q) {
            $q->whereNull('status')
              ->orWhere('status', '!=', 3);
        })
        ->where( 'inv_current_stock.store_id', '=', $store_id )
        ->groupBy( DB::raw( 'date(sales.date)' ) )
        // ->limit( '60' )
        ->get();

        $totalMonthlySales = DB::table( 'sales_details' )

        ->select( DB::raw( "DATE_FORMAT(sales.date, '%b %y') month,sum(amount) amount" ) )
        ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->where(function ($q) {
            $q->whereNull('status')
              ->orWhere('status', '!=', 3);
        })
        ->where( 'inv_current_stock.store_id', '=', $store_id )
        ->groupBy( DB::raw( "DATE_FORMAT(sales.date, '%Y%m')" ) )
        ->get();

        $salesByCategory = DB::table( 'sales_details' )
        ->select( DB::raw( 'inv_categories.name as category,sum(amount) amount' ) )
        ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
        ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
        ->join( 'inv_categories', 'inv_categories.id', '=', 'inv_products.category_id' )
        ->wherenull( 'sales_details.status' )
        ->orwhere( 'sales_details.status', '!=', 3 )
        ->where( 'inv_current_stock.store_id', '=', $store_id )
        ->groupBy( 'category' )
        ->get();

        $data[ 'avgDailySales' ] = $avgDailySales;
        $data[ 'todaySales' ] = $todaySales;
        $data[ 'totalDailySales' ] = $totalDailySales;
        $data[ 'salesByCategory' ] = $salesByCategory;
        $data[ 'total_monthly' ] = $totalMonthlySales;

        return $data;

    }

    private function purchaseDashboard() {
        $data = array();

        $store_id = current_store_id();

        //Admin User
        if ( auth()->user()->checkPermission( 'Manage All Branches' ) && $store_id == 1 ) {

            $totalPurchases = GoodsReceiving::sum( 'total_cost' );

            $days = GoodsReceiving::select( DB::raw( 'date(created_at)' ) )
            ->distinct()
            ->get();

            if ( $days->count() == 0 ) {
                $avgDailyPurchases = 0;
            } else {
                $avgDailyPurchases = $totalPurchases / $days->count();
            }

            $todayPurchases = GoodsReceiving::whereRaw( 'date(created_at) = date(now())' )
            ->sum( 'total_cost' );

            $totalDailyPurchase = GoodsReceiving::select( DB::raw( 'date(created_at) date, sum(total_cost) value' ) )
            ->groupBy( DB::raw( 'date(created_at)' ) )
            ->limit( '60' )
            ->get();

            $totalMonthlyPurchases = GoodsReceiving::select( DB::raw( "DATE_FORMAT(created_at, '%b %y') month,sum(total_cost) amount" ) )
            ->groupBy( DB::raw( "DATE_FORMAT(created_at, '%Y%m')" ) )
            ->get();

            $purchasesByCategory = GoodsReceiving::select( DB::raw( '(inv_categories.name) category,sum(total_cost) amount' ) )
            ->join( 'inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id' )
            ->join( 'inv_categories', 'inv_categories.id', '=', 'inv_products.category_id' )
            ->groupBy( 'inv_products.category_id' )
            ->get();

            $data[ 'avgDailyPurchases' ] = $avgDailyPurchases;
            $data[ 'todayPurchases' ] = $todayPurchases;
            $data[ 'totalDailyPurchases' ] = $totalDailyPurchase;
            $data[ 'purchasesByCategory' ] = $purchasesByCategory;
            $data[ 'total_monthly' ] = $totalMonthlyPurchases;

            return $data;
        }

        $totalPurchases = GoodsReceiving::where( 'store_id', $store_id )
        ->sum( 'total_cost' );

        $days = GoodsReceiving::where( 'store_id', $store_id )
        ->select( DB::raw( 'date(created_at)' ) )
        ->distinct()
        ->get();

        if ( $days->count() == 0 ) {
            $avgDailyPurchases = 0;
        } else {
            $avgDailyPurchases = $totalPurchases / $days->count();
        }

        $todayPurchases = GoodsReceiving::whereRaw( 'date(created_at) = date(now())' )
        ->where( 'store_id', $store_id )
        ->sum( 'total_cost' );

        $totalDailyPurchase = GoodsReceiving::select( DB::raw( 'date(created_at) date, sum(total_cost) value' ) )
        ->where( 'store_id', $store_id )
        ->groupBy( DB::raw( 'date(created_at)' ) )
        ->limit( '60' )
        ->get();

        $totalMonthlyPurchases = GoodsReceiving::select( DB::raw( "DATE_FORMAT(created_at, '%b %y') month,sum(total_cost) amount" ) )
        ->where( 'store_id', $store_id )
        ->groupBy( DB::raw( "DATE_FORMAT(created_at, '%Y%m')" ) )
        ->get();

        $purchasesByCategory = GoodsReceiving::select( DB::raw( '(inv_categories.name) category,sum(total_cost) amount' ) )
        ->join( 'inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id' )
        ->join( 'inv_categories', 'inv_categories.id', '=', 'inv_products.category_id' )
        ->where( 'inv_incoming_stock.store_id', $store_id )
        ->groupBy( 'inv_products.category_id' )
        ->get();

        $data[ 'avgDailyPurchases' ] = $avgDailyPurchases;
        $data[ 'todayPurchases' ] = $todayPurchases;
        $data[ 'totalDailyPurchases' ] = $totalDailyPurchase;
        $data[ 'purchasesByCategory' ] = $purchasesByCategory;
        $data[ 'total_monthly' ] = $totalMonthlyPurchases;

        return $data;

    }

    private function expenseDashboard() {
        $data = array();

        $store_id = current_store_id();

        //Admin User
        if ( auth()->user()->checkPermission( 'Manage All Branches' ) && $store_id == 1 ) {
            $totalExpenses = Expense::sum( 'amount' );

            $days = Expense::select( DB::raw( 'date(created_at)' ) )
            ->distinct()
            ->get();

            if ( $days->count() == 0 ) {
                $avgDailyExpenses = 0;
            } else {
                $avgDailyExpenses = $totalExpenses / $days->count();
            }

            $todayExpenses = Expense::whereRaw( 'date(created_at) = date(now())' )
            ->sum( 'amount' );

            $totalDailyExpenses = Expense::select( DB::raw( 'date(created_at) date, sum(amount) value' ) )
            ->groupBy( DB::raw( 'date(created_at)' ) )
            ->limit( '60' )
            ->get();

            $totalMonthlyExpenses = Expense::select( DB::raw( "DATE_FORMAT(created_at, '%b %y') month,sum(amount) amount" ) )
            ->groupBy( DB::raw( "DATE_FORMAT(created_at, '%Y%m')" ) )
            ->get();

            $expensesByCategory = Expense::select( DB::raw( '(acc_expense_categories.name) category,sum(amount) amount' ) )
            ->join( 'acc_expense_categories', 'acc_expense_categories.id', '=', 'acc_expenses.expense_category_id' )
            ->groupBy( 'acc_expense_categories.name' )
            ->get();

            $data[ 'avgDailyExpenses' ] = $avgDailyExpenses;
            $data[ 'todayExpenses' ] = $todayExpenses;
            $data[ 'totalDailyExpenses' ] = $totalDailyExpenses;
            $data[ 'expensesByCategory' ] = $expensesByCategory;
            $data[ 'total_monthly' ] = $totalMonthlyExpenses;

            return $data;
        }

        $totalExpenses = Expense::where( 'store_id', $store_id )->sum( 'amount' );

        $days = Expense::select( DB::raw( 'date(created_at)' ) )
        ->where( 'store_id', $store_id )
        ->distinct()
        ->get();

        if ( $days->count() == 0 ) {
            $avgDailyExpenses = 0;
        } else {
            $avgDailyExpenses = $totalExpenses / $days->count();
        }

        $todayExpenses = Expense::whereRaw( 'date(created_at) = date(now())' )
        ->where( 'store_id', $store_id )
        ->sum( 'amount' );

        $totalDailyExpenses = Expense::select( DB::raw( 'date(created_at) date, sum(amount) value' ) )
        ->where( 'store_id', $store_id )
        ->groupBy( DB::raw( 'date(created_at)' ) )
        ->limit( '60' )
        ->get();

        $totalMonthlyExpenses = Expense::select( DB::raw( "DATE_FORMAT(created_at, '%b %y') month,sum(amount) amount" ) )
        ->where( 'store_id', $store_id )
        ->groupBy( DB::raw( "DATE_FORMAT(created_at, '%Y%m')" ) )
        ->get();

        $expensesByCategory = Expense::select( DB::raw( '(acc_expense_categories.name) category,sum(amount) amount' ) )
        ->where( 'store_id', $store_id )
        ->join( 'acc_expense_categories', 'acc_expense_categories.id', '=', 'acc_expenses.expense_category_id' )
        ->groupBy( 'acc_expense_categories.name' )
        ->get();

        $data[ 'avgDailyExpenses' ] = $avgDailyExpenses;
        $data[ 'todayExpenses' ] = $todayExpenses;
        $data[ 'totalDailyExpenses' ] = $totalDailyExpenses;
        $data[ 'expensesByCategory' ] = $expensesByCategory;
        $data[ 'total_monthly' ] = $totalMonthlyExpenses;

        return $data;

    }

    public function showChangePasswordForm() {
        return view( 'auth.changepassword' );
    }

    public function changePassword( Request $request ) {
        if ( !( Hash::check( $request->get( 'current-password' ), Auth::user()->password ) ) ) {
            // The passwords matches
            return redirect()->back()->with( 'error', 'Your current password does not matches with the password you provided. Please try again.' );
        }
        if ( strcmp( $request->get( 'current-password' ), $request->get( 'new-password' ) ) == 0 ) {
            //Current password and new password are same
            return redirect()->back()->with( 'error', 'New Password cannot be same as your current password. Please choose a different password.' );
        }
        $validatedData = $request->validate( [
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ] );
        //Change Password
        $user = Auth::user();
        $user->password = bcrypt( $request->get( 'new-password' ) );
        $user->save();
        Session::flash( 'alert-success', 'Password changed successfully!' );
        return redirect()->route( 'home' );
    }

    public function stockSummary( Request $request ) {
        $request[ 'store_id' ] = current_store_id();
        if ( $request->ajax() ) {

            switch ( $request->summary_no ) {
                case 1:
                return $this->outOfStock( $request );
                case 2:
                return $this->fastMoving( $request );
                case 3:
                return $this->expired( $request );
                default:
            }
        }
    }

    public function outOfStock( $request ) {
        $store_id = current_store_id();
        $columns = array(
            0 => 'product_id',
            1 => 'product_id',
            2 => 'batch_number'
        );

        $query = CurrentStock::where( 'quantity', 0 )
        ->groupby( 'product_id' );

        if(!is_all_store()){
            $query->where('store_id', $store_id);
        }

        $totalData = $query->get();

        $totalFiltered = $totalData->count();

        $limit = $request->input( 'length' );
        $start = $request->input( 'start' );
        $order = $columns[ $request->input( 'order.0.column' ) ];
        $dir = $request->input( 'order.0.dir' );

        if ( empty( $request->input( 'search.value' ) ) ) {
            $query = CurrentStock::where( 'quantity', 0 )
            ->groupby( 'product_id' )
            ->offset( $start )
            ->limit( $limit )
            ->orderBy( $order, $dir );
            if(!is_all_store()){
                $query->where('store_id', $store_id);
            }
            $out_of_stock = $query->get();
        } else {
            $search = $request->input( 'search.value' );

            $out_of_stock = CurrentStock::join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
            ->orwhere( 'quantity', 0 )
            ->orWhere( 'name', 'LIKE', "%{$search}%" )
            ->where( 'store_id', $request->input( 'store_id' ) )
            ->groupby( 'product_id' )
            ->offset( $start )
            ->limit( $limit )
            ->orderBy( $order, $dir )
            ->get();

            $totalFiltered = CurrentStock::join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
            ->orwhere( 'store_id', $request->input( 'store_id' ) )
            ->orwhere( 'quantity', 0 )
            ->orWhere( 'name', 'LIKE', "%{$search}%" )
            ->groupby( 'product_id' )
            ->get();
            $totalFiltered = $totalFiltered->count();
        }

        $data = array();
        if ( !empty( $out_of_stock ) ) {
            foreach ( $out_of_stock as $adjustment ) {
                $nestedData[ 'name' ] = $adjustment->product[ 'name' ].' '.($adjustment->product[ 'brand' ].' ' ?? '').($adjustment->product[ 'pack_size' ] ?? '').($adjustment->product[ 'sales_uom' ] ?? '');
                $nestedData[ 'batch_number' ] = $adjustment->batch_number;
                $nestedData[ 'brand' ] = $adjustment->product[ 'brand' ];
                $nestedData[ 'pack_size' ] = $adjustment->product[ 'pack_size' ];
                $nestedData[ 'sales_uom' ] = $adjustment->product[ 'sales_uom' ];
                $nestedData[ 'product_id' ] = $adjustment->product[ 'id' ];
                $nestedData[ 'category' ] = $adjustment->product[ 'category' ][ 'name' ] ?? '';

                $data[] = $nestedData;

            }
        }

        $json_data = array(
            'draw' => intval( $request->input( 'draw' ) ),
            'recordsTotal' => intval( $totalData->count() ),
            'recordsFiltered' => intval( $totalFiltered ),
            'data' => $data
        );

        echo json_encode( $json_data );
    }

    public function fastMoving( $request ) {
        $store_id = current_store_id();
         //Admin User
        try {
            $columns = array(
                0 => 'product_id',
                1 => 'product_name',
                2 => 'occurrence'
            );

            //Troubleshooting error on query
            $query = DB::table( 'sales_details' )->select( 'sales.receipt_number as receipt_number', 'inv_products.name as product_name', 'inv_products.brand as brand', 'inv_products.pack_size as pack_size', 'inv_products.sales_uom as sales_uom',
            DB::raw( 'count(inv_products.name) as occurrence' ), 'inv_products.id as product_id' )
            ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
            ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
            ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
            ->whereRaw( 'date(sales.date) >= date(now()) - interval 90 day' )
            // ->where( 'inv_current_stock.store_id', $request->input( 'store_id' ) )
            ->groupBy( [ 'sales.receipt_number', 'inv_products.name' ] );
            // ->get();
            if(!is_all_store()){
                $query->where('inv_current_stock.store_id', $store_id);
            }

            $totalData = $query->get();

            $sum_by_product_name = $this->fastMovingCalculation( $totalData );

            $totalFiltered = sizeof( $sum_by_product_name );

            $limit = $request->input( 'length' );
            $start = $request->input( 'start' );
            $order = $columns[ $request->input( 'order.0.column' ) ];
            $dir = $request->input( 'order.0.dir' );

            if ( empty( $request->input( 'search.value' ) ) ) {
                //Troubleshooting error on query
                $query = DB::table( 'sales_details' )->select( 'sales.receipt_number as receipt_number', 'inv_products.name as product_name', 'inv_products.brand as brand', 'inv_products.pack_size as pack_size', 'inv_products.sales_uom as sales_uom',
                DB::raw( 'count(inv_products.name) as occurrence' ), 'inv_products.id as product_id' )
                ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
                ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
                ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
                ->whereRaw( 'date(sales.date) >= date(now()) - interval 90 day' )
                // ->where( 'inv_current_stock.store_id', $request->input( 'store_id' ) )
                ->groupBy( [ 'sales.receipt_number', 'inv_products.name' ] )
                ->orderBy( $order, $dir );
                // ->get();
                if(!is_all_store()){
                    $query->where('inv_current_stock.store_id', $store_id);
                }
                $fast_moving = $query->get();
                
            } else {
                $search = $request->input( 'search.value' );

                //Troubleshooting error on query
                $query = DB::table( 'sales_details' )->select( 'sales.receipt_number as receipt_number', 'inv_products.name as product_name', 'inv_products.brand as brand', 'inv_products.pack_size as pack_size', 'inv_products.sales_uom as sales_uom',
                DB::raw( 'count(inv_products.name) as occurrence' ), 'inv_products.id as product_id' )
                ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
                ->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'sales_details.stock_id' )
                ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
                ->whereRaw( 'date(sales.date) >= date(now()) - interval 90 day' )
                ->orWhere( 'product_name', 'LIKE', "%{$search}%" )
                // ->orWhere( 'inv_current_stock.store_id', $request->input( 'store_id' ) )
                ->groupBy( [ 'sales.receipt_number', 'inv_products.name' ] )
                ->orderBy( $order, $dir );
                // ->get();
                if(!is_all_store()){
                    $query->where('inv_current_stock.store_id', $store_id);
                }
                $fast_moving = $query->get();

                $totalFiltered = DB::table( 'sales_details' )->select( 'receipt_number', 'product_name',
                DB::raw( 'count(product_name) as occurrence' ), 'product_id' )
                ->join( 'sales', 'sales.id', '=', 'sales_details.sale_id' )
                ->whereRaw( 'date(sales.date) >= date(now()) - interval 90 day' )
                ->orWhere( 'product_name', 'LIKE', "%{$search}%" )
                ->groupBy( [ 'sales.receipt_number', 'inv_products.name' ] )
                ->get();

                $sum_by_product_name = $this->fastMovingCalculation( $totalFiltered );
                $totalFiltered = sizeof( $sum_by_product_name );
            }

            $data = array();
            if ( !empty( $fast_moving ) ) {
                $sum_by_product_name = $this->fastMovingCalculation( $fast_moving );
                $data = $sum_by_product_name;
            }

            $sort_column = array_column( $data, 'occurrence' );
            array_multisort( $sort_column, SORT_DESC, $data );

            $json_data = array(
                'draw' => intval( $request->input( 'draw' ) ),
                'recordsTotal' => intval( sizeof( $sum_by_product_name ) ),
                'recordsFiltered' => intval( $totalFiltered ),
                'data' => $data
            );
            Log::info('Fast moving', $json_data);
            echo json_encode( $json_data );
        } catch ( Exception $e ) {
            Log::info( 'FastMovingError', [ 'ErrorMessage'=>$e ] );
        }
    }

    public function expired( $request ) {
        $store_id = current_store_id();
        
        $columns = array(
            0 => 'product_id',
            1 => 'product_id',
            2 => 'quantity',
            3 => 'expiry_date'

        );

        $query = CurrentStock::where( 'quantity', '>', 0 )
        ->whereRaw( 'expiry_date <  date(now())' );
        // ->get();
        if(!is_all_store()){
            $query->where('store_id', $store_id);
        }

        $totalData = $query->get();

        $totalFiltered = $totalData->count();

        $limit = $request->input( 'length' );
        $start = $request->input( 'start' );
        $order = $columns[ $request->input( 'order.0.column' ) ];
        $dir = $request->input( 'order.0.dir' );

        if ( empty( $request->input( 'search.value' ) ) ) {
            $query = CurrentStock::select( 'name', 'brand', 'pack_size', 'sales_uom', 'quantity', 'inv_current_stock.id', 'product_id', 'expiry_date' )
            ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
            ->whereRaw( 'expiry_date <  date(now())' )
            ->where( 'quantity', '>', 0 )
            // ->where( 'inv_current_stock.store_id', $request->input( 'store_id' ) )
            ->offset( $start )
            ->limit( $limit )
            ->orderby( 'expiry_date', 'desc' )
            ->orderBy( $order, $dir );
            // ->get();

            if(!is_all_store()){
                $query->where('inv_current_stock.store_id', $store_id);
            }
            $expired = $query->get();

        } else {
            $search = $request->input( 'search.value' );

            $query = CurrentStock::select( 'name', 'brand', 'pack_size', 'sales_uom', 'quantity', 'inv_current_stock.id', 'product_id', 'expiry_date' )
            ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
            ->orWhere( 'name', 'LIKE', "%{$search}%" )
            ->orwhereRaw( 'expiry_date <  date(now())' )
            ->where( 'quantity', '>', 0 )
            // ->where( 'inv_current_stock.store_id', $request->input( 'store_id' ) )
            ->offset( $start )
            ->limit( $limit )
            ->orderby( 'expiry_date', 'desc' )
            ->orderBy( $order, $dir );
            // ->get();

            if(!is_all_store()){
                $query->where('inv_current_stock.store_id', $store_id);
            }
            $expired = $query->get();

            $query = CurrentStock::select( 'name', 'brand', 'pack_size', 'sales_uom', 'quantity', 'inv_current_stock.id', 'product_id', 'expiry_date' )
            ->join( 'inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id' )
            ->orWhere( 'name', 'LIKE', "%{$search}%" )
            ->orwhereRaw( 'expiry_date <  date(now())' )
            ->where( 'quantity', '>', 0 );
            // ->where( 'inv_current_stock.store_id', $request->input( 'store_id' ) )
            // ->get();
            if(!is_all_store()){
                $query->where('inv_current_stock.store_id', $store_id);
            }
            $totalFiltered = $query->get();
            $totalFiltered = $totalFiltered->count();
        }

        $data = array();
        if ( !empty( $expired ) ) {
            foreach ( $expired as $item ) {
                $nestedData[ 'name' ] = $item->name.' '.($item->brand.' ' ?? '').($item->pack_size ?? '').($item->sales_uom ?? '');
                $nestedData[ 'quantity' ] = $item->quantity;
                $nestedData[ 'product_id' ] = $item->product_id;
                $nestedData[ 'expiry_date' ] = date_format($item->expiry_date, 'Y-m-d');

                $data[] = $nestedData;

            }
        }
                Log::info('Expired'.print_r($data,true));

        $json_data = array(
            'draw' => intval( $request->input( 'draw' ) ),
            'recordsTotal' => intval( $totalData->count() ),
            'recordsFiltered' => intval( $totalFiltered ),
            'data' => $data
        );

        echo json_encode( $json_data );
    }

    public function taskSchedule( Request $request ) {
        if ( $request->ajax() ) {
            $commonFunction = new CommonFunctions();
            return $commonFunction->stockNotificationSchedule( Auth::user()->id );
        }
    }

    private function transportDashboard() {
        $store_id = current_store_id();
        $query = TransportOrder::query();

        if ( $store_id && !Auth::user()->checkPermission( 'Manage All Branches' ) ) {
            $query->where( 'store_id', $store_id );
        }

        $orders = $query->get();

        $total_trips = $orders->count();
        $total_revenue = $orders->sum( 'transport_rate' );
        $pending_trips = $orders->where( 'status', 'draft' )->count();
        $in_transit_trips = $orders->where( 'status', 'confirmed' )->count();
        $delivered_trips = $orders->where( 'status', 'delivered' )->count();

        return [
            'total_trips' => $total_trips,
            'total_revenue' => $total_revenue,
            'pending_trips' => $pending_trips,
            'in_transit_trips' => $in_transit_trips,
            'delivered_trips' => $delivered_trips,
        ];
    }

    public function markAsRead( Request $request ) {
        if ( $request->ajax() ) {
            $id = auth()->user()->unreadNotifications[ 0 ]->id;
            auth()->user()->unreadNotifications->where( 'id', $id )->markAsRead();
            return 'marked_read';
        }

    }

}
