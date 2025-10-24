<?php

namespace App\Http\Controllers;

use App\AdjustmentReason;
use App\Category;
use App\CurrentStock;
use App\PriceCategory;
use App\PriceList;
use App\Product;
use App\Setting;
use App\Store;
use Dompdf\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CurrentStockController extends Controller
{

    public function currentStock()
    {
        if (!Auth()->user()->checkPermission('View Current Stock')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $expireSettings = Setting::where('id', 123)->value('value');
        $expireEnabled = $expireSettings === 'YES';
        $stocks = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->join('inv_categories','inv_products.category_id','=','inv_categories.id')
            ->select('inv_current_stock.product_id','inv_products.name','inv_products.sales_uom',
                'inv_products.brand', 'inv_products.pack_size',
                DB::raw('sum(inv_current_stock.quantity) as quantity'),
                'inv_categories.name as cat_name')
            // ->where('inv_current_stock.store_id',$store_id)
            ->when(!is_all_store(), function ($query) use ($store_id) {
                return $query->where('inv_current_stock.store_id', $store_id);
            })
            ->groupBy(['inv_current_stock.product_id', 'inv_products.name', 
                'inv_products.brand', 'inv_products.pack_size', 'inv_categories.name'])
            ->havingRaw(DB::raw('sum(quantity) > 0'))
            ->get();
            
        $allStocks = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->join('inv_categories','inv_products.category_id','=','inv_categories.id')
            ->select('inv_current_stock.id','inv_current_stock.product_id','inv_products.name','inv_products.sales_uom',
                'inv_products.brand', 'inv_products.pack_size',
                DB::raw('sum(inv_current_stock.quantity) as quantity'),
                'inv_categories.name as cat_name')
            // ->where('inv_current_stock.store_id',$store_id)
            ->when(!is_all_store(), function ($query) use ($store_id) {
                return $query->where('inv_current_stock.store_id', $store_id);
            })
            ->groupBy(['inv_current_stock.product_id', 'inv_products.name', 
                'inv_products.brand', 'inv_products.pack_size', 'inv_categories.name'])
            ->orderBy('inv_products.id', 'desc')
            ->get();

        $detailed = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->join('inv_categories','inv_products.category_id','=','inv_categories.id')
            ->select('inv_current_stock.product_id','inv_products.name', 'inv_products.sales_uom', 'inv_current_stock.unit_cost',
                'inv_products.brand', 'inv_products.pack_size', 'inv_categories.name as cat_name',
                'inv_current_stock.quantity',
                'inv_current_stock.batch_number',
                'inv_current_stock.expiry_date')
            // ->where('inv_current_stock.store_id',$store_id)
            ->when(!is_all_store(), function ($query) use ($store_id) {
                return $query->where('inv_current_stock.store_id', $store_id);
            })
            ->where('inv_current_stock.quantity','>',0)
            ->get();

        $allDetailed = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->join('inv_categories','inv_products.category_id','=','inv_categories.id')
            ->select('inv_current_stock.id', 'inv_current_stock.product_id','inv_products.name', 'inv_products.sales_uom', 'inv_current_stock.unit_cost',
                'inv_products.brand', 'inv_products.pack_size', 'inv_categories.name as cat_name',
                'inv_current_stock.quantity',
                'inv_current_stock.batch_number',
                'inv_current_stock.expiry_date')
            // ->where('inv_current_stock.store_id',$store_id)
            ->when(!is_all_store(), function ($query) use ($store_id) {
                return $query->where('inv_current_stock.store_id', $store_id);
            })
            ->orderBy('inv_products.id', 'desc')
            ->get();

        $outstock = DB::table('inv_current_stock')
            ->join('inv_products', 'inv_current_stock.product_id', '=', 'inv_products.id')
            ->join('inv_categories', 'inv_products.category_id', '=', 'inv_categories.id')
            ->select(
                'inv_current_stock.id',
                'inv_current_stock.product_id',
                'inv_products.name',
                'inv_products.sales_uom',
                'inv_products.brand',
                'inv_products.pack_size',
                'inv_categories.name as cat_name',
                DB::raw('sum(inv_current_stock.quantity) as quantity'),
            )
            // ->where('inv_current_stock.store_id', $store_id)
            ->when(!is_all_store(), function ($query) use ($store_id) {
                return $query->where('inv_current_stock.store_id', $store_id);
            })
            ->groupBy([
                'inv_current_stock.product_id',
                'inv_products.name',
                'inv_products.sales_uom',
                'inv_products.brand',
                'inv_products.pack_size',
                'inv_categories.name'
            ]
            )
            ->havingRaw('SUM(inv_current_stock.quantity) = 0')
            ->distinct()
            ->get();
            
        $outDetailed = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->join('inv_categories','inv_products.category_id','=','inv_categories.id')
            ->select('inv_current_stock.id','inv_current_stock.product_id','inv_products.name', 'inv_products.sales_uom', 'inv_current_stock.unit_cost',
                'inv_products.brand', 'inv_products.pack_size', 'inv_categories.name as cat_name',
                'inv_current_stock.quantity',
                'inv_current_stock.batch_number',
                'inv_current_stock.expiry_date')
            // ->where('inv_current_stock.store_id',$store_id)
            ->when(!is_all_store(), function ($query) use ($store_id) {
                return $query->where('inv_current_stock.store_id', $store_id);
            })
            ->where('inv_current_stock.quantity','=',0)
            ->get();

        $stores = Store::all();

        return view('stock_management.current_stock.current_stock')->with([
            'allStocks' => $allStocks,
            'allDetailed' => $allDetailed,
            'stocks' => $stocks,
            'detailed' => $detailed,
            'outstock' => $outstock,
            'outDetailed' => $outDetailed,
            'stores' => $stores,
            'expireEnabled' => $expireEnabled
        ]);
    }

    //Current Stock
    public function allStock()
    {
        $store_id = current_store_id();
        $expireSettings = Setting::where('id', 123)->value('value');
        $price_categories = PriceCategory::all();
        $expireEnabled = $expireSettings === 'YES';
        if(is_all_store()) {
        $stocks = DB::table('inv_current_stock')
            ->join('inv_products', 'inv_current_stock.product_id', '=', 'inv_products.id')
            ->join(DB::raw('(
                SELECT stock_id, MAX(created_at) as latest_date
                FROM sales_prices
                GROUP BY stock_id
            ) as sp_max'), 'inv_current_stock.id', '=', 'sp_max.stock_id')
            ->join('sales_prices', function($join) {
                $join->on('inv_current_stock.id', '=', 'sales_prices.stock_id')
                    ->on('sales_prices.created_at', '=', 'sp_max.latest_date');
            })
            ->select(
                'inv_current_stock.product_id',
                'inv_products.name',
                'inv_products.brand',
                'inv_products.pack_size',
                'inv_products.sales_uom',
                'sales_prices.price',
                DB::raw('SUM(inv_current_stock.quantity) as quantity'),
                DB::raw('SUM(inv_current_stock.quantity * inv_current_stock.unit_cost) as buying_price'),
                DB::raw('SUM(inv_current_stock.quantity * sales_prices.price) as selling_price'),
                DB::raw('SUM(inv_current_stock.quantity * sales_prices.price) - SUM(inv_current_stock.quantity * inv_current_stock.unit_cost) as profit')
            )
            ->where('inv_current_stock.store_id', $store_id)
            ->groupBy(
                ['inv_current_stock.product_id',
                'inv_products.name',
                'inv_products.brand',
                'inv_products.pack_size',
                'inv_products.sales_uom']
            )
            ->havingRaw('SUM(quantity) > 0')
            ->get();
        }else{
        $stocks = DB::table('inv_current_stock')
            ->join('inv_products', 'inv_current_stock.product_id', '=', 'inv_products.id')
            ->join(DB::raw('(
                SELECT stock_id, MAX(created_at) as latest_date
                FROM sales_prices
                GROUP BY stock_id
            ) as sp_max'), 'inv_current_stock.id', '=', 'sp_max.stock_id')
            ->join('sales_prices', function($join) {
                $join->on('inv_current_stock.id', '=', 'sales_prices.stock_id')
                    ->on('sales_prices.created_at', '=', 'sp_max.latest_date');
            })
            ->select(
                'inv_current_stock.product_id',
                'inv_current_stock.unit_cost',
                'inv_products.name',
                'inv_products.brand',
                'inv_products.pack_size',
                'inv_products.sales_uom',
                'sales_prices.price',
                DB::raw('SUM(inv_current_stock.quantity) as quantity'),
                DB::raw('SUM(inv_current_stock.quantity * inv_current_stock.unit_cost) as buying_price'),
                DB::raw('SUM(inv_current_stock.quantity * sales_prices.price) as selling_price'),
                DB::raw('SUM(inv_current_stock.quantity * sales_prices.price) - SUM(inv_current_stock.quantity * inv_current_stock.unit_cost) as profit')
            )
            ->where('inv_current_stock.store_id', $store_id)
            ->groupBy(
                ['inv_current_stock.product_id',
                'inv_products.name',
                'inv_products.brand',
                'inv_products.pack_size',
                'inv_products.sales_uom']
            )
            ->havingRaw('SUM(quantity) > 0')
            ->get();

        }


        $detailed = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->select('inv_current_stock.product_id','inv_products.name',
                'inv_current_stock.quantity',
                'inv_current_stock.batch_number',
                'inv_current_stock.expiry_date')
            ->where('inv_current_stock.store_id',$store_id)
            ->where('inv_current_stock.quantity','>',0)
            ->get();


        $outstock = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->select('inv_current_stock.product_id','inv_products.name',
                DB::raw('sum(inv_current_stock.quantity) as quantity'),
                'inv_current_stock.batch_number',
                'inv_current_stock.expiry_date')
            ->where('inv_current_stock.store_id',$store_id)
            ->groupBy(['inv_current_stock.product_id'])
            ->havingRaw(DB::raw('sum(quantity) = 0'))
            ->get();


        return view('stock_management.current_stock.current_stock_value')->with([
            'stocks'=>$stocks,
            'detailed'=>$detailed,
            'outstock'=>$outstock,
            'expireEnabled' => $expireEnabled,
            'price_categories' => $price_categories
        ]);
    }
    
    public function getStockValue(Request $request)
    {
        $store_id = current_store_id();
        $price_category = $request->price_category ?? 1;
        $price_categories = PriceCategory::all();

        try {
            // Subquery kupata latest inv_current_stock per product
            $latestStock = DB::table('inv_current_stock as ics1')
                ->select('ics1.product_id', 'ics1.id as latest_stock_id', 'ics1.unit_cost')
                ->whereRaw('ics1.id = (
                    SELECT ics2.id
                    FROM inv_current_stock as ics2
                    WHERE ics2.product_id = ics1.product_id
                    ' . (!is_all_store() ? 'AND ics2.store_id = ' . (int)$store_id : '') . '
                    ORDER BY ics2.created_at DESC, ics2.id DESC
                    LIMIT 1
                )');

            // Subquery kupata latest selling price per stock_id
            $latestPrice = DB::table('sales_prices as sp1')
                ->select('sp1.stock_id', 'sp1.price')
                ->where('sp1.price_category_id', $price_category)
                ->whereRaw('sp1.id = (
                    SELECT sp2.id
                    FROM sales_prices as sp2
                    WHERE sp2.stock_id = sp1.stock_id
                    AND sp2.price_category_id = ' . $price_category . '
                    ORDER BY sp2.created_at DESC, sp2.id DESC
                    LIMIT 1
                )');

            // Main query
            $stocks = DB::table('inv_current_stock as ics')
                ->join('inv_products as p', 'ics.product_id', '=', 'p.id')
                ->joinSub($latestStock, 'latest_stock', function($join) {
                    $join->on('ics.product_id', '=', 'latest_stock.product_id');
                })
                ->leftJoinSub($latestPrice, 'latest_price', function($join) {
                    $join->on('latest_stock.latest_stock_id', '=', 'latest_price.stock_id');
                })
                ->select(
                    'ics.product_id',
                    'p.name',
                    'p.brand',
                    'p.pack_size',
                    'p.sales_uom',
                    'latest_stock.unit_cost',
                    'latest_price.price',
                    DB::raw('SUM(ics.quantity) as quantity'),
                    DB::raw('SUM(ics.quantity * latest_stock.unit_cost) as buying_price'),
                    DB::raw('SUM(ics.quantity * COALESCE(latest_price.price, 0)) as selling_price'),
                    DB::raw('SUM(ics.quantity * COALESCE(latest_price.price, 0)) - SUM(ics.quantity * latest_stock.unit_cost) as profit')
                )
                ->groupBy(
                    ['ics.product_id',
                    'p.name',
                    'p.brand',
                    'p.pack_size',
                    'p.sales_uom',
                    'latest_stock.unit_cost']
                );

            if (!is_all_store()) {
                $stocks->where('ics.store_id', $store_id);
            }

            $stocks = $stocks->get();
            
            return view('stock_management.current_stock.current_stock_value')->with([
                'stocks' => $stocks,
                'price_categories' => $price_categories
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getStockValue: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while fetching stock data'
            ], 500);
        }
    }

    public function getOldStockValue(Request $request)
    {
        // dd($request->all());
        $store_id = current_store_id();
        $price_category = $request->price_category ?? 1;
        $date = $request->old_stock_date ?? \Carbon\Carbon::now()->toDateString(); // default today
        $price_categories = PriceCategory::all();

        try {
            // First check if table exists and has data
            $tableExists = DB::select("SHOW TABLES LIKE 'inv_old_stock_values'");
            if (empty($tableExists)) {
                Log::error('inv_old_stock_values table does not exist');
                return view('stock_management.current_stock.old_stock_value')->with([
                    'stocks' => collect(),
                    'price_categories' => $price_categories,
                    'selected_date' => $date,
                    'selected_price_category' => $price_category,
                    'error' => 'Old stock values table does not exist. Please run migrations.'
                ]);
            }

            // Get min and max dates from inv_old_stock_values, limited to yesterday
            $dateRange = DB::table('inv_old_stock_values')
                ->selectRaw('MIN(snapshot_date) as min_date, MAX(snapshot_date) as max_date')
                ->first();

            $yesterday = \Carbon\Carbon::yesterday()->toDateString();
            $min_date_raw = $dateRange->min_date ?? $yesterday;
            $max_date = min($dateRange->max_date ?? $yesterday, $yesterday);

            // Set min_date to January 1 of the min year
            $min_year = date('Y', strtotime($min_date_raw));
            $min_date = $min_year . '-01-01';

            $query = DB::table('inv_old_stock_values as os')
                ->join('inv_products as p', 'os.product_id', '=', 'p.id')
                ->select(
                    'os.product_id',
                    'p.name',
                    'p.brand',
                    'p.pack_size',
                    'p.sales_uom',
                    'os.buy_price',
                    'os.sell_price',
                    DB::raw('SUM(os.quantity) as quantity'),
                    DB::raw('SUM(os.quantity * os.buy_price) as buying_price'),
                    DB::raw('SUM(os.quantity * COALESCE(os.sell_price, 0)) as selling_price'),
                    DB::raw('SUM(os.quantity * COALESCE(os.sell_price, 0)) - SUM(os.quantity * os.buy_price) as profit')
                )
                ->where('os.snapshot_date', $date)
                ->groupBy(
                    [
                        'os.product_id',
                        'p.name',
                        'p.brand',
                        'p.pack_size',
                        'p.sales_uom',
                        'os.buy_price',
                        'os.sell_price'
                    ]
                );

            if ($price_category !== null) {
                $query->where('os.price_category_id', $price_category);
            } else {
                $query->whereNull('os.price_category_id');
            }

            if (!is_all_store()) {
                $query->where('os.store_id', $store_id);
            }

            $stocks = $query->get();

            return view('stock_management.current_stock.old_stock_value')->with([
                'stocks' => $stocks,
                'price_categories' => $price_categories,
                'selected_date' => $date,
                'selected_price_category' => $price_category,
                'min_date' => $min_date,
                'max_date' => $max_date
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getOldStockValue: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return view('stock_management.current_stock.old_stock_value')->with([
                'stocks' => collect(),
                'price_categories' => $price_categories,
                'selected_date' => $date,
                'selected_price_category' => $price_category,
                'error' => 'An error occurred while fetching old stock data: ' . $e->getMessage()
            ]);
        }
    }

    public function filterStockValue(Request $request)
    {

        $store_id = current_store_id();
        $from = $request->date_from;
        $to = $request->date_to;

        //When Dates are filtered
        if(isset($from) && isset($to)) {
            $stocks = DB::table('inv_current_stock')
                ->join('inv_products', 'inv_current_stock.product_id', '=', 'inv_products.id')
                ->join('sales_prices', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                ->select('inv_current_stock.product_id', 'inv_products.name', 'inv_products.brand', 'inv_products.pack_size', 'inv_products.sales_uom',
                    DB::raw('sum(inv_current_stock.quantity) as quantity'),
                    'inv_current_stock.batch_number',
                    'inv_current_stock.expiry_date',
                    'inv_current_stock.created_at',
                    'inv_current_stock.unit_cost',
                    'sales_prices.price',
                    DB::raw('inv_current_stock.quantity * inv_current_stock.unit_cost AS buying_price'),
                    DB::raw('inv_current_stock.quantity * sales_prices.price AS selling_price'),
                    DB::raw('sales_prices.price  - inv_current_stock.unit_cost AS unit_profit'),
                    DB::raw('(inv_current_stock.quantity * sales_prices.price) - (inv_current_stock.quantity * inv_current_stock.unit_cost) AS profit'))
                ->where('inv_current_stock.store_id',$store_id)
                ->groupBy(['inv_current_stock.product_id', 'inv_products.name', 'inv_products.brand', 'inv_products.pack_size', 'inv_products.sales_uom', 'inv_current_stock.batch_number', 'inv_current_stock.expiry_date', 'inv_current_stock.created_at', 'inv_current_stock.unit_cost', 'sales_prices.price'])
                ->havingRaw(DB::raw('sum(quantity) > 0'))
                ->get();

        }

        //When no date is filtered
        if(!isset($from) && !isset($to)) {
            $stocks = DB::table('inv_current_stock')
                ->join('inv_products', 'inv_current_stock.product_id', '=', 'inv_products.id')
                ->join('sales_prices', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                ->select('inv_current_stock.product_id', 'inv_products.name',
                    DB::raw('sum(inv_current_stock.quantity) as quantity'),
                    'inv_current_stock.batch_number',
                    'inv_current_stock.expiry_date',
                    'inv_current_stock.created_at',
                    'inv_current_stock.unit_cost',
                    'sales_prices.price',
                    DB::raw('inv_current_stock.quantity * inv_current_stock.unit_cost AS buying_price'),
                    DB::raw('inv_current_stock.quantity * sales_prices.price AS selling_price'),
                    DB::raw('sales_prices.price  - inv_current_stock.unit_cost AS unit_profit'),
                    DB::raw('(inv_current_stock.quantity * sales_prices.price) - (inv_current_stock.quantity * inv_current_stock.unit_cost) AS profit'))
                ->groupBy(['inv_current_stock.product_id'])
                ->havingRaw(DB::raw('sum(quantity) > 0'))
                ->get();
        }


        return $stocks;
    }

    //Old Stock
    public function oldStock()
    {
        $store_id = Auth::user()->store_id;
        $stocks = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->join('sales_prices','inv_current_stock.id','=','sales_prices.stock_id')
            ->select('inv_current_stock.product_id','inv_products.name',
                DB::raw('inv_current_stock.quantity as quantity'),
                'inv_current_stock.batch_number',
                'inv_current_stock.expiry_date',
                'inv_current_stock.created_at',
                'inv_current_stock.unit_cost',
                'sales_prices.price',
                DB::raw('inv_current_stock.quantity * inv_current_stock.unit_cost AS buying_price'),
                DB::raw('inv_current_stock.quantity * sales_prices.price AS selling_price'),
                DB::raw('sales_prices.price  - inv_current_stock.unit_cost AS unit_profit'),
                DB::raw('(inv_current_stock.quantity * sales_prices.price) - (inv_current_stock.quantity * inv_current_stock.unit_cost) AS profit'))
            ->where('inv_current_stock.store_id',$store_id)
            ->groupBy(['inv_current_stock.product_id'])
            ->havingRaw(DB::raw('sum(quantity) > 0'))
            ->get();

        $detailed = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->select('inv_current_stock.product_id','inv_products.name',
                'inv_current_stock.quantity',
                'inv_current_stock.batch_number',
                'inv_current_stock.expiry_date')
            ->where('inv_current_stock.store_id',$store_id)
            ->where('inv_current_stock.quantity','<',0)
            ->get();


        $outstock = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->select('inv_current_stock.product_id','inv_products.name',
                DB::raw('sum(inv_current_stock.quantity) as quantity'),
                'inv_current_stock.batch_number',
                'inv_current_stock.expiry_date')
            ->where('inv_current_stock.store_id',$store_id)
            ->groupBy(['inv_current_stock.product_id'])
            ->havingRaw(DB::raw('sum(quantity) = 0'))
            ->get();


        return view('stock_management.current_stock.old_stock_value')->with([
            'stocks'=>$stocks,
            'detailed'=>$detailed,
            'outstock'=>$outstock]);
    }

    public function currentStockApi(Request $request)
    {
        try {
            $store_id = $request->stores_id ?? Auth::user()->store_id;
            $category = $request->category ?? "1";
            $status = $request->status ?? "1";

            $query = DB::table('inv_current_stock')
                ->join('inv_products', 'inv_current_stock.product_id', '=', 'inv_products.id')
                ->join('inv_categories', 'inv_products.category_id', '=', 'inv_categories.id')
                ->leftJoin('sales_prices', function($join) {
                    $join->on('inv_current_stock.id', '=', 'sales_prices.stock_id')
                         ->where('sales_prices.status', '=', 1);
                })
                ->select(
                    'inv_current_stock.product_id',
                    'inv_products.name',
                    'inv_products.pack_size',
                    DB::raw('sum(inv_current_stock.quantity) as quantity'),
                    'inv_current_stock.expiry_date',
                    'inv_current_stock.batch_number',
                    'inv_categories.name as category_name',
                    DB::raw('CASE 
                        WHEN sum(inv_current_stock.quantity) > 0 THEN "In Stock"
                        WHEN sum(inv_current_stock.quantity) = 0 THEN "Out of Stock"
                        ELSE "Low Stock"
                    END as stock_status'),
                    DB::raw('COALESCE(inv_current_stock.stock_value, sum(inv_current_stock.quantity * COALESCE(sales_prices.price, inv_current_stock.unit_cost))) as stock_value')
                )
                ->where('inv_current_stock.store_id', $store_id)
                ->groupBy(
                    'inv_current_stock.product_id',
                    'inv_products.name',
                    'inv_products.pack_size',
                    'inv_current_stock.expiry_date',
                    'inv_current_stock.batch_number',
                    'inv_categories.name',
                    'inv_current_stock.stock_value'
                );

            if ($category != "1") {
                $query->where('inv_products.category_id', $category);
            }

            if ($status == "1") {
                $query->havingRaw('sum(quantity) > 0');
            } else {
                $query->havingRaw('sum(quantity) = 0');
            }

            $stocks = $query->get();

            return response()->json([
                'data' => $stocks
            ]);

        } catch (\Exception $e) {
            Log::error('Error in currentStockApi: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while fetching stock data'
            ], 500);
        }
    }

    public function index()
    {
        $stocks = CurrentStock::with(['product', 'store'])
            ->when(request('store_id'), function($query, $storeId) {
                return $query->where('store_id', $storeId);
            })
            ->when(request('search'), function($query, $search) {
                return $query->whereHas('product', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->paginate(15);

        $stocks->getCollection()->transform(function ($stock) {
            $stock->status = $stock->getStockStatus();
            return $stock;
        });

        return view('stock_management.current_stock.index', compact('stocks'));
    }

    public function edit($productId)
    {
        $store_id = Auth::user()->store_id;

        $product = Product::findOrFail($productId);

        $stocks = CurrentStock::where('product_id', $productId)
            ->where('inv_current_stock.store_id', $store_id)
            ->leftJoin('sales_prices', function($join) {
                $join->on('inv_current_stock.id', '=', 'sales_prices.stock_id')
                     ->where('sales_prices.status', '=', 1);
            })
            ->select(
                'inv_current_stock.id',
                'inv_current_stock.batch_number',
                'inv_current_stock.expiry_date',
                'inv_current_stock.quantity',
                'inv_current_stock.unit_cost',
                'sales_prices.price',
                'sales_prices.id as sales_id'
            )
            ->get();

        return response()->json([            'product' => $product,
            'stocks' => $stocks
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inv_current_stock,id',
            'items.*.batch_number' => 'nullable|string',
            'items.*.expiry_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'The given data was invalid.', 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            foreach ($request->items as $itemData) {
                $stock = CurrentStock::find($itemData['id']);

                // Only update batch_number and expiry_date fields
                if (isset($itemData['batch_number'])) {
                    $stock->batch_number = $itemData['batch_number'];
                }
                if (isset($itemData['expiry_date'])) {
                    $stock->expiry_date = $itemData['expiry_date'];
                }

                $stock->save();
            }

            DB::commit();

            return response()->json(['message' => 'Stock details updated successfully!']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock Update Error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating the stock details.'], 500);
        }
    }

    public function filter(Request $request)
    {
        if ($request->ajax()) {

            $max_prices = array();
            $products = PriceList::where('price_category_id', $request->get("val"))
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->where('quantity', '>', 0)
                ->select('inv_products.id as id', 'name')
                ->groupBy('product_id')
                ->get();

            foreach ($products as $product) {
                $data = PriceList::select('stock_id', 'price')->where('price_category_id', $request->get("val"))
                    ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                    ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                    ->orderBy('stock_id', 'desc')
                    ->where('product_id', $product->id)
                    ->first('price');

                $quantity = CurrentStock::where('product_id', $product->id)->sum('quantity');

                array_push($max_prices, array(
                    'name' => $data->currentStock['product']['name'],
                    'unit_cost' => $data->currentStock['unit_cost'],
                    'price' => $data->price,
                    'quantity' => $quantity,
                    'id' => $data->stock_id,
                    'product_id' => $product->id
                ));

            }

            return $max_prices;

        }

    }

    public function currentStockDetail(Request $request)
    {

        if ($request->ajax()) {

            $current_stock = CurrentStock::where('product_id', '=', $request->get("val"))
                ->where('store_id', $request->store_id)
                ->get();

            foreach ($current_stock as $item) {
                $item->product;
            }

            return json_decode($current_stock, true);
        }
    }

    public function currentStockPricing(Request $request)
    {

        if ($request->ajax()) {
            if ($request->bulk_adjust) {

                $max_prices = [];

                $quantity = CurrentStock::where('product_id', $request->get("val"))
                    ->where('store_id', $request->store_id)
                    ->sum('quantity');

                $current_stock = CurrentStock::where('product_id', '=', $request->get("val"))
                    ->where('store_id', $request->store_id)
                    ->orderby('id', 'desc')
                    ->first();

                $stock_id = $current_stock->id;

                array_push($max_prices, array(
                    'name' => $current_stock->product->name,
                    'unit_cost' => $current_stock->currentStock['unit_cost'],
                    'price' => $current_stock->price,
                    'quantity' => $quantity,
                    'stock_id' => $stock_id,
                    'product_id' => $request->get("val")
                ));


                return $max_prices;


            }

            if (!$request->bulk_adjust) {
                $current_stock = CurrentStock::where('product_id', '=', $request->get("val"))
                    ->where('store_id', $request->store_id)
                    ->orderby('id', 'desc')
                    ->first();

                if ($current_stock != null) {
                    $current_stock->product;
                }

                return json_decode($current_stock, true);

            }


        }
    }

    public function allInStock(Request $request)
    {

        if ($request->status == 1) {
            $columns = array(
                0 => 'product_id',
                1 => 'quantity',
                2 => 'product_id',
            );

            /*count for that category*/
            if ($request->category != 0) {
                $totalData = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
                    ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                    ->where('inv_products.category_id', $request->category)
                    ->where('inv_current_stock.store_id', $request->store_id)
                    ->groupBy('inv_current_stock.product_id')
                    ->havingRaw(DB::raw('sum(quantity) > 0'))
                    ->get()
                    ->count();
            } else {
                $totalData = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
                    ->where('store_id', $request->store_id)
                    ->groupBy('product_id')
                    ->havingRaw(DB::raw('sum(quantity) > 0'))
                    ->get()
                    ->count();
            }


            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if (empty($request->input('search.value'))) {
//                $status = $request->status;
                if ($request->category != 0) {
                    $stocks = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
                        ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                        ->where('inv_products.category_id', $request->category)
                        ->where('inv_current_stock.store_id', $request->store_id)
                        ->groupBy('inv_current_stock.product_id')
                        ->havingRaw(DB::raw('sum(quantity) > 0'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
                } else {
                    $stocks = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
                        ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                        ->where('inv_current_stock.store_id', $request->store_id)
                        ->groupBy('inv_current_stock.product_id')
                        ->havingRaw(DB::raw('sum(quantity) > 0'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
                }


            } else {
                $search = $request->input('search.value');

                if ($request->category != 0) {
                    $stocks = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
                        ->where('quantity', 'LIKE', "%{$search}%")
                        ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                        ->orWhere('inv_products.name', 'LIKE', "%{$search}%")
                        ->where('category_id', $request->category)
                        ->where('store_id', $request->store_id)
                        ->groupBy('product_id')
                        ->havingRaw(DB::raw('sum(quantity) > 0'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                    $totalFiltered = CurrentStock::where('quantity', 'LIKE', "%{$search}%")
                        ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                        ->orWhere('inv_products.name', 'LIKE', "%{$search}%")
                        ->where('category_id', $request->category)
                        ->where('store_id', $request->store_id)
                        ->count();
                } else {
                    $stocks = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
                        ->where('quantity', 'LIKE', "%{$search}%")
                        ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                        ->orWhere('inv_products.name', 'LIKE', "%{$search}%")
                        ->where('store_id', $request->store_id)
                        ->groupBy('product_id')
                        ->havingRaw(DB::raw('sum(quantity) > 0'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                    $totalFiltered = CurrentStock::where('quantity', 'LIKE', "%{$search}%")
                        ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                        ->orWhere('inv_products.name', 'LIKE', "%{$search}%")
                        ->where('store_id', $request->store_id)
                        ->count();
                }

            }

            $data = array();
            if (!empty($stocks)) {
                foreach ($stocks as $stock) {
                    $nestedData['name'] = $stock->product['name'];
                    $nestedData['quantity'] = $stock->quantity;
                    $nestedData['product_id'] = $stock->product_id;
                    $data[] = $nestedData;

                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
            );

            echo json_encode($json_data);
        } else {
            $columns = array(
                0 => 'product_id',
                1 => 'quantity',
                2 => 'product_id',
            );

            /*category count*/
            if ($request->category != 0) {
                $totalData = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
                    ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                    ->where('category_id', $request->category)
                    ->where('store_id', $request->store_id)
                    ->groupBy('product_id')
                    ->havingRaw(DB::raw('sum(quantity) <= 0'))
                    ->get()
                    ->count();
            } else {
                $totalData = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
                    ->where('store_id', $request->store_id)
                    ->groupBy('product_id')
                    ->havingRaw(DB::raw('sum(quantity) <= 0'))
                    ->get()
                    ->count();
            }

            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if (empty($request->input('search.value'))) {
                if ($request->category != 0) {
                    $stocks = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
                        ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                        ->where('category_id', $request->category)
                        ->where('store_id', $request->store_id)
                        ->groupBy('product_id')
                        ->havingRaw(DB::raw('sum(quantity) <= 0'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
                } else {
                    $stocks = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
                        ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                        ->where('store_id', $request->store_id)
                        ->groupBy('product_id')
                        ->havingRaw(DB::raw('sum(quantity) <= 0'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
                }
            } else {
                $search = $request->input('search.value');

                if ($request->category != 0) {
                    $stocks = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
                        ->where('quantity', 'LIKE', "%{$search}%")
                        ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                        ->orWhere('inv_products.name', 'LIKE', "%{$search}%")
                        ->where('category_id', $request->category)
                        ->where('store_id', $request->store_id)
                        ->groupBy('product_id')
                        ->havingRaw(DB::raw('sum(quantity) <= 0'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                    $totalFiltered = CurrentStock::where('quantity', 'LIKE', "%{$search}%")
                        ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                        ->orWhere('inv_products.name', 'LIKE', "%{$search}%")
                        ->where('category_id', $request->category)
                        ->where('store_id', $request->store_id)
                        ->count();
                } else {
                    $stocks = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
                        ->where('quantity', 'LIKE', "%{$search}%")
                        ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                        ->orWhere('inv_products.name', 'LIKE', "%{$search}%")
                        ->where('store_id', $request->store_id)
                        ->groupBy('product_id')
                        ->havingRaw(DB::raw('sum(quantity) <= 0'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                    $totalFiltered = CurrentStock::where('quantity', 'LIKE', "%{$search}%")
                        ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                        ->orWhere('inv_products.name', 'LIKE', "%{$search}%")
                        ->where('store_id', $request->store_id)
                        ->count();
                }

            }

            $data = array();
            if (!empty($stocks)) {
                foreach ($stocks as $stock) {
                    $nestedData['name'] = $stock->product['name'];
                    $nestedData['quantity'] = $stock->quantity;
                    $nestedData['product_id'] = $stock->product_id;
                    $data[] = $nestedData;

                }
            }

            $json_data = array(
                "draw" => intval($request->input('draw')),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
            );

            echo json_encode($json_data);
        }

    }
    public function showPricing($id)
    {
        $stock = CurrentStock::with(['product', 'priceList' => function($query) {
            $query->where('status', 1);
        }])->findOrFail($id);

        $priceList = $stock->priceList->first();
        $priceCategories = PriceCategory::where('status', 1)->get();
        $priceCategory = $priceList ? $priceList->priceCategory : null;
        
        $calculatedPrice = null;
        if ($priceList && !$priceList->is_custom) {
            $calculatedPrice = $stock->unit_cost * (1 + ($priceCategory->default_markup_percentage / 100));
        }

        $priceHistory = null;
        if (auth()->user()->can('view price history')) {
            $priceHistory = PriceList::where('stock_id', $id)
                ->with(['priceCategory', 'overriddenBy'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('stock_management.current_stock.pricing', compact(
            'stock',
            'priceList',
            'priceCategories',
            'priceCategory',
            'calculatedPrice',
            'priceHistory'
        ));
    }

    public function updatePrice(Request $request)
    {
        $request->validate([
            'stock_id' => 'required|exists:inv_current_stock,id',
            'price_category_id' => 'required|exists:price_categories,id',
            'price_type' => 'required|in:default,custom',
            'price' => 'required|numeric|min:0',
            'override_reason' => 'required_if:price_type,custom'
        ]);

        try {
            DB::beginTransaction();

            $stock = CurrentStock::findOrFail($request->stock_id);
            $priceCategory = PriceCategory::findOrFail($request->price_category_id);

            // Deactivate old price
            PriceList::where('stock_id', $stock->id)
                    ->where('status', 1)
                    ->update(['status' => 0]);

            // Create new price
            $priceList = new PriceList([
                'stock_id' => $stock->id,
                'price_category_id' => $priceCategory->id,
                'price' => $request->price,
                'is_custom' => $request->price_type === 'custom',
                'status' => 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            if ($request->price_type === 'custom') {
                if (!auth()->user()->can('override product prices')) {
                    throw new \Exception('You do not have permission to set custom prices.');
                }
                $priceList->override_reason = $request->override_reason;
                $priceList->override_by = auth()->id();
            } else {
                $priceList->default_markup_percentage = $priceCategory->default_markup_percentage;
            }

            $priceList->save();

            // Update stock value
            $stock->calculateStockValue();

            DB::commit();

            return redirect()->route('current-stock')
                           ->with('success', 'Price updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating price: ' . $e->getMessage());
            return back()->with('error', 'Error updating price: ' . $e->getMessage());
        }
    }

}
