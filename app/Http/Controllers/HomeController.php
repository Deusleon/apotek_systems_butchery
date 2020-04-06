<?php

namespace App\Http\Controllers;

use App\CommonFunctions;
use App\CurrentStock;
use App\Expense;
use App\GoodsReceiving;
use App\SalesDetail;
use App\Setting;
use App\Store;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $fast_moving = DB::table('sale_details')->select('receipt_number', 'product_name',
            DB::raw('count(product_name) as occurrence'), 'product_id')
            ->whereRaw('date(sold_at) >= date(now()) - interval 90 day')
            ->groupBy('receipt_number', 'product_name')
            ->get();

        $fast_moving = $this->fastMovingCalculation($fast_moving);

        if ($fast_moving != []) {
            $moving_item = 0;
            foreach ($fast_moving as $moving) {
                $moving_item = $moving_item + $moving['occurrence'];
            }

            $fast_moving = $moving_item;

        } else {
            $fast_moving = 0;
        }

        $expired = CurrentStock::where('quantity', '>', 0)->whereRaw('expiry_date <  date(now())')->count();

        $pharmacy_data = $this->pharmacyDashboard();
        $purchase_data = $this->purchaseDashboard();
        $expense_data = $this->expenseDashboard();

        return view('home', compact('outOfStock', 'outOfStockList', 'expired', 'fast_moving', 'pharmacy_data'
            , 'purchase_data', 'expense_data'));

    }

    private function fastMovingCalculation($test)
    {
        /*grouped data*/
        $ungrouped_result = [];
        $grouped_result = [];
        foreach ($test as $value) {
            array_push($ungrouped_result, array(
                'receipt_number' => $value->receipt_number,
                'product_id' => $value->product_id,
                'product_name' => $value->product_name,
                'occurrence' => $value->occurrence
            ));
        }

        foreach ($ungrouped_result as $val) {
            if (array_key_exists('receipt_number', $val)) {
                $grouped_result[$val['receipt_number']][] = $val;
            }
        }

        $sum_by_product_name = array();
        $sum_by_key = new CommonFunctions();
        foreach ($grouped_result as $value) {
            foreach ($value as $item) {
                $index = $sum_by_key->sumByKey($item['product_name'], $sum_by_product_name, 'product_name');
                if ($index < 0) {
                    $sum_by_product_name[] = $item;
                } else {
                    $sum_by_product_name[$index]['occurrence'] += $item['occurrence'];
                }
            }
        }

        return $sum_by_product_name;

    }

    private function pharmacyDashboard()
    {
        $data = array();

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
            ->whereRaw('date(sold_at) = date(now()) and (status != 3 or status is null)')
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

        $data['avgDailySales'] = $avgDailySales;
        $data['todaySales'] = $todaySales;
        $data['totalDailySales'] = $totalDailySales;
        $data['salesByCategory'] = $salesByCategory;
        $data['total_monthly'] = $totalMonthlySales;


        return $data;

    }

    private function purchaseDashboard()
    {
        $data = array();

        $totalPurchases = GoodsReceiving::sum('total_cost');

        $days = GoodsReceiving::select(DB::raw('date(created_at)'))
            ->distinct()
            ->get();

        if ($days->count() == 0) {
            $avgDailyPurchases = 0;
        } else {
            $avgDailyPurchases = $totalPurchases / $days->count();
        }

        $todayPurchases = GoodsReceiving::whereRaw('date(created_at) = date(now())')
            ->sum('total_cost');

        $totalDailyPurchase = GoodsReceiving::select(DB::raw('date(created_at) date, sum(total_cost) value'))
            ->groupBy(DB::raw('date(created_at)'))
            ->limit('60')
            ->get();

        $totalMonthlyPurchases = GoodsReceiving::select(DB::raw("DATE_FORMAT(created_at, '%b %y') month,sum(total_cost) amount"))
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y%m')"))
            ->get();

        $purchasesByCategory = GoodsReceiving::select(DB::raw("(inv_categories.name) category,sum(total_cost) amount"))
            ->join('inv_products', 'inv_products.id', '=', 'inv_incoming_stock.product_id')
            ->join('inv_categories', 'inv_categories.id', '=', 'inv_products.category_id')
            ->groupBy('inv_products.category_id')
            ->get();

        $data['avgDailyPurchases'] = $avgDailyPurchases;
        $data['todayPurchases'] = $todayPurchases;
        $data['totalDailyPurchases'] = $totalDailyPurchase;
        $data['purchasesByCategory'] = $purchasesByCategory;
        $data['total_monthly'] = $totalMonthlyPurchases;


        return $data;

    }

    private function expenseDashboard()
    {
        $data = array();

        $totalExpenses = Expense::sum('amount');

        $days = Expense::select(DB::raw('date(created_at)'))
            ->distinct()
            ->get();

        if ($days->count() == 0) {
            $avgDailyExpenses = 0;
        } else {
            $avgDailyExpenses = $totalExpenses / $days->count();
        }

        $todayExpenses = Expense::whereRaw('date(created_at) = date(now())')
            ->sum('amount');

        $totalDailyExpenses = Expense::select(DB::raw('date(created_at) date, sum(amount) value'))
            ->groupBy(DB::raw('date(created_at)'))
            ->limit('60')
            ->get();

        $totalMonthlyExpenses = Expense::select(DB::raw("DATE_FORMAT(created_at, '%b %y') month,sum(amount) amount"))
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y%m')"))
            ->get();

        $expensesByCategory = Expense::select(DB::raw("(acc_expense_categories.name) category,sum(amount) amount"))
            ->join('acc_expense_categories', 'acc_expense_categories.id', '=', 'acc_expenses.expense_category_id')
            ->groupBy('acc_expense_categories.name')
            ->get();

        $data['avgDailyExpenses'] = $avgDailyExpenses;
        $data['todayExpenses'] = $todayExpenses;
        $data['totalDailyExpenses'] = $totalDailyExpenses;
        $data['expensesByCategory'] = $expensesByCategory;
        $data['total_monthly'] = $totalMonthlyExpenses;


        return $data;

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
                $nestedData['category'] = $adjustment->product['category']['name'];

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
            1 => 'product_name',
            2 => 'occurrence'
        );

        $totalData = DB::table('sale_details')->select('receipt_number', 'product_name',
            DB::raw('count(product_name) as occurrence'), 'product_id')
            ->whereRaw('date(sold_at) >= date(now()) - interval 90 day')
            ->groupBy('receipt_number', 'product_name')
            ->get();

        $sum_by_product_name = $this->fastMovingCalculation($totalData);

        $totalFiltered = sizeof($sum_by_product_name);

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $fast_moving = DB::table('sale_details')->select('receipt_number', 'product_name',
                DB::raw('count(product_name) as occurrence'), 'product_id')
                ->whereRaw('date(sold_at) >= date(now()) - interval 90 day')
                ->groupBy('receipt_number', 'product_name')
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $fast_moving = DB::table('sale_details')->select('receipt_number', 'product_name',
                DB::raw('count(product_name) as occurrence'), 'product_id')
                ->whereRaw('date(sold_at) >= date(now()) - interval 90 day')
                ->orWhere('product_name', 'LIKE', "%{$search}%")
                ->groupBy('receipt_number', 'product_name')
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = DB::table('sale_details')->select('receipt_number', 'product_name',
                DB::raw('count(product_name) as occurrence'), 'product_id')
                ->whereRaw('date(sold_at) >= date(now()) - interval 90 day')
                ->orWhere('product_name', 'LIKE', "%{$search}%")
                ->groupBy('receipt_number', 'product_name')
                ->get();

            $sum_by_product_name = $this->fastMovingCalculation($totalFiltered);
            $totalFiltered = sizeof($sum_by_product_name);
        }

        $data = array();
        if (!empty($fast_moving)) {
            $sum_by_product_name = $this->fastMovingCalculation($fast_moving);
            $data = $sum_by_product_name;
        }

        $sort_column = array_column($data, 'occurrence');
        array_multisort($sort_column, SORT_DESC, $data);

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval(sizeof($sum_by_product_name)),
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


        $totalData = CurrentStock::where('quantity', '>', 0)
            ->whereRaw('expiry_date <  date(now())')
            ->get();

        $totalFiltered = $totalData->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $expired = CurrentStock::select('name', 'quantity', 'inv_current_stock.id', 'product_id', 'expiry_date')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->whereRaw('expiry_date <  date(now())')
                ->where('quantity', '>', 0)
                ->offset($start)
                ->limit($limit)
                ->orderby('expiry_date', 'desc')
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $expired = CurrentStock::select('name', 'quantity', 'inv_current_stock.id', 'product_id', 'expiry_date')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->orwhereRaw('expiry_date <  date(now())')
                ->where('quantity', '>', 0)
                ->offset($start)
                ->limit($limit)
                ->orderby('expiry_date', 'desc')
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = CurrentStock::select('name', 'quantity', 'inv_current_stock.id', 'product_id', 'expiry_date')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->orWhere('name', 'LIKE', "%{$search}%")
                ->orwhereRaw('expiry_date <  date(now())')
                ->where('quantity', '>', 0)
                ->get();
            $totalFiltered = $totalFiltered->count();
        }

        $data = array();
        if (!empty($expired)) {
            foreach ($expired as $item) {

                $nestedData['name'] = $item->name;
                $nestedData['quantity'] = $item->quantity;
                $nestedData['product_id'] = $item->product_id;
                $nestedData['expiry_date'] = $item->expiry_date;

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

    public function taskSchedule(Request $request)
    {
        if ($request->ajax()) {
            $commonFunction = new CommonFunctions();
            return $commonFunction->stockNotificationSchedule(Auth::user()->id);
        }
    }

    public function markAsRead(Request $request)
    {
        if ($request->ajax()) {
            $id = auth()->user()->unreadNotifications[0]->id;
            auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
            return 'marked_read';
        }

    }

}
