<?php

namespace App\Http\Controllers;

use App\AdjustmentReason;
use App\Category;
use App\CurrentStock;
use App\PriceCategory;
use App\PriceList;
use App\Product;
use App\Setting;
use App\StockAdjustment;
use App\StockTracking;
use App\Store;
use App\StockAdjustmentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StockAdjustmentController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('permission:View Stock Adjustment|Stock Adjustment', ['only' => ['index', 'show']]);
    //     $this->middleware('permission:Stock Adjustment', ['only' => ['create', 'store']]);
    // }

    public function index()
    {
        if (!Auth()->user()->checkPermission('View Stock Adjustment')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        // Summary per product (grouped)
        $adjustments = StockAdjustmentLog::join('inv_current_stock', 'stock_adjustment_logs.current_stock_id', '=', 'inv_current_stock.id')
        ->join('inv_products', 'inv_current_stock.product_id', '=', 'inv_products.id')
        ->join('inv_categories', 'inv_products.category_id', '=', 'inv_categories.id')
        ->select(
            'stock_adjustment_logs.current_stock_id',
            'inv_products.id as product_id',
            'inv_products.name',
            'inv_products.brand',
            'inv_products.pack_size',
            'inv_products.sales_uom',
            'inv_categories.name as category_name',
            DB::raw('COUNT(stock_adjustment_logs.id) as adjustments_count'),
            DB::raw('SUM(stock_adjustment_logs.adjustment_quantity) as total_adjusted')
        )
        ->where('stock_adjustment_logs.store_id', $store_id)
        ->groupBy(
            // 'stock_adjustment_logs.current_stock_id',
            'inv_products.name',
            'inv_products.brand',
            'inv_products.pack_size',
            'inv_products.sales_uom',
            'inv_categories.name'
        )
        ->latest('stock_adjustment_logs.created_at')
        ->get();

         $detailed = StockAdjustmentLog::with(['currentStock.product.category', 'user'])
        ->where('store_id', $store_id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy(function($item) {
            return $item->currentStock->product_id; 
        })
        ->map(function($logs) {
            return $logs->groupBy(function($item) {
                return $item->currentStock->batch_number ?? 'N/A'; 
            });
        });

        return view('stock_management.adjustments.index', compact('adjustments', 'detailed'));
    }
      public function newAdjustment()
    {
        if (!Auth()->user()->checkPermission('Create Stock Adjustment')) {
            if (!Auth()->user()->checkPermission('View Stock Adjustment')) {
                abort(403, 'Access Denied');
            }
            return redirect()->route('stock-adjustments-history');
        }
        $store_id = current_store_id();
        $stocks = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->join('inv_categories','inv_products.category_id','=','inv_categories.id')
            ->select('inv_current_stock.id', 'inv_current_stock.product_id','inv_products.name','inv_products.sales_uom',
                'inv_products.brand', 'inv_products.pack_size',
                DB::raw('sum(inv_current_stock.quantity) as quantity'),
                'inv_categories.name as cat_name')
            ->where('inv_current_stock.store_id',$store_id)
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
            ->where('inv_current_stock.store_id',$store_id)
            ->groupBy(['inv_current_stock.product_id', 'inv_products.name', 
                'inv_products.brand', 'inv_products.pack_size', 'inv_categories.name'])
            ->orderBy('inv_products.id', 'desc')
            ->get();

        $detailed = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->join('inv_categories','inv_products.category_id','=','inv_categories.id')
            ->select('inv_current_stock.id', 'inv_current_stock.product_id', 'inv_products.name', 'inv_products.sales_uom', 'inv_current_stock.unit_cost',
                'inv_products.brand', 'inv_products.pack_size', 'inv_categories.name as cat_name',
                'inv_current_stock.quantity',
                'inv_current_stock.batch_number',
                'inv_current_stock.expiry_date')
            ->where('inv_current_stock.store_id',$store_id)
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
            ->where('inv_current_stock.store_id',$store_id)
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
            ->where('inv_current_stock.store_id', $store_id)
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
            ->where('inv_current_stock.store_id',$store_id)
            ->where('inv_current_stock.quantity','=',0)
            ->get();

        $stores = Store::all();
        $reasons = AdjustmentReason::all();

        return view('stock_management.adjustments.new_adjustment')->with([
            'allStocks' => $allStocks,
            'allDetailed' => $allDetailed,
            'stocks' => $stocks,
            'detailed' => $detailed,
            'outstock' => $outstock,
            'outDetailed' => $outDetailed,
            'stores' => $stores,
            'reasons' => $reasons
        ]);
    }
    public function create()
    {
        $stocks = CurrentStock::with(['product'])
            ->whereHas('product') // Only get stocks that have a valid product
            ->where('store_id', session('store_id', 1))
            ->get();
                
        $reasons = AdjustmentReason::all();
        return view('stock_management.adjustments.create', compact('stocks', 'reasons'));
    }

    public function store(Request $request)
    {
        if (!Auth()->user()->checkPermission('Create Stock Adjustment')) {
                abort(403, 'Access Denied');
        }
        $validator = Validator::make($request->all(), [
        'stock_id' => 'required|exists:inv_current_stock,id',
        'product_id' => 'required|exists:inv_products,id',
        'current_stock' => 'required|numeric|min:0',
        'new_quantity' => 'required|numeric|min:0',
        'reason' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors'  => $validator->errors()
        ], 422); 
    }
        // Log::info('Store Stock Adjustment Request:', $request->all());

        DB::beginTransaction();
        try {
            $currentStock = CurrentStock::findOrFail($request->stock_id);
            $previousQuantity = $currentStock->quantity;
            
            // Calculate new quantity
            $newQuantity = $request->new_quantity;
            
            if ($newQuantity > $previousQuantity) {
                $adjustmentType = 'increase';
                $adjustmentQuantity = $newQuantity - $previousQuantity;
            } else {
                $adjustmentType = 'decrease';
                $adjustmentQuantity = $previousQuantity - $newQuantity;

                // Check if we have enough stock for decrease
                if ($adjustmentType === 'decrease' && $adjustmentQuantity > $previousQuantity) {
                    return back()->with('error', 'Not enough stock available for adjustment. Current stock: ' . $previousQuantity);
                }
            }
            
            // Update current stock
            $currentStock->quantity = $newQuantity;
            $currentStock->save();
            
            // Create adjustment log with all necessary fields
            $adjustment = new StockAdjustmentLog();
            $adjustment->current_stock_id = $request->stock_id;
            $adjustment->user_id = Auth::id();
            $adjustment->store_id = current_store_id();
            $adjustment->previous_quantity = $previousQuantity;
            $adjustment->new_quantity = $newQuantity;
            $adjustment->adjustment_quantity = $adjustmentQuantity;
            $adjustment->adjustment_type = $adjustmentType;
            $adjustment->reason = $request->reason;
            // $adjustment->notes = $request->notes;
            $adjustment->reference_number = 'ADJ-' . time(); // Generate a reference number
            $adjustment->save();

            // Create adjustment with all necessary fields
            $adjust = new StockAdjustment();
            $adjust->stock_id = $request->stock_id;
            $adjust->quantity = $adjustmentQuantity;
            $adjust->type = $adjustmentType;
            $adjust->reason = $request->reason;
            $adjust->description = $request->notes;
            $adjust->created_by = Auth::id();
            $adjust->created_at = now();
            $adjust->save();            

            // Add to stock tracking
            StockTracking::create([
                'stock_id' => $currentStock->id,
                'product_id' => $currentStock->product_id,
                'out_mode' => 'Stock adjustment: ' . $request->reason,
                'quantity' =>  $adjustmentQuantity,
                'store_id' => current_store_id(),
                'created_by' => Auth::id(),
                'updated_at' => date('Y-m-d'),
                'movement' => ($adjustmentType === 'increase' ? 'IN' : 'OUT'),
            ]);

            DB::commit();
            
            // Create detailed success message
            $productName = $currentStock->product->name ?? 'Unknown Product';
            $adjustmentType = ucfirst($adjustmentType);
            $successMessage = "Stock adjustment created successfully! {$adjustmentType} of {$adjustmentQuantity} units for '{$productName}'. Previous stock: {$previousQuantity}, New stock: {$newQuantity}. Reference: {$adjustment->reference_number}";
            
            return response()->json([
                'success' => true,
                'message' => $successMessage
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating stock adjustment: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error creating stock adjustment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(StockAdjustmentLog $adjustment)
    {
        $adjustment->load(['currentStock.product', 'user', 'store']);
        return view('stock_management.adjustments.show', compact('adjustment'));
    }

    public function stockAdjustment()
    {
        /*get default store*/
        $default_store = Auth::user()->store->name ?? 'Default Store';
        $stores = Store::where('name', $default_store)->first();

        if ($stores != null) {
            $default_store_id = $stores->id;
        } else {
            $default_store_id = 1;
        }

        $stores = Store::all();

        /*return in stock by default
         * */
        $current_stock = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
            ->groupBy('product_id')
            ->havingRaw(DB::raw('sum(quantity) > 0'))
            ->get();


        /*get default Price Category*/
        $default_sale_type = Setting::where('id', 125)->value('value');
        $sale_type = PriceCategory::where('name', $default_sale_type)->first();


        if ($sale_type != null) {
            $default_sale_type = $sale_type->id;
            // return $default_sale_type;
        } else {
            $default_sale_type = PriceCategory::first()->value('id');
        }


        $default_sale_type_name = PriceCategory::where('id', $default_sale_type)->value('name');
        // return $default_sale_type_name;
        $stock_detail = CurrentStock::all();
        $price_Category = PriceCategory::all();
        $products = Product::all();
        $sale_price = PriceList::all();
        $adjustment_reason = AdjustmentReason::all('reason');
        $categories = Category::all();

        return view('stock_management.stock_adjustment.index_adjustment')->with([
            'current_stock' => $current_stock,
            'stock_details' => $stock_detail,
            'stores' => $stores,
            'products' => $products,
            'categories' => $categories,
            'reasons' => $adjustment_reason,
            'default_sale_type' => $default_sale_type,
            'default_sale_type_name' => $default_sale_type_name,
            'price_categories' => $price_Category,
            'sale_prices' => $sale_price,
            'default_store_id' => $default_store_id
        ]);
    }

    public function update(Request $request)
    {

    }

    public function destroy(Request $request)
    {

    }

    public function allAdjustments(Request $request)
    {
        if ($request->has('from_date') && $request->has('to_date')) {
            $from = $request->from_date;
            $to = $request->to_date;
            
            $query = StockAdjustmentLog::with(['currentStock.product', 'user', 'store'])
                ->whereBetween(DB::raw('date(created_at)'), [
                    date('Y-m-d', strtotime($from)), 
                    date('Y-m-d', strtotime($to))
                ]);

            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->whereHas('currentStock.product', function($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('reason', 'LIKE', "%{$search}%")
                    ->orWhere('adjustment_type', 'LIKE', "%{$search}%");
                });
            }

            $totalData = $query->count();
            $totalFiltered = $totalData;

            if ($request->has('start') && $request->has('length')) {
                $query->offset($request->start)
                    ->limit($request->length);
            }

            if ($request->has('order')) {
                $columns = [
                    'created_at',
                    'adjustment_type',
                    'adjustment_quantity',
                    'reason'
                ];
                $order = $request->order[0];
                $query->orderBy($columns[$order['column']], $order['dir']);
            } else {
                $query->latest();
            }

            $adjustments = $query->get();

            $data = $adjustments->map(function($adjustment) {
                return [
                    'name' => $adjustment->currentStock->product->name,
                    'quantity_adjusted' => $adjustment->adjustment_quantity,
                    'date' => $adjustment->created_at->format('d-m-Y'),
                    'type' => $adjustment->adjustment_type,
                    'reason' => $adjustment->reason,
                    'description' => $adjustment->notes
                ];
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data
            ]);
        }

        // If no date range provided, return all adjustments
        $adjustments = StockAdjustmentLog::with(['currentStock.product', 'user', 'store'])
            ->latest()
            ->get();

        return response()->json($adjustments);
    }

}
