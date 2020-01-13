<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\SalesDetail;
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
        $outOfStock = CurrentStock::where('quantity', 0)->count();

        $outOfStockList = CurrentStock::where('quantity', 0)->get();

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
            ->sum('amount');

        $totalDailySales = DB::table('sale_details')
            ->select(DB::raw('date(sold_at) date, sum(amount) value'))
            ->groupBy(DB::raw('date(sold_at)'))
            ->limit('60')
            ->get();

        $totalMonthlySales = DB::table('sale_details')
            ->select(DB::raw("DATE_FORMAT(sold_at, '%b %y') month,sum(amount) amount"))
            ->groupBy(DB::raw("DATE_FORMAT(sold_at, '%Y%m')"))
            ->get();

        $salesByCategory = DB::table('sale_details')
            ->select(DB::raw("category,sum(amount) amount"))
            ->groupBy('category')
            ->get();


// dd($salesByCategory);
        return view('home', compact('outOfStock', 'outOfStockList', 'expired', 'avgDailySales', 'todaySales', 'totalDailySales',
            'totalMonthlySales', 'salesByCategory'));

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

}
