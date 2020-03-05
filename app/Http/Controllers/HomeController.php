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
                    $outOfStock = CurrentStock::where('quantity', 0)
                        ->groupby('product_id')
                        ->get();
                    foreach ($outOfStock as $value) {
                        $value->product;
                    }
                    return $outOfStock;
                    break;
                case 2:
                    //return all
                    $stock_tracking = StockTracking::select(DB::raw('sum(quantity) as quantity'), 'product_id', 'stock_id', 'updated_at')
                        ->whereRaw('month(updated_at) = month(now())')
                        ->where('movement', 'OUT')
                        ->where('out_mode', 'Cash Sales')
                        ->orderby('quantity', 'desc')
                        ->groupby('product_id')
                        ->get();

                    //return product object
                    foreach ($stock_tracking as $tracking) {
                        $tracking->currentStock->product;
                        $tracking->user;
                        $tracking->date = date('d-m-Y', strtotime($tracking->updated_at));
                    }
                    return $stock_tracking;
                    break;
                case 3:
                    $expired = CurrentStock::whereRaw('expiry_date <  date(now())')->get();
                    foreach ($expired as $value) {
                        $value->product;
                    }
                    return $expired;
                    break;
                default;
            }
        }
    }

}
