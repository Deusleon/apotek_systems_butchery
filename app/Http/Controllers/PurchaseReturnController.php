<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\GoodsReceiving;
use App\Product;
use App\PurchaseReturn;
use App\StockTracking;
use App\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseReturnController extends Controller
{
    public function index()
    {
        date_default_timezone_set('Africa/Nairobi');
        $date = date('m-d-Y');
        $suppliers = Supplier::orderby('name', 'ASC')->get();
        $products = Product::all();
        $expire_date = 'NO';

        return view('purchases.purchase_returns.index', compact('suppliers', 'products', 'expire_date'));
    }

    public function getPurchaseReturns(Request $request)
    {
        Log::info('getPurchaseReturns called', [
            'action' => $request->action,
            'date' => $request->date,
            'status' => $request->status,
            'goods_receiving' => $request->goods_receiving
        ]);

        if ($request->action == "approve") {
            $this->approve($request->goods_receiving);
        }
        if ($request->action == "reject") {
            $this->reject($request->goods_receiving);
        }
        if ($request->action == "check_return") {
            $return = PurchaseReturn::where('goods_receiving_id', $request->goods_receiving_id)->first();
            if ($return) {
                return response()->json([
                    'has_return' => true,
                    'return_id' => $return->id,
                    'return_quantity' => $return->quantity,
                    'reason' => $return->reason
                ]);
            } else {
                return response()->json(['has_return' => false]);
            }
        }

        if ($request->action == "check_status") {
            $goodsReceiving = GoodsReceiving::find($request->goods_receiving_id);
            if ($goodsReceiving) {
                return response()->json([
                    'status' => $goodsReceiving->status,
                    'has_pending_return' => in_array($goodsReceiving->status, [2, 3, 4, 5])
                ]);
            } else {
                return response()->json(['status' => null, 'has_pending_return' => false]);
            }
        }

        // Handle missing date parameters
        $from = isset($request->date[0]) ? date('Y-m-d', strtotime($request->date[0])) : date('Y-m-d', strtotime('-30 days'));
        $to = isset($request->date[1]) ? date('Y-m-d', strtotime($request->date[1])) : date('Y-m-d');
        $status = $request->status ?? 2; // Default to pending (2)

        Log::info('Date parameters', [
            'raw_date' => $request->date,
            'from' => $from,
            'to' => $to,
            'status' => $status,
            'current_date' => date('Y-m-d')
        ]);

        // First, let's check if there are any purchase returns at all
        $allReturns = PurchaseReturn::all();
        Log::info('Total purchase returns in database', ['count' => $allReturns->count()]);

        // Check goods receiving with status 2
        $pendingGoods = DB::table('inv_incoming_stock')->where('status', 2)->get();
        Log::info('Goods receiving with status 2', ['count' => $pendingGoods->count()]);

        $query = PurchaseReturn::with(['goodsReceiving' => function($q) {
            $q->with(['product', 'supplier']);
        }])
            ->where(DB::Raw("DATE(purchase_returns.date)"), '>=', $from)
            ->where(DB::Raw("DATE(purchase_returns.date)"), '<=', $to);

        if ($status == 4) {
            $query->whereHas('goodsReceiving', function($q) {
                $q->where('status', '=', 4);
            });
        } else if ($status == 3) {
            $query->whereHas('goodsReceiving', function($q) {
                $q->where(function($q2) {
                    $q2->where('status', '=', 3)
                       ->orWhere('status', '=', 5);
                });
            });
        } else {
            $query->whereHas('goodsReceiving', function($q) {
                $q->where('status', '=', 2);
            });
        }

        $returns = $query->orderBy('purchase_returns.updated_at', 'desc')->orderBy('purchase_returns.id', 'desc')->get();

        Log::info('Query results', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'count' => $returns->count()
        ]);

        // Transform the data to match the expected format
        $formattedReturns = [];
        foreach ($returns as $return) {
            $goodsReceiving = $return->goodsReceiving;
            if ($goodsReceiving) {
                $formattedReturns[] = [
                    'id' => $return->id,
                    'goods_receiving_id' => $return->goods_receiving_id,
                    'quantity' => $return->quantity, // Use the correct field from purchase_returns table
                    'reason' => $return->reason,
                    'date' => $return->date,
                    'created_at' => $return->created_at,
                    'goods_receiving' => [
                        'id' => $goodsReceiving->id,
                        'product_id' => $goodsReceiving->product_id,
                        'quantity' => $goodsReceiving->quantity, // Use the correct field from goods_receiving
                        'unit_cost' => $goodsReceiving->unit_cost,
                        'total_cost' => $goodsReceiving->total_cost,
                        'created_at' => $goodsReceiving->created_at,
                        'status' => $goodsReceiving->status,
                        'product' => $goodsReceiving->product ? [
                            'id' => $goodsReceiving->product->id,
                            'name' => $goodsReceiving->product->name,
                            'brand' => $goodsReceiving->product->brand,
                            'pack_size' => $goodsReceiving->product->pack_size,
                            'sales_uom' => $goodsReceiving->product->sales_uom
                        ] : null,
                        'supplier' => $goodsReceiving->supplier ? [
                            'id' => $goodsReceiving->supplier->id,
                            'name' => $goodsReceiving->supplier->name
                        ] : null
                    ]
                ];
            }
        }

        Log::info('Returning formatted data', ['count' => count($formattedReturns)]);
        return response()->json($formattedReturns);
    }

    public function approve($goodsReceivingData)
    {
        Log::info('Approving purchase return', $goodsReceivingData);

        // Get the purchase return record
        $purchaseReturn = PurchaseReturn::where('goods_receiving_id', $goodsReceivingData['id'])->first();
        if (!$purchaseReturn) {
            Log::error('Purchase return record not found for goods_receiving_id: ' . $goodsReceivingData['id']);
            return response()->json(['error' => 'Purchase return record not found'], 404);
        }

        $stock = CurrentStock::where('product_id', $goodsReceivingData['product_id'])
            ->where('store_id', current_store_id())
            ->first();

        if ($stock) {
            $stock->quantity -= $purchaseReturn->quantity;
            $stock->save();
            Log::info('Stock updated', ['product_id' => $goodsReceivingData['product_id'], 'new_quantity' => $stock->quantity]);
        }
        
        StockTracking::create([
            'stock_id' => $stock->id,
            'product_id' => $stock->product_id,
            'out_mode' => 'Purchase Return',
            'quantity' => $purchaseReturn->quantity,
            'store_id' => current_store_id(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'updated_at' => now()->format('Y-m-d'),
            'movement' => 'OUT',
        ]);

        $goodsReceiving = GoodsReceiving::find($goodsReceivingData['id']);

        // Get the original received quantity from the goods receiving record before any returns
        // Since we're approving a return, we need to calculate what the remaining quantity should be
        $returnedQty = $purchaseReturn->quantity;
        $currentQty = $goodsReceiving->quantity;

        // The remaining quantity after return approval
        $newqty = $currentQty - $returnedQty;

        // IF Partial return the values are re-calculated
        if ($newqty > 0) {
            $status = 5; // Partially returned
            // Recalculate total cost, total sell, and item profit proportionally
            $unitCost = $goodsReceiving->unit_cost;
            $sellPrice = $goodsReceiving->sell_price;
            $goodsReceiving->total_cost = $newqty * $unitCost;
            $goodsReceiving->total_sell = $newqty * $sellPrice;
            $goodsReceiving->item_profit = $goodsReceiving->total_sell - $goodsReceiving->total_cost;
            $goodsReceiving->quantity = $newqty;
        } else {
            $status = 3; // Fully returned
            $goodsReceiving->total_cost = 0;
            $goodsReceiving->total_sell = 0;
            $goodsReceiving->item_profit = 0;
            $goodsReceiving->quantity = 0;
        }

        $goodsReceiving->status = $status;
        $goodsReceiving->updated_by = Auth::User()->id;
        $goodsReceiving->updated_at = now();
        $goodsReceiving->save();

        // Update the purchase return record's updated_at for proper sorting
        $purchaseReturn->updated_at = now();
        $purchaseReturn->save();

        Log::info('Purchase return approved successfully');
        return response()->json(['success' => 'Purchase return approved successfully']);
    }

    public function reject($goodsReceivingData)
    {
        Log::info('Rejecting purchase return', $goodsReceivingData);

        $goodsReceiving = GoodsReceiving::find($goodsReceivingData['id']);
        $goodsReceiving->status = 4;
        $goodsReceiving->updated_by = Auth::User()->id;
        $goodsReceiving->updated_at = now();
        $goodsReceiving->save();

        // Update the purchase return record's updated_at for proper sorting
        $purchaseReturn = PurchaseReturn::where('goods_receiving_id', $goodsReceivingData['id'])->first();
        if ($purchaseReturn) {
            $purchaseReturn->updated_at = now();
            $purchaseReturn->save();
        }

        Log::info('Purchase return rejected successfully');
        return response()->json(['success' => 'Purchase return rejected successfully']);
    }

    public function store(Request $request)
    {
        Log::info('Creating purchase return', [
            'goods_receiving_id' => $request->goods_receiving_id,
            'quantity' => $request->quantity,
            'reason' => $request->reason
        ]);

        date_default_timezone_set('Africa/Nairobi');
        $date = date('Y-m-d,H:i:s');
        $goodsReceiving = GoodsReceiving::find($request->goods_receiving_id);

        if (!$goodsReceiving) {
            Log::error('Goods receiving not found', ['id' => $request->goods_receiving_id]);
            session()->flash("alert-danger", "Goods receiving record not found!");
            return back();
        }

        Log::info('Found goods receiving', [
            'id' => $goodsReceiving->id,
            'current_status' => $goodsReceiving->status
        ]);

        $purchase_return = new PurchaseReturn();
        $purchase_return->goods_receiving_id = $request->goods_receiving_id;
        $purchase_return->quantity = $request->quantity;
        $purchase_return->reason = $request->reason;
        $purchase_return->date = $date;
        $purchase_return->created_by = Auth::User()->id;

        $goodsReceiving->status = 2;
        $goodsReceiving->updated_by = Auth::User()->id;
        $goodsReceiving->updated_at = now();

        try {
            $goodsReceiving->save();
            $purchase_return->save();

            Log::info('Purchase return created successfully', [
                'return_id' => $purchase_return->id,
                'goods_receiving_status' => $goodsReceiving->status
            ]);

            session()->flash("alert-success", "Purchase returned, transaction will be effected after approval!");
        } catch (\Exception $e) {
            Log::error('Error creating purchase return', ['error' => $e->getMessage()]);
            session()->flash("alert-danger", "Error creating purchase return!");
        }

        return back();
    }

    public function update(Request $request, $id)
    {
        Log::info('Updating purchase return', [
            'id' => $id,
            'quantity' => $request->quantity,
            'reason' => $request->reason
        ]);

        $purchaseReturn = PurchaseReturn::find($id);
        if (!$purchaseReturn) {
            session()->flash("alert-danger", "Purchase return not found!");
            return back();
        }

        $purchaseReturn->quantity = $request->quantity;
        $purchaseReturn->reason = $request->reason;
        $purchaseReturn->save();

        Log::info('Purchase return updated successfully', ['id' => $id]);
        session()->flash("alert-success", "Purchase return updated successfully!");
        return back();
    }

    public function approvals()
    {
        return view('purchases.purchase_returns.approvals');
    }
}
