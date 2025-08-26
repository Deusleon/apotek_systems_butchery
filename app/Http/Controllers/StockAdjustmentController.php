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

class StockAdjustmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:View Stock Adjustment|Stock Adjustment', ['only' => ['index', 'show']]);
        $this->middleware('permission:Stock Adjustment', ['only' => ['create', 'store']]);
    }

    public function index()
    {
        $query = StockAdjustmentLog::with(['currentStock.product', 'user', 'store']);
        
        // Apply date filter if provided
        if (request('start_date') && request('end_date')) {
            $startDate = request('start_date') . ' 00:00:00';
            $endDate = request('end_date') . ' 23:59:59';
            
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        // Apply search filter if provided
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('currentStock.product', function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhere('reason', 'LIKE', "%{$search}%")
                ->orWhere('reference_number', 'LIKE', "%{$search}%");
            });
        }
        
        // Apply adjustment type filter if provided
        if (request('adjustment_type')) {
            $query->where('adjustment_type', request('adjustment_type'));
        }
        
        $adjustments = $query->latest()->paginate(15);
        
        return view('stock_management.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $stocks = CurrentStock::with(['product'])
            ->whereHas('product') // Only get stocks that have a valid product
            ->where('store_id', session('store_id', 1))
            ->get();
        
        // Debug the data
        Log::info('Stocks count: ' . $stocks->count()); 
        foreach ($stocks as $stock) {
            Log::info('Stock ID: ' . $stock->id);
            Log::info('Product relationship: ' . ($stock->product ? 'exists' : 'null'));
            if ($stock->product) {
                Log::info('Product name: ' . $stock->product->name);
            }
        }
        
        $reasons = AdjustmentReason::all();
        return view('stock_management.adjustments.create', compact('stocks', 'reasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'current_stock_id' => 'required|exists:inv_current_stock,id',
            'adjustment_quantity' => 'required|numeric',
            'adjustment_type' => 'required|in:increase,decrease',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Get current stock
            $currentStock = CurrentStock::findOrFail($request->current_stock_id);
            $previousQuantity = $currentStock->quantity;
            
            // Calculate new quantity
            $adjustmentQuantity = $request->adjustment_quantity;
            if ($request->adjustment_type === 'increase') {
                $newQuantity = $previousQuantity + $adjustmentQuantity;
            } else {
                $newQuantity = $previousQuantity - $adjustmentQuantity;
                
                // Check if we have enough stock for decrease
                if ($newQuantity < 0) {
                    return back()->with('error', 'Not enough stock available for adjustment. Current stock: ' . $previousQuantity);
                }
            }
            
            // Update current stock
            $currentStock->quantity = $newQuantity;
            $currentStock->save();
            
            // Create adjustment log with all necessary fields
            $adjustment = new StockAdjustmentLog();
            $adjustment->current_stock_id = $request->current_stock_id;
            $adjustment->user_id = Auth::id();
            $adjustment->store_id = session('store_id', 1); // Default to store 1 if not set
            $adjustment->previous_quantity = $previousQuantity;
            $adjustment->new_quantity = $newQuantity;
            $adjustment->adjustment_quantity = $adjustmentQuantity;
            $adjustment->adjustment_type = $request->adjustment_type;
            $adjustment->reason = $request->reason;
            $adjustment->notes = $request->notes;
            $adjustment->reference_number = 'ADJ-' . time(); // Generate a reference number
            $adjustment->save();
            
            // Add to stock tracking
            StockTracking::create([
                'product_id' => $currentStock->product_id,
                'store_id' => $currentStock->store_id,
                'quantity' => ($request->adjustment_type === 'increase' ? $adjustmentQuantity : -$adjustmentQuantity),
                'tracking_type' => 'adjustment',
                'tracking_id' => $adjustment->id,
                'user_id' => Auth::id(),
                'description' => 'Stock adjustment: ' . $request->reason
            ]);

            DB::commit();
            
            // Log the action
            Log::info('Stock adjustment created', [
                'user' => Auth::user()->name,
                'product_id' => $currentStock->product_id,
                'product_name' => $currentStock->product->name ?? 'Unknown Product',
                'adjustment_type' => $request->adjustment_type,
                'adjustment_quantity' => $adjustmentQuantity,
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $newQuantity,
                'reason' => $request->reason
            ]);
            
            // Create detailed success message
            $productName = $currentStock->product->name ?? 'Unknown Product';
            $adjustmentType = ucfirst($request->adjustment_type);
            $successMessage = "Stock adjustment created successfully! {$adjustmentType} of {$adjustmentQuantity} units for '{$productName}'. Previous stock: {$previousQuantity}, New stock: {$newQuantity}. Reference: {$adjustment->reference_number}";
            
            return redirect()->route('stock-adjustments.index')
                           ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating stock adjustment: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return back()->with('error', 'Error creating stock adjustment: ' . $e->getMessage());
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
