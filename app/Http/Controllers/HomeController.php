<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\SalesDetail;
use App\Setting;
use App\StockTracking;
use App\Store;
use DB;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    //login form
    public function login()
    {
        return view('auth.login');
    }


    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        /*return default store*/
        $default_store = Setting::where('id', 122)->value('value');
        $stores = Store::where('name', $default_store)->first();

        if ($stores != null) {
            $default_store_id = $stores->name;
        } else {
            $default_store_id = "Please Set Store";
        }
        session()->put('store', $default_store_id);

        $outOfStock = CurrentStock::where('quantity', 0)->groupby('product_id')->get();
        $outOfStock = $outOfStock->count();
        $outOfStockList = CurrentStock::where('quantity', 0)->groupby('product_id')->get();

        $fast_moving = StockTracking::select(DB::raw('sum(quantity) as quantity'), 'product_id', 'stock_id', 'updated_at')
            ->whereRaw('month(updated_at) = month(now())')
            ->where('movement', 'OUT')
            ->where('out_mode', 'Cash Sales')
            ->orderby('quantity', 'desc')
            ->groupby('product_id')
            ->get();
        $fast_moving = $fast_moving->count();

        $expired = CurrentStock::whereRaw('expiry_date <  date(now())')->count();

        $totalSales = SalesDetail::sum('amount');

        $days = DB::table('sale_details')
            ->select(DB::raw('date(sold_at)'))
            ->distinct()
            ->get();

        if ($days->count() == 0) {
            $avgDailySales = 0;
        } else {
            $avgDailySales = $totalSales / $days->count();
        }

        $todaySales = DB::table('sale_details')
            ->whereRaw('date(sold_at) = date(now())')
            ->wherenull('status')
            ->orwhere('status', '!=', 3)
            ->sum('amount');

        $totalDailySales = DB::table('sale_details')
            ->select(DB::raw('date(sold_at) date, sum(amount) value'))
            ->wherenull('status')
            ->orwhere('status', '!=', 3)
            ->groupBy(DB::raw('date(sold_at)'))
            ->limit('60')
            ->get();

        $totalMonthlySales = DB::table('sale_details')
            ->select(DB::raw("DATE_FORMAT(sold_at, '%b %y') month,sum(amount) amount"))
            ->wherenull('status')
            ->orwhere('status', '!=', 3)
            ->groupBy(DB::raw("DATE_FORMAT(sold_at, '%Y%m')"))
            ->get();

        $salesByCategory = DB::table('sale_details')
            ->select(DB::raw("category,sum(amount) amount"))
            ->wherenull('status')
            ->orwhere('status', '!=', 3)
            ->groupBy('category')
            ->get();


