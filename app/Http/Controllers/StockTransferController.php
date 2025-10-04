<?php

namespace App\Http\Controllers;

use App\CommonFunctions;
use App\CurrentStock;
use App\PriceList;
use App\Setting;
use App\StockTracking;
use App\StockTransfer;
use App\Store;
use App\User;
use App\Notifications\StockTransferNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PDF;

class StockTransferController extends Controller {

    public function index() {
        if (!Auth()->user()->checkPermission('Create Stock Transfer')) {
            if (!Auth()->user()->checkPermission('View Stock Transfer')) {
                abort(403, 'Access Denied');
            }
            return redirect()->route('stock-transfer-history');
        }
        $storeId = current_store_id();

        if ( is_all_store() ) {
            $products = CurrentStock::with( 'product' )
            ->select( DB::raw( 'SUM(quantity) as quantity' ),
            DB::raw( 'product_id' ), DB::raw( 'MAX(id) as stock_id' ) )
            ->where( 'quantity', '>', 0 )
            ->whereHas( 'product' )
            ->groupBy( 'product_id' )
            ->get();
        } else {
            $products = CurrentStock::with( 'product' )
            ->select( DB::raw( 'SUM(quantity) as quantity' ),
            DB::raw( 'product_id' ), DB::raw( 'MAX(id) as stock_id' ) )
            ->where( 'quantity', '>', 0 )
            ->whereHas( 'product' )
            ->where( 'store_id', $storeId )
            ->groupBy( 'product_id' )
            ->get();
        }

        $stores = Store::where( 'name', '<>', 'ALL' )->get();
        return view( 'stock_management.stock_transfer.index' )->with( [
            'stores' => $stores,
            'products' => $products
        ] );

    }

    public function storeTransfer( Request $request ) {
        if ( $request->ajax() ) {
            try {
                $transfer_no = $this->store( $request );

                return response()->json( [
                    'success' => true,
                    'message' => 'Stock transfer created successfully!',
                    'transfer_no' => $transfer_no,
                    // 'redirect_to' => route( 'stock-transfer-pdf-gen', $transfer_no ),
                    // 'history_url' => route( 'stock-transfer-history' )
                ] );
            } catch ( Exception $e ) {
                Log::error( 'Stock Transfer Creation Error: ' . $e->getMessage() );
                return response()->json( [
                    'success' => false,
                    'message' => 'Failed to create stock transfer. Please try again.',
                    'error' => $e->getMessage()
                ], 500 );
            }
        }

        // Non-AJAX request - redirect to history page
        $transfer_no = $this->store( $request );
        return redirect()->route( 'stock-transfer-history' )->with( 'success', 'Stock transfer created successfully!' );
    }

    protected function sendNotifications( $transfer, $status, $action ) {
        Log::info( 'Sending stock transfer notifications', [
            'transfer' => $transfer,
            'status' => $status,
            'action' => $action
        ] );
        // Get users to notify based on the action

        $users = collect();

        switch ( $action ) {
            case 'created':
            // Notify destination store managers
            $users = User::whereHas( 'roles', function( $q ) {
                $q->where( 'name', 'store_manager' );
            }
        )->where( 'store_id', $transfer->to_store )->get();
        break;

        case 'needs_approval':
        // Notify users with approval permission - handle missing permission gracefully
        try {
            $users = User::permission( 'approve_stock_transfers' )->get();
        } catch ( Exception $e ) {
            // If permission doesn't exist, notify store managers instead
                    $users = User::whereHas('roles', function($q) {
                        $q->where('name', 'store_manager');
                    })->get();
                }
                break;

            case 'status_change':
            case 'acknowledged':
                // Notify both source and destination store managers
                $users = User::whereHas('roles', function($q) {
                    $q->where('name', 'store_manager');
                })->whereIn('store_id', [$transfer->from_store, $transfer->to_store])->get();
                break;
        }

        // Send notifications only if users exist
        if ($users->count() > 0) {
            try {
                Notification::send($users, new StockTransferNotification($transfer, $status, $action));
            } catch (Exception $e) {
                Log::warning('Failed to send stock transfer notification: ' . $e->getMessage());
            }
        }
    }