// dd($salesByCategory);
        return view('home', compact('outOfStock', 'outOfStockList', 'expired', 'avgDailySales', 'todaySales', 'totalDailySales',
            'totalMonthlySales', 'salesByCategory', 'fast_moving'));

    }

    public function showChangePasswordForm()
    {
        return view('auth.changepassword');
    }

    public function changePassword(Request $request)
    {
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error", "Your current password does not matches with the password you provided. Please try again.");
        }
        if (strcmp($request->get('current-password'), $request->get('new-password')) == 0) {
            //Current password and new password are same
            return redirect()->back()->with("error", "New Password cannot be same as your current password. Please choose a different password.");
        }
        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ]);
        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new-password'));
        $user->save();
        Session::flash("alert-success", "Password changed successfully!");
        return redirect()->route('home');
    }

    public function stockSummary(Request $request)
    {
        if ($request->ajax()) {

            switch ($request->summary_no) {
                case 1:
                    return $this->outOfStock($request);
                    break;
                case 2:
                    return $this->fastMoving($request);
                    break;
                case 3:
                    return $this->expired($request);
                    break;
                default;
            }
        }
    }

    public function outOfStock($request)
    {
        $columns = array(
            0 => 'product_id',
            1 => 'product_id',
            2 => 'batch_number'
        );


        $totalData = CurrentStock::where('quantity', 0)
            ->groupby('product_id')
            ->get();

        $totalFiltered = $totalData->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $out_of_stock = CurrentStock::where('quantity', 0)
                ->groupby('product_id')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $out_of_stock = CurrentStock::join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->orwhere('quantity', 0)
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->groupby('product_id')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = CurrentStock::join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->orwhere('quantity', 0)
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->groupby('product_id')
                ->get();
            $totalFiltered = $totalFiltered->count();
        }

        $data = array();
        if (!empty($out_of_stock)) {
            foreach ($out_of_stock as $adjustment) {

                $nestedData['name'] = $adjustment->product['name'];
                $nestedData['batch_number'] = $adjustment->batch_number;
                $nestedData['code'] = $adjustment->product['name'];
                $nestedData['product_id'] = $adjustment->product['id'];

                $data[] = $nestedData;

            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData->count()),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }

    public function fastMoving($request)
    {
        $columns = array(
            0 => 'product_id',
            1 => 'product_id',
            2 => 'batch_number'
        );


        $totalData = StockTracking::select(DB::raw('sum(quantity) as quantity'), 'stock_id', 'product_id', 'stock_id', 'updated_at')
            ->whereRaw('month(updated_at) = month(now())')
            ->where('movement', 'OUT')
            ->where('out_mode', 'Cash Sales')
            ->orderby('quantity', 'desc')
            ->groupby('product_id')
            ->get();

        $totalFiltered = $totalData->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $fast_moving = StockTracking::select(DB::raw('sum(quantity) as quantity'), 'stock_id', 'product_id', 'stock_id', 'updated_at')
                ->whereRaw('month(updated_at) = month(now())')
                ->where('movement', 'OUT')
                ->where('out_mode', 'Cash Sales')
                ->orderby('quantity', 'desc')
                ->groupby('product_id')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $fast_moving = StockTracking::select(DB::raw('sum(quantity) as quantity'), 'stock_id', 'product_id', 'stock_id', 'updated_at')
                ->join('inv_products', 'inv_products.id', '=', 'inv_stock_tracking.product_id')
                ->whereRaw('month(updated_at) = month(now())')
                ->where('movement', 'OUT')
                ->where('out_mode', 'Cash Sales')
                ->orderby('quantity', 'desc')
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->groupby('product_id')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = StockTracking::select(DB::raw('sum(quantity) as quantity'), 'stock_id', 'product_id', 'stock_id', 'updated_at')
                ->join('inv_products', 'inv_products.id', '=', 'inv_stock_tracking.product_id')
                ->whereRaw('month(updated_at) = month(now())')
                ->where('movement', 'OUT')
                ->where('out_mode', 'Cash Sales')
                ->orderby('quantity', 'desc')
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->groupby('product_id')
                ->get();
            $totalFiltered = $totalFiltered->count();
        }

        $data = array();
        if (!empty($fast_moving)) {
            foreach ($fast_moving as $adjustment) {

                $nestedData['name'] = $adjustment->currentStock['product']['name'];
                $nestedData['product_id'] = $adjustment->product_id;
                $nestedData['quantity'] = $adjustment->quantity;


                $data[] = $nestedData;

            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData->count()),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }

    public function expired($request)
    {
        $columns = array(
            0 => 'product_id',
            1 => 'product_id',
            2 => 'quantity',
            3 => 'expiry_date'

        );


        $totalData = CurrentStock::whereRaw('expiry_date <  date(now())')
            ->get();

        $totalFiltered = $totalData->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $out_of_stock = CurrentStock::select('name', 'quantity', 'inv_current_stock.id', 'product_id', 'expiry_date')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->whereRaw('expiry_date <  date(now())')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $out_of_stock = CurrentStock::select('name', 'quantity', 'inv_current_stock.id', 'product_id', 'expiry_date')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->orwhereRaw('expiry_date <  date(now())')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = CurrentStock::select('name', 'quantity', 'inv_current_stock.id', 'product_id', 'expiry_date')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->orwhereRaw('expiry_date <  date(now())')
                ->get();
            $totalFiltered = $totalFiltered->count();
        }

        $data = array();
        if (!empty($out_of_stock)) {
            foreach ($out_of_stock as $adjustment) {

                $nestedData['name'] = $adjustment->name;
                $nestedData['quantity'] = $adjustment->quantity;
                $nestedData['product_id'] = $adjustment->product_id;
                $nestedData['expiry_date'] = $adjustment->expiry_date;

                $data[] = $nestedData;

            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData->count()),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }

}