    public function store(Request $request)
    {
        // Handle file upload (kama ulivyokuwa)
        $pictureName = null;
        if ($request->hasFile('evidence')) {
            $picture = $request->file('evidence');
            $pictureExtension = $picture->getClientOriginalExtension();
            $pictureName = $picture->getFilename() . '.' . $pictureExtension;
            $picture->move(public_path('fileStore'), $pictureName);
        }

        $transfer_no = $this->transferNumberAutoGen();
        $to_save_data = [];
        $user_id = Auth::id();

        foreach (json_decode($request->cart, true) as $value) {
            if (!array_key_exists('quantityIn', $value)) {
                session()->flash("alert-danger", "Please quantity transfered exceeds quantity available!");
                return back();
            }

            $transferData = [
                'stock_id' => $value['stock_id'],
                'product_id' => $value['product_id'],
                'transfer_no' => $transfer_no,
                'transfer_qty' => str_replace(', ', '', $value['quantityTran']),
                'from_store' => $request->from_id,
                'to_store' => $request->to_id,
                'status' => 'created',
                'remarks' => $request->remark, 
                'created_by' => $user_id,
                'created_at' => now(),
                'evidence' => $pictureName
            ];

            $to_save_data[] = $transferData;
        }

        // Insert transfers
        foreach ($to_save_data as $save_data) {
            try {
                $id = DB::table('inv_stock_transfers')->insertGetId([
                    'stock_id' => $save_data['stock_id'],
                    'transfer_qty' => $save_data['transfer_qty'],
                    'from_store' => $save_data['from_store'],
                    'to_store' => $save_data['to_store'],
                    'status' => $save_data['status'],
                    'remarks' => $save_data['remarks'],
                    'created_by' => $save_data['created_by'],
                    'created_at' => $save_data['created_at'],
                    'transfer_no' => $save_data['transfer_no'],
                    'evidence' => $save_data['evidence']
                ]);

                $transfer = DB::table('inv_stock_transfers')->where('id', $id)->first();
                $this->sendNotifications($transfer, 'created', 'created');

                if (config('stock.require_transfer_approval', true)) {
                    $this->sendNotifications($transfer, 'created', 'needs_approval');
                }

            } catch (\Exception $e) {
                Log::error('Stock Transfer Error: ' . $e->getMessage());
                return back()->with('error', 'Failed to create stock transfer. Please try again.');
            }
        }

        return strval($transfer_no);
    }

        public function show($id)
    {
        $transfer_group = StockTransfer::findOrFail($id);
        $transfers = StockTransfer::with(['currentStock.product', 'fromStore', 'toStore'])->where('transfer_no', $transfer_group->transfer_no)->get();

        if ($transfers->isEmpty()) {
            return redirect()->route('stock-transfer-history')->with('error', 'Transfer not found.');
        }

        return view('stock_management.stock_transfer.show', compact('transfers'));
    }
 
    // public function edit($transfer_no)
    // {
    //     $transfer_group = StockTransfer::where('transfer_no', $transfer_no)->first();
    //     $transfers = StockTransfer::with(['currentStock.product', 'fromStore', 'toStore'])->where('transfer_no', $transfer_group->transfer_no)->get();

    //     if ($transfers->isEmpty()) {
    //         return redirect()->route('stock-transfer-history')->with('error', 'Transfer not found.');
    //     }

    //     foreach ($transfers as $transfer) {
    //         $productId = $transfer->currentStock->product_id ?? null;
    //         if ($productId) {
    //             $totalStock = DB::table('inv_current_stock')
    //                 ->where('product_id', $productId)
    //                 ->sum('quantity');

    //             $transfer->total_stock = $totalStock;
    //         } else {
    //             $transfer->total_stock = 0;
    //         }
    //     }
    //     $stores = Store::where('name', '<>', 'ALL')->get();

    //     return view('stock_management.stock_transfer.edit', compact('transfers', 'stores'));
    // }
    public function edit($transfer_no)
    {
        $transfer_group = StockTransfer::where('transfer_no', $transfer_no)->first();

        if (!$transfer_group) {
            return redirect()->route('stock-transfer-history')->with('error', 'Transfer group not found.');
        }

        $transfers = StockTransfer::with(['currentStock', 'fromStore', 'toStore'])
            ->where('transfer_no', $transfer_group->transfer_no)
            ->get();

        if ($transfers->isEmpty()) {
            return redirect()->route('stock-transfer-history')->with('error', 'Transfer not found.');
        }

        foreach ($transfers as $transfer) {
            $productId = $transfer->currentStock->product_id ?? null;

            $storeId = $transfer->from_store_id ?? $transfer->fromStore->id ?? $transfer->currentStock->store_id ?? null;

            if ($productId && $storeId) {
                $transfer->total_stock = DB::table('inv_current_stock')
                    ->where('product_id', $productId)
                    ->where('store_id', $storeId)
                    ->sum('quantity');
            } else {
                $transfer->total_stock = 0;
            }
        }

        $stores = Store::where('name', '<>', 'ALL')->get();

        return view('stock_management.stock_transfer.edit', compact('transfers', 'stores'));
    }


    public function update(Request $request, $transfer_no)
    {
        $request->validate([
            'from_id' => 'required|integer|min:1',
            'to_id' => 'required|integer|min:1|different:from_id',
            'order_cart' => 'required|string',
        ]);

        $order_cart = json_decode($request->input('order_cart'), true);

        if (empty($order_cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer list is empty.'
            ]);
        }

        DB::beginTransaction();
        try {
            // Handle file upload
            $pictureName = null;
            if ($request->hasFile('evidence')) {
                $picture = $request->file('evidence');
                $pictureExtension = $picture->getClientOriginalExtension();
                $pictureName = $picture->getFilename() . '.' . $pictureExtension;
                $picture->move(public_path('fileStore'), $pictureName);
            }else{
                $pictureName = $request->input('old_evidence');
            }

            $oldTransfers = StockTransfer::where('transfer_no', $transfer_no)->get();
            $oldMap = $oldTransfers->keyBy('stock_id'); 

            $newStockIds = collect($order_cart)->pluck('stock_id')->toArray();

            // 1. Update / Insert
            foreach ($order_cart as $item) {
                $stockId = $item['stock_id'];
                $qty = $item['quantityTran'];

                if ($oldMap->has($stockId)) {
                    // update
                    StockTransfer::where('transfer_no', $transfer_no)
                        ->where('stock_id', $stockId)
                        ->update([
                            'remarks' => $request->remarks,
                            'evidence' => $pictureName,
                            'transfer_qty' => $qty,
                            'updated_by'   => auth()->id(),
                            'updated_at'   => now(),
                        ]);
                } else {
                    // create new
                        $st = new StockTransfer();
                        $st->transfer_no   = $transfer_no;
                        $st->stock_id      = $stockId;
                        $st->transfer_qty  = $qty;
                        $st->from_store = $request->from_id;
                        $st->to_store   = $request->to_id;
                        $st->remarks       = $request->remarks;
                        $st->evidence      = $pictureName;
                        $st->status        = 'created';
                        $st->created_by    = auth()->id();
                        $st->created_at    = now();
                        $st->save();
                
                }
            }

            // 2. Delete old items which are not in cart
            $oldMap->each(function ($oldItem, $stockId) use ($newStockIds, $transfer_no) {
                if (!in_array($stockId, $newStockIds)) {
                    StockTransfer::where('transfer_no', $transfer_no)
                        ->where('stock_id', $stockId)
                        ->delete();
                }
            });

            // 3. Update info (remarks, evidence, stores)
            StockTransfer::where('transfer_no', $transfer_no)->update([
                'from_store' => $request->from_id,
                'to_store'   => $request->to_id,
                'remarks'       => $request->remarks,
                'updated_by'    => auth()->id(),
                'updated_at'    => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock transfer updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ]);
        }
    }
            public function transferNumberAutoGen() {
                $number_gen = new CommonFunctions();
                $unique = $number_gen->generateNumber();
                return $unique;
            }

            public function generateStockTransferPDF( $transfer_no ) {
                $transfer = DB::table( 'inv_stock_transfers as t' )
                ->select(
                    't.*',
                    'fs.name as from_store_name',
                    'ts.name as to_store_name',
                    'p.name as product_name',
                    'p.brand',
                    'p.pack_size',
                    'u.name as created_by_name',
                    'up.name as updated_by_name'
                )
                ->join( 'inv_stores as fs', 'fs.id', '=', 't.from_store' )
                ->join( 'inv_stores as ts', 'ts.id', '=', 't.to_store' )
                ->join( 'inv_current_stock as cs', 'cs.id', '=', 't.stock_id' )
                ->join( 'inv_products as p', 'p.id', '=', 'cs.product_id' )
                ->join( 'users as u', 'u.id', '=', 't.created_by' )
                ->leftJoin( 'users as up', 'up.id', '=', 't.updated_by' )
                ->where( 't.transfer_no', $transfer_no )
                ->first();

                if ( !$transfer ) {
                    return back()->with( 'error', 'Transfer not found' );
                }

                // Get status text
                $statuses = [
                    1 => 'Created',
                    2 => 'Assigned',
                    3 => 'Approved',
                    4 => 'In Transit',
                    5 => 'Acknowledged',
                    6 => 'Completed'
                ];
                $transfer->status_text = $statuses[ $transfer->status ] ?? 'Unknown';

                // Get audit trail - try to find relevant stock adjustments or provide empty collection
                try {
                    $audit_trail = DB::table( 'stock_adjustment_logs' )
                    ->where( 'current_stock_id', $transfer->stock_id )
                    ->where( 'created_at', '>=', $transfer->created_at )
                    ->orderBy( 'created_at', 'asc' )
                    ->get();
                } catch ( Exception $e ) {
                    // If audit table doesn't exist or has issues, provide empty collection
            $audit_trail = collect();
        }

        $pdf = PDF::loadView('stock_management.stock_transfer.pdf', [
            'transfer' => $transfer,
            'audit_trail' => $audit_trail
        ]);

        return $pdf->stream('stock_transfer_' . $transfer_no . '.pdf');
    }

    public function regenerateStockTransferPDF(Request $request)
    {
        $transfer_no = $request->transfer_no;
        
        if (!$transfer_no) {
            return redirect()->back()->with('error', 'Transfer number is required');
        }

        return redirect()->route('stock-transfer-pdf-gen', strval($transfer_no));
    }

    public function stockTransferHistory()
    {
        if (!Auth()->user()->checkPermission('View Stock Transfer')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $stores = Store::where('name', '<>', 'ALL')->get();

        if (is_all_store()) {
            // Get ALL transfers grouped by transfer_no
            $transfers_groups = StockTransfer::with(['fromStore', 'toStore', 'currentStock.product'])
                ->latest()
                ->get()
                ->groupBy('transfer_no');
        } else {
            // Get ONLY transfers that involve the current store
            $transfers_groups = StockTransfer::with(['fromStore', 'toStore', 'currentStock.product'])
                ->where(function ($query) use ($store_id) {
                    $query->where('from_store', $store_id)
                        ->orWhere('to_store', $store_id);
                })
                ->latest()
                ->get()
                ->groupBy('transfer_no');
        }

        // Collect unique approver IDs from all transfers (flatten groups first)
        $flattened = $transfers_groups->flatten(1); // collection of StockTransfer models
        $approverIds = $flattened->pluck('approved_by')->filter()->unique()->values()->all();
        $cancellerIds = $flattened->pluck('cancelled_by')->filter()->unique()->values()->all();

        // Resolve User model from auth config (works for App\User or App\Models\User setups)
        $userModel = config('auth.providers.users.model');

        $approvers = [];
        if (!empty($approverIds) || !empty($cancellerIds)) {
        $allIds = array_unique(array_merge($approverIds, $cancellerIds));
        $approvers = $userModel::whereIn('id', $allIds)->get()->keyBy('id');
        }

        // Create a representative model for each group to display in the main table.
        $transfers = $transfers_groups->map(function ($group) use ($approvers) {
            $first = $group->first();

            // create a replica to avoid mutating original relations
            $repres = $first->replicate();
            $repres->id = $first->id; // preserve id (for links)
            $repres->total_products = $group->count();
            $repres->status = $group->min('status');
            $repres->remarks = $first->remarks;
            $repres->created_at = $first->created_at;
            $repres->all_items = $group;

            // get approver id from the group (first non-null approved_by)
        $approverId = $group->pluck('approved_by')->filter()->first();
        $repres->approved_by_name = $approverId && isset($approvers[$approverId])
            ? $approvers[$approverId]->name
            : null;

        // canceller
        $cancellerId = $group->pluck('cancelled_by')->filter()->first();
        $repres->cancelled_by_name = $cancellerId && isset($approvers[$cancellerId])
            ? $approvers[$cancellerId]->name
            : null;

        // acknowledged by
        $acknowledgerId = $group->pluck('acknowledged_by')->filter()->first();
        $repres->acknowledged_by_name = $acknowledgerId && isset($approvers[$acknowledgerId])
            ? $approvers[$acknowledgerId]->name
            : null;

            return $repres;
        });

        return view('stock_management.stock_transfer.history', compact('transfers', 'stores'));
    }

    public function filterTransferByDate(Request $request)
    {
        if ($request->ajax()) {
            $all_transfer = StockTransfer::where(DB::raw('date( created_at )'), '=', $request->date)->get();

            foreach ($all_transfer as $transfer) {
                $transfer->product;
            }

            return json_decode($all_transfer, true);

        }
    }

    public function filterByStore(Request $request)
    {
        if ($request->ajax()) {
            $products = CurrentStock::select(
                    DB::raw('SUM( inv_current_stock.quantity ) as quantity'),
                    'inv_current_stock.product_id',
                    DB::raw('MAX( inv_current_stock.id ) as stock_id'),
                    'inv_products.name',
                    'inv_products.pack_size',
                    'inv_products.brand',
                    'inv_products.sales_uom'
                )
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->where('inv_current_stock.quantity', '>', 0)
                ->where('inv_products.status', 1)
                ->where('inv_current_stock.store_id', $request->from_id)
                ->groupBy('inv_current_stock.product_id', 'inv_products.name', 'inv_products.pack_size')
                // ->limit(10)
                ->get();

            return response()->json($products);
        }

    }

    public function filterByWord(Request $request)
    {
        if ($request->ajax()) {

            $products = CurrentStock::select(DB::raw('sum( quantity ) as quantity'),
                DB::raw('product_id'), DB::raw('max( inv_current_stock.id ) as stock_id'), 'inv_products.name', 'inv_products.pack_size')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->where('inv_products.name', 'LIKE', "%{$request->word}%")
                ->where('inv_current_stock.quantity', '>', '0')
                ->where('inv_current_stock.store_id', $request->from_id)
                ->where('inv_products.status', '=', 1)
                ->groupby('inv_current_stock.product_id', 'inv_products.name', 'inv_products.pack_size')
                ->limit(10)
                ->get();
            foreach ($products as $product) {
                $product->product;
            }
            return json_decode($products, true);
        }
    }

    public function acknowledgeAll(Request $request)
    {
        DB::beginTransaction();
        try{

            $unacknowledge = DB::table('inv_stock_transfers')
                ->where('status','1')
                ->where('evidence','<>','NULL')
                ->where('transfer_no','=',$request->transfer_no)
                ->get();


            foreach($unacknowledge as $data)
            {
                $this->acknowledgeAndUpdateStock($data);
            }

            Session::flash('success', 'All stocks were transfered successfully');
            DB::commit();

            return redirect()->back();
        }catch (Exception $e)
        {

            Session::flash('danger', 'Something went wrong');
            DB::rollback();
            Log::info('AcknowledgementError',['Error'=>$e]);

            return redirect()->back();
        }
    }

    public function acknowledgeAndUpdateStock($request)
    {

        /*get default store*/
        $default_store = Auth::user()->store->name ?? 'Default Store';
        $stores = Store::where('name', $default_store)->first();

        if ($stores != null) {
            $default_store_id = $stores->id;
        } else {
            $default_store_id = 1;
        }

        $stock_update = CurrentStock::find($request->stock_id);

        $transfered_quantity = (int)str_replace(', ','',$request->transfer_qty);
        $received_quantity = (int)str_replace(', ','',$request->transfer_qty);

        $remain_stock =  $transfered_quantity  - $received_quantity;
        $present_stock = $stock_update->quantity + $remain_stock;

        $stock_update->quantity = $present_stock;
        $stock_update->save();

        /*status 5 meaning acknowledged*/
        $transfer = StockTransfer::find($request->id);
        $transfer->accepted_qty = $request->transfer_qty;
        $transfer->status = 5; // Acknowledged
        $transfer->acknowledged_by = Auth::user()->id;
        $transfer->acknowledged_at = now();
        $transfer->save();

        /*insert in current stock*/
        $current_stock = new CurrentStock;
        $current_stock->product_id = $stock_update->product_id;
        $current_stock->expiry_date = $stock_update->expiry_date;
        $current_stock->quantity = $request->transfer_qty;
        $current_stock->unit_cost = $stock_update->unit_cost;
        $current_stock->batch_number = $stock_update->batch_number;
        $current_stock->store_id = $transfer->to_store;
        $current_stock->created_by = Auth::user()->id;
        $current_stock->save();
        /*end of insert*/

        /*insert into price*/
        $prev_price = PriceList::where('stock_id', $request->stock_id)
            ->orderby('id', 'desc')
            ->first();
        $price = new PriceList;
        $price->stock_id = $current_stock->id;
        $price->price = str_replace(', ', '', $prev_price->price);
        $price->price_category_id = $prev_price->price_category_id;
        $price->status = 1;
        $price->created_at = date('Y-m-d H:m:s');
        $price->save();
        /*end insert*/

        /*save in stocktracking*/
        $stock_tracking = new StockTracking;
        $stock_tracking->stock_id = $request->stock_id;
        $stock_tracking->product_id = $transfer->currentStock['product_id'];
        $stock_tracking->quantity = $remain_stock;
        $stock_tracking->store_id = $default_store_id;
        $stock_tracking->updated_by = Auth::user()->id;
        $stock_tracking->out_mode = 'Stock Transfer';
        $stock_tracking->updated_at = date('Y-m-d');
        $stock_tracking->movement = 'IN';
        $stock_tracking->save();

        $stock_tracking = new StockTracking;
        $stock_tracking->stock_id = $request->stock_id;
        $stock_tracking->product_id = $transfer->currentStock['product_id'];
        $stock_tracking->quantity = $request->transfer_qty;
        $stock_tracking->store_id = $transfer->to_store;
        $stock_tracking->updated_by = Auth::user()->id;
        $stock_tracking->out_mode = 'Stock Transfer Completed';
        $stock_tracking->updated_at = date('Y-m-d');
        $stock_tracking->movement = 'IN';
        $stock_tracking->save();

        session()->flash("alert-success", "Transfer updated successfully!");
        return back();
    }

    public function updateStatus(Request $request, $id)
    {
        $transfer = DB::table('inv_stock_transfers')->where('id', $id)->first();
        $newStatus = $request->status;
        
        // Validate status transition
        if (!$this->isValidStatusTransition($transfer->status, $newStatus)) {
            return response()->json(['error' => 'Invalid status transition'], 400);
        }
        
        DB::table('inv_stock_transfers')
            ->where('id', $id)
            ->update([
                'status' => $newStatus,
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

        // Send appropriate notifications
        $action = $newStatus == 5 ? 'acknowledged' : 'status_change';
        $this->sendNotifications($transfer, $newStatus, $action);

        return response()->json(['message' => 'Status updated successfully']);
    }

    /**
     * Validate status transitions
     */
    private function isValidStatusTransition($currentStatus, $newStatus)
    {
        $validTransitions = [
            1 => [2, 3], // Created -> Assigned, Approved
            2 => [3, 4], // Assigned -> Approved, In Transit
            3 => [4],    // Approved -> In Transit
            4 => [5],    // In Transit -> Acknowledged
            5 => [6],    // Acknowledged -> Completed
            6 => []      // Completed -> No further transitions
        ];

        return in_array($newStatus, $validTransitions[$currentStatus] ?? []);
    }

    /**
     * Assign transfer to destination store
     */
    public function assignTransfer(Request $request, $id)
    {
        $transfer = DB::table('inv_stock_transfers')->where('id', $id)->first();
        
        if ($transfer->status != 1) {
            return response()->json(['error' => 'Transfer must be in Created status to assign'], 400);
        }

        DB::table('inv_stock_transfers')
            ->where('id', $id)
            ->update([
                'status' => 2, // Assigned
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

        $this->sendNotifications($transfer, 2, 'assigned');
        return response()->json(['message' => 'Transfer assigned successfully']);
    }

    /**
     * Approve transfer
     */
    public function approveTransfer(Request $request)
    {
        $order = $request->input('transfer_no');

        // get all transfer rows for this group
        $transfers = DB::table('inv_stock_transfers')->where('transfer_no', $order)->get();

        if ($transfers->isEmpty()) {
            return response()->json(['message' => 'Transfer not found.'], 404);
        }

        // if any already approved, prevent double-approve
        $alreadyApproved = $transfers->contains(function ($t) {
            return $t->status === 'approved' || $t->status === 'Approved' || $t->status == 2;
        });
        if ($alreadyApproved) {
            return response()->json(['message' => 'Transfer already approved.'], 422);
        }

        DB::beginTransaction();
        try {
            foreach ($transfers as $transfer) {
                $productId = $transfer->product_id ?? null;
                $fromStore = $transfer->from_store;
                $qtyToMove = (float) $transfer->transfer_qty;

                if (!$productId) {
                    $stockRow = DB::table('inv_current_stock')->where('id', $transfer->stock_id)->first();
                    $productId = $stockRow->product_id ?? null;
                }

                if (!$productId) {
                    DB::rollBack();
                    return response()->json(['message' => "Product id not found for transfer id {$transfer->id}"], 422);
                }

                $remaining = $qtyToMove;

                $batches = \App\CurrentStock::where('product_id', $productId)
                    ->where('store_id', $fromStore)
                    ->where('quantity', '>', 0)
                    ->orderBy('expiry_date', 'asc') 
                    ->get();

                if ($batches->isEmpty()) {
                    DB::rollBack();
                    session()->flash('alert-danger', "Insufficient stock in this store");
            return redirect()->route('stock-transfer-history')->with(['message' => "Insufficient stock for product id {$productId} in store {$fromStore}"]);
                }

                foreach ($batches as $batch) {
                    if ($remaining <= 0) break;

                    $available = (float) $batch->quantity;

                    if ($available <= 0) continue;

                    if ($available >= $remaining) {
                        $batch->quantity = $available - $remaining;
                        $consumed = $remaining;
                        $remaining = 0;
                    } else {
                        $batch->quantity = 0;
                        $consumed = $available;
                        $remaining -= $available;
                    }

                    $batch->save();

                    // create stock tracking (OUT)
                    \App\StockTracking::create([
                        'stock_id' => $batch->id, 
                        'product_id' => $productId,
                        'quantity' => $consumed,
                        'store_id' => $fromStore,
                        'created_by' => Auth::id(),
                        'out_mode' => 'Stock Transfer',
                        'updated_at' => date('Y-m-d'),
                        'movement' => 'OUT'
                    ]);
                }

                if ($remaining > 0) {
                    // not enough across all batches - rollback and return error
                    DB::rollBack();
                    session()->flash('alert-danger', "Insufficient stock in this store");
            return redirect()->route('stock-transfer-history')->with(['message' => "Insufficient stock for product id {$productId} in store {$fromStore}"]);
                    // return response()->json([
                    //     'message' => "Insufficient total stock for product id {$productId} in store {$fromStore}. Short by {$remaining}."
                    // ], 422);
                }
            }

            // If reached here, all deductions succeeded — update transfers to approved
            DB::table('inv_stock_transfers')
                ->where('transfer_no', $order)
                ->update([
                    'status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now()
                ]);

            $representative = DB::table('inv_stock_transfers')->where('transfer_no', $order)->first();
            $this->sendNotifications($representative, 'approved', 'approved');

            DB::commit();

            // return response()->json(['message' => 'Stock transfer approved successfully']);
            return redirect()->route('stock-transfer-history')->with(['message' => 'Stock transfer approved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approve transfer error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to approve transfer: ' . $e->getMessage()], 500);
        }
    }
    public function rejectTransfer(Request $request)
    {
        $validate = [
            'reject_transfer_no' => 'required|string',
            'rejection_reason' => 'required|string',
        ];
        $this->validate($request, $validate);
        $order = $request->get('reject_transfer_no');
        $reason = $request->get('rejection_reason');

        // get all transfer rows for this group
        $transfers = DB::table('inv_stock_transfers')->where('transfer_no', $order)->get();

        if ($transfers->isEmpty()) {
            return response()->json(['message' => 'Transfer not found.'], 404);
        }

        DB::beginTransaction();
        try {
            DB::table('inv_stock_transfers')
                ->where('transfer_no', $order)
                ->update([
                    'status' => 'cancelled',
                    'cancelled_by' => Auth::id(),
                    'cancelled_at' => now(),
                    'cancellation_reason' => $reason,
                ]);

            $representative = $transfers->first();
            $this->sendNotifications($representative, 'cancelled', 'cancelled');

            DB::commit();

            return redirect()->route('stock-transfer-history')->with(['message' => 'Stock transfer rejected successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reject transfer error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to reject transfer: ' . $e->getMessage()], 500);
        }
    }

    public function showJson($transfer)
    {
        $transfer = DB::table('inv_stock_transfers as t')
            ->select(
                't.*',
                'fs.name as from_store_name',
                'ts.name as to_store_name',
                'u.name as created_by_name',
                'up.name as updated_by_name',
                DB::raw('COUNT( * ) as total_products')
            )
            ->join('inv_stores as fs', 'fs.id', '=', 't.from_store')
            ->join('inv_stores as ts', 'ts.id', '=', 't.to_store')
            ->join('users as u', 'u.id', '=', 't.created_by')
            ->leftJoin('users as up', 'up.id', '=', 't.updated_by')
            ->where('t.transfer_no', $transfer)
            ->groupBy('t.transfer_no', 't.from_store', 't.to_store', 't.created_at', 't.status', 't.remark', 't.evidence', 'fs.name', 'ts.name', 'u.name', 'up.name')
            ->first();

        if (!$transfer) {
            return response()->json(['error' => 'Transfer not found'], 404);
        }

        // Get status class for badge styling
        $status_classes = [
            1 => 'primary',    // Created
            2 => 'warning',    // Assigned
            3 => 'info',       // Approved
            4 => 'success',    // In Transit
            5 => 'success',    // Acknowledged
            6 => 'success'     // Completed
        ];
        $transfer->status_class = $status_classes[$transfer->status] ?? 'secondary';

        // Get product details
        $products = DB::table('inv_stock_transfers as t')
            ->select(
                't.*',
                'p.name as product_name',
                'p.brand',
                'p.pack_size',
                'cs.unit_cost as unit_price',
                DB::raw('t.transfer_qty * cs.unit_cost as total_price')
            )
            ->join('inv_current_stock as cs', 'cs.id', '=', 't.stock_id')
            ->join('inv_products as p', 'p.id', '=', 'cs.product_id')
            ->where('t.transfer_no', $transfer)
            ->get();

        return response()->json([
            'transfer_no' => $transfer->transfer_no,
            'date' => $transfer->created_at->format('Y-m-d'),
            'total_products' => $transfer->total_products,
            'from_store' => $transfer->from_store_name,
            'to_store' => $transfer->to_store_name,
            'status' => $transfer->status,
            'status_class' => $transfer->status_class,
            'remark' => $transfer->remark,
            'stock_transfer_items' => $products
        ]);
    }

    public function markInTransit(Request $request, $id)
    {
        $transfer = DB::table('inv_stock_transfers')->where('id', $id)->first();
        
        if ($transfer->status != 3) {
            return response()->json(['error' => 'Transfer must be in Approved status'], 400);
        }

        DB::table('inv_stock_transfers')
            ->where('id', $id)
            ->update([
                'status' => 4, // In Transit
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

        $this->sendNotifications($transfer, 4, 'status_change');
        return response()->json(['message' => 'Transfer marked as in transit successfully']);
    }

    public function acknowledgeTransfer(Request $request, $id)
    {
        $transfer = DB::table('inv_stock_transfers')->where('id', $id)->first();
        
        if ($transfer->status != 4) {
            return response()->json(['error' => 'Transfer must be in In Transit status'], 400);
        }

        DB::table('inv_stock_transfers')
            ->where('id', $id)
            ->update([
                'status' => 5, // Acknowledged
                'acknowledged_by' => Auth::id(),
                'acknowledged_at' => now(),
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

        $this->sendNotifications($transfer, 5, 'acknowledged');
        return response()->json(['message' => 'Transfer acknowledged successfully']);
    }

    /**
     * Complete transfer
     */
    public function completeTransfer(Request $request, $id)
    {
        $transfer = DB::table('inv_stock_transfers')->where('id', $id)->first();
        
        if ($transfer->status != 5) {
            return response()->json(['error' => 'Transfer must be in Acknowledged status'], 400);
        }

        DB::table('inv_stock_transfers')
            ->where('id', $id)
            ->update([
                'status' => 6, // Completed
                'updated_by' => Auth::id(),
                'updated_at' => now()
            ]);

        $this->sendNotifications($transfer, 6, 'completed');
        return response()->json(['message' => 'Transfer completed successfully' ] );
                }

            }