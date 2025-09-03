<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\Product;
use App\Requisition;
use App\RequisitionDetail;
use App\Setting;
use App\StockTracking;
use App\Store;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDF;
use Yajra\DataTables\DataTables;

class RequisitionController extends Controller
{
    public function index()
    {
        if (!Auth()->user()->checkPermission('View Requisition')) {
            abort(403, 'Access Denied');
        }

        return view('requisitions.index');
    }

    public function getRequisitions(Request $request)
    {
        if (!Auth()->user()->checkPermission('View Requisition')) {
            abort(403, 'Access Denied');
        }

        if ($request->ajax()) {
            $data = Requisition::with(['reqDetails'])
                ->leftJoin(DB::raw('inv_stores as from_store'), 'requisitions.from_store', '=', 'from_store.id')
                ->leftJoin(DB::raw('inv_stores as to_store'), 'requisitions.to_store', '=', 'to_store.id')
                ->selectRaw('requisitions.*, to_store.name as toStore, from_store.name as fromStore')
                ->orderBy('requisitions.id', 'DESC');
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $btn_view = '';
                    if (Auth()->user()->can('Manage Product Categories')) {
                        if (Auth()->user()->can('Manage Product Categories')) {

                            $btn_view =
                              '<form action="'. route('print-requisitions')  .'" method="GET" target="_blank">
                                <input type="hidden" name="req_id" value="'.$row->id .'">
                                <button type="button"  data-toggle="modal" data-target="#requisition-details" data-id="'.$row->id.'" class="btn btn-rounded btn-success btn-sm">Show</button>
                                <a href="' . route('requisitions.view', $row->id) . '" class="btn btn-rounded btn-primary btn-sm" title="EDIT">Edit</a>
                                <button type="submit" name="save" class="btn btn-rounded btn-secondary btn-sm">Print <span class="fa fa-print"></span></button>
                             </form>';
                        }
                    }

                    return $btn_view;
                })
                ->addColumn('products', function ($row) {
                    // Get first 2-3 product names for display
                    $productNames = [];
                    foreach ($row->reqDetails->take(3) as $detail) {
                        if ($detail->products_) {
                            $productNames[] = $detail->products_->name . ' ' . 
                                            $detail->products_->brand . ' ' . 
                                            $detail->products_->pack_size . ' ' . 
                                            $detail->products_->sales_uom;
                        }
                    }
                    
                    $displayText = implode(', ', $productNames);
                    if ($row->reqDetails->count() > 3) {
                        $displayText .= ' and ' . ($row->reqDetails->count() - 3) . ' more';
                    }
                    
                    $prod = '<span class="badge badge-primary p-1" title="' . htmlspecialchars($displayText) . '">' . 
                            $row->reqDetails->count() . ' Products</span>';
                    return $prod;
                })
                ->addColumn('reqDate', function ($row) {
                    return $row->created_at;
                })
                ->rawColumns(['action', 'products', 'reqTo', 'reqDate'])
                ->make(true);
        }
    }

    public function create()
    {
        if (!Auth()->user()->checkPermission('Create Requisitions')) {
            abort(403, 'Access Denied');
        }


        $items = Product::where('status', 1)->select('id', 'name', 'brand', 'pack_size', 'sales_uom')->get();
        $users = User::where('status', 1)->get();
        $stores = Store::where('name','<>','ALL')
            ->get();
        return view('requisitions.create', compact('items', 'users', 'stores'));
    }

    function search_items(Request $request)
    {
        if (!Auth()->user()->checkPermission('Create Requisitions')) {
            abort(403, 'Access Denied');
        }

        $request->validate([
            'item_id' => 'required'
        ]);

        $item = Product::find($request->item_id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        return response()->json([
            'item' => $item,
        ]);
    }

        public function store(Request $request)
    {
        if (!Auth()->user()->checkPermission('Create Requisitions')) {
            abort(403, 'Access Denied');
        }

        // Validate file upload
        $request->validate([
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // max 2MB
        ]);

        $orders = json_decode($request->orders);

        // Handle file upload - FIXED VARIABLE NAME
        $evidencePath = null;
        if($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('requisition_evidence', 'public');
        }

        $last_req_id = Requisition::orderBy('id', 'DESC')->first();

        if ($last_req_id) {
            $Id = ++$last_req_id->id;
        } else {
            $Id = 1;
        }

        $req_no = date('m') . date('d') . str_pad($Id, 5, '0', STR_PAD_LEFT);

        if(auth()->user()->checkPermission('Manage All Branches')) {
            $from_store = $request->from_store;
        }

        if(!auth()->user()->checkPermission('Manage All Branches')) {
            $from_store = Auth::user()->store_id;
        }

        if (!empty($orders)) {
            $requisition = new Requisition();
            $requisition->req_no = $req_no;
            $requisition->notes = $request->notes;
            $requisition->remarks = $request->remark;
            $requisition->evidence_document = $evidencePath; // FIXED COLUMN NAME
            $requisition->from_store = $from_store;
            $requisition->to_store = "1";
            $requisition->status = 0;
            $requisition->created_by = Auth::user()->id;

            $success = false;
            DB::beginTransaction();

            $success = $requisition->save();

            foreach ($orders as $order_details) {
                $order_detail = new RequisitionDetail();
                $order_detail->req_id = $requisition->id;
                $order_detail->product = $order_details->itemss->id;
                $order_detail->quantity = $order_details->quantity;
                $order_detail->unit = $order_details->unit;
                $success = $order_detail->save();
            }
            
            session()->flash("alert-success", "Requisition Created Successfully!");
            DB::commit();

            return back();
        }
    }

    public function show($id)
{
    if (!Auth()->user()->checkPermission('View Requisitions Details')) {
        abort(403, 'Access Denied');
    }

    $items = Product::where('status', 1)->get();
    $stores = Store::get();

    $requisition = Requisition::with(['reqDetails', 'creator'])->find($id);

    $fromStore = Store::findOrFail($requisition->from_store);
    $toStore = Store::findOrFail($requisition->to_store);

    $requisitionDet = RequisitionDetail::with('products_')
        ->leftJoin('inv_current_stock', 'inv_current_stock.product_id', 'requisition_details.product')
        ->selectRaw('requisition_details.*, sum(inv_current_stock.quantity) as qty_oh')
        ->groupBy('inv_current_stock.product_id')
        // ->havingRaw(DB::raw('sum(inv_current_stock.quantity) > 0'))
        // ->where('inv_current_stock.store_id', $requisition->to_store)
        ->where('requisition_details.req_id', $id)
        ->get();

    // ADD THIS CONCATENATION CODE:
    $requisitionDet->each(function($detail) {
        if ($detail->products_) {
            $detail->products_->full_product_name = 
                $detail->products_->name . ' ' . 
                ($detail->products_->brand ?? '') . ' ' . 
                ($detail->products_->pack_size ?? '') . ' ' . 
                ($detail->products_->sales_uom ?? '');
        }
    });

    return view("requisitions.show", compact('requisition', 'requisitionDet', 'fromStore', 'toStore', 'items', 'stores'));
}

    //Shows Requisition Details
    public function showRequisition(Request $request)
    {
        $id = $request->req_id;
        $data = DB::table('requisition_details')
            ->join('inv_products', 'inv_products.id','=', 'requisition_details.product')
            ->select(
                'inv_products.name',
                'inv_products.brand',
                'inv_products.pack_size', 
                'inv_products.sales_uom',
                'requisition_details.unit',
                'requisition_details.quantity'
            )
            ->where('requisition_details.req_id','=',$id)
            ->get();

        // Transform the data to include concatenated product name
        $transformedData = $data->map(function($item) {
            return [
                'name' => $item->name,
                'brand' => $item->brand,
                'pack_size' => $item->pack_size,
                'sales_uom' => $item->sales_uom,
                'full_product_name' => $item->name . ' ' . $item->brand . ' ' . $item->pack_size . ' ' . $item->sales_uom,
                'unit' => $item->unit,
                'quantity' => $item->quantity
            ];
        });

        Log::info('message',['Data'=>$transformedData]);

        return $transformedData;
    }

    public function printReq(Request $request)
    {
        $receipt_size = Setting::where('id', 119)->value('value');
        $req_id = $request->req_id;

        $requisition = Requisition::with(['reqDetails', 'reqTo', 'creator'])->find($req_id);
        $requisitionDet = RequisitionDetail::with('products_')->where('req_id', $req_id)->get();
        $pharmacy = $this->companyInfo();

        // ADD CONCATENATION LOGIC HERE:
        $requisitionDet->each(function($detail) {
            if ($detail->products_) {
                $detail->products_->full_product_name = 
                    $detail->products_->name . ' ' . 
                    ($detail->products_->brand ?? '') . ' ' . 
                    ($detail->products_->pack_size ?? '') . ' ' . 
                    ($detail->products_->sales_uom ?? '');
            }
        });

        if ($receipt_size == '58mm Thermal Paper') {
            $view = 'requisitions.pdf.receipt_thermal';
            $output = 'request.pdf';
            $pdf = PDF::loadView($view, compact('requisition', 'requisitionDet', 'pharmacy'));
            return $pdf->stream($output);
        } else if ($receipt_size == 'A4 / Letter') {
            $view = 'requisitions.pdf.receipt';
            $output = 'request.pdf';
            $pdf = PDF::loadView($view, compact('requisition', 'requisitionDet', 'pharmacy'));
            return $pdf->stream($output);
        } else if ($receipt_size == '80mm Thermal Paper') {
            $view = 'requisitions.pdf.receipt_thermal';
            $output = 'request.pdf';
            $pdf = PDF::loadView($view, compact('requisition', 'requisitionDet', 'pharmacy'));
            return $pdf->stream($output);
        } else if ($receipt_size == 'A5 / Half Letter') {
            $view = 'requisitions.pdf.receipt';
            $output = 'request.pdf';
            $pdf = PDF::loadView($view, compact('requisition', 'requisitionDet', 'pharmacy'));
            return $pdf->stream($output);
        } else {
            echo "<script>window.close();</script>";
        }
    }

    private function companyInfo()
    {
        $pharmacy['name'] = Setting::where('id', 100)->value('value');
        $pharmacy['address'] = Setting::where('id', 106)->value('value');
        $pharmacy['phone'] = Setting::where('id', 107)->value('value');
        $pharmacy['email'] = Setting::where('id', 108)->value('value');
        $pharmacy['website'] = Setting::where('id', 109)->value('value');
        $pharmacy['logo'] = Setting::where('id', 105)->value('value');
        $pharmacy['tin_number'] = Setting::where('id', 102)->value('value');
        $pharmacy['slogan'] = Setting::where('id', 104)->value('value');
        $pharmacy['vrn_number'] = Setting::where('id', 103)->value('value');

        return $pharmacy;
    }

    public function update(Request $request)
    {
        if (!Auth()->user()->checkPermission('Create Requisitions')) {
            abort(403, 'Access Denied');
        }

        // Validate file upload (same as store method)
        $request->validate([
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // max 2MB
        ]);

        $req_id = $request->requisition_id;
        $remarks = $request->remark;
        
        if(auth()->user()->checkPermission('Manage All Branches')) {
            $from_store = $request->from_store;
        }

        if(!auth()->user()->checkPermission('Manage All Branches')) {
            $from_store = Auth::user()->store_id;
        }

        $to_store = "1";

        // Handle file upload - NEW CODE ADDED
        $evidencePath = null;
        if($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('requisition_evidence', 'public');
            
            // Optional: Delete old evidence file if it exists
            $oldRequisition = Requisition::find($req_id);
            if ($oldRequisition->evidence_document && Storage::disk('public')->exists($oldRequisition->evidence_document)) {
                Storage::disk('public')->delete($oldRequisition->evidence_document);
            }
        }

        $orders = json_decode($request->orders);
        if (!empty($orders)) {
            DB::beginTransaction();

            $requisition = Requisition::findOrFail($req_id);
            $requisition->from_store = $from_store;
            $requisition->to_store = $to_store;
            $requisition->remarks = $remarks;
            $requisition->updated_by = Auth::user()->id;
            
            // Update evidence document if a new file was uploaded - NEW CODE
            if ($evidencePath) {
                $requisition->evidence_document = $evidencePath;
            }
            
            $requisition->save();

            foreach ($orders as $order_details) {
                $check_req = RequisitionDetail::query()
                    ->where('req_id', $req_id)
                    ->where('product', $order_details->products_->id)
                    ->get();

                if (!$check_req->isEmpty()) {
                    $updateDetails = [
                        'quantity' => $order_details->quantity,
                        'unit' => $order_details->unit
                    ];

                    RequisitionDetail::query()
                        ->where('req_id', $req_id)
                        ->where('product', $order_details->products_->id)
                        ->update($updateDetails);
                } else {
                    $order_detail = new RequisitionDetail();
                    $order_detail->req_id = $requisition->id;
                    $order_detail->product = $order_details->products_->id;
                    $order_detail->quantity = $order_details->quantity;
                    $order_detail->unit = $order_details->unit;
                    $order_detail->save();
                }
            }
            
            session()->flash("alert-success", "Requisition Updated Successfully!");
            DB::commit();

            return back();
        }

        session()->flash("alert-success", "Requisition Accepted Successfully!");
        return back();
    }

    public function destroy(Request $request)
    {
//        if (!Auth()->user()->checkPermission('Delete Requisitions')) {
//            abort(403, 'Access Denied');
//        }

        Requisition::destroy($request->req_id);
        DB::table('requisition_details')->where('req_id', $request->req_id)->delete();

        return redirect()->route('requisitions.index');
        session()->flash("alert-success", "Requisition Deleted Successfully!");
    }

    public function issueReq()
    {
//        if (!Auth()->user()->checkPermission('View Requisitions')) {
//            abort(403, 'Access Denied');
//        }

        return view('issue_requisitions.index');
    }

    public function getRequisitionsIssue(Request $request)
    {
//        if (!Auth()->user()->checkPermission('View Requisitions Issue')) {
//            abort(403, 'Access Denied');
//        }

        if ($request->ajax()) {
            $data = Requisition::with(['reqDetails'])
                ->leftJoin(DB::raw('inv_stores as from_store'), 'requisitions.from_store', '=', 'from_store.id')
                ->leftJoin(DB::raw('inv_stores as to_store'), 'requisitions.to_store', '=', 'to_store.id')
                ->selectRaw('requisitions.*, to_store.name as toStore, from_store.name as fromStore')
                ->orderBy('requisitions.id', 'DESC');
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $btn_view = '';
                    if (Auth()->user()->can('Manage Product Categories')) {
                        if (Auth()->user()->can('Manage Product Categories')) {
                            $btn_view = '<a href="' . route('requisitions.issue', $row->id) . '" class="btn btn-warning btn-sm" title="ISSUE">Issue</a>';
                        }
                    }

                    return $btn_view;
                })
                ->addColumn('products', function ($row) {
                    $prod = '<span class="badge badge-primary p-1">' . $row->reqDetails->count() . ' Products</span>';
                    return $prod;
                })
                ->addColumn('reqDate', function ($row) {
                    return $row->created_at;
                })
                ->rawColumns(['action', 'products', 'reqTo', 'reqDate'])
                ->make(true);
        }
    }

    public function issue($id)
    {
    //        if (!Auth()->user()->checkPermission('View Requisitions Issue')) {
    //            abort(403, 'Access Denied');
    //        }

        $items = Product::where('status', 1)->get();
        $stores = Store::get();

        $requisition = Requisition::with(['reqDetails', 'creator'])->find($id);

        $fromStore = Store::findOrFail($requisition->from_store);
        $toStore = Store::findOrFail($requisition->to_store);

        $requisitionDet = RequisitionDetail::with('products_')
            ->leftJoin('inv_current_stock', 'inv_current_stock.product_id', 'requisition_details.product')
            ->selectRaw('requisition_details.*, sum(inv_current_stock.quantity) as qty_oh')
            ->groupBy('inv_current_stock.product_id')
            // ->havingRaw(DB::raw('sum(inv_current_stock.quantity) > 0'))
            // ->where('inv_current_stock.store_id', $fromStore)
            ->where('requisition_details.req_id', $id)
            ->get();

        // ADD CONCATENATION LOGIC HERE:
        $requisitionDet->each(function($detail) {
            if ($detail->products_) {
                $detail->products_->full_product_name = 
                    $detail->products_->name . ' ' . 
                    ($detail->products_->brand ?? '') . ' ' . 
                    ($detail->products_->pack_size ?? '') . ' ' . 
                    ($detail->products_->sales_uom ?? '');
            }
        });

        return view("issue_requisitions.show", compact('requisition', 'requisitionDet', 'fromStore', 'toStore', 'items', 'stores'));
    }

    public function issuing(Request $request)
    {
//        if (!Auth()->user()->checkPermission('View Requisitions Issue')) {
//            abort(403, 'Access Denied');
//        }

        $req_id = $request->requisition_id;
        $remarks = $request->remarks;
        $store_id = $request->store_id;

        $content = array_map(null, $request->product_id, $request->qty, $request->qty_req);
        // dd($content);

        foreach ($content as $value) {

            $product_id = $value[0];
            $qty_given = $value[1];
            $qty_req = $value[2];

            $current_stock = CurrentStock::where('product_id', $product_id)
                ->where('quantity', '=', 0)
                ->get();

            $previous_current_stock = CurrentStock::select('id')
                ->where('product_id', $product_id)
                ->orderby('id', 'desc')
                ->first();

            if (!($current_stock->isEmpty())) {
                //update
//                dd('kulwa');
                $get_current_stock = CurrentStock::find($current_stock->first()->id);

                $check_buy_price = number_format($get_current_stock->unit_cost, 2);


                $update_stock = CurrentStock::find($current_stock->first()->id);
                $update_stock->batch_number = null;

                //TODO: Commented not understood
//                if ($request->expire_date == "YES") {
//                    if ($single_item['expire_date'] != null) {
//                        $update_stock->expiry_date = date('Y-m-d', strtotime($single_item['expire_date']));
//                    } else {
//                        $update_stock->expiry_date = null;
//                    }
//                } else {
//                    $update_stock->expiry_date = null;
//                }

                $update_stock->quantity = $qty_given;
                $update_stock->store_id = $store_id;
                $update_stock->save();
                $overal_stock_id = $update_stock->id;
            } else {

                $stock = new CurrentStock;
                $stock->product_id = $product_id;
                $stock->batch_number = null;

//                if ($request->expire_date == "YES") {
//
//                    if ($single_item['expire_date'] != null) {
//                        $stock->expiry_date = date('Y-m-d', strtotime($single_item['expire_date']));
//                    } else {
//                        $stock->expiry_date = null;
//                    }
//                } else {
//                    $stock->expiry_date = null;
//                }

                $stock->expiry_date = null;
                $stock->quantity = $qty_given;
                $stock->unit_cost = null;
                $stock->store_id = $store_id;

                $stock->save();
                $overal_stock_id = $stock->id;
            }

            $stock_tracking = new StockTracking();
            $stock_tracking->stock_id = $overal_stock_id;
            $stock_tracking->product_id = $product_id;
            $stock_tracking->quantity = $qty_given;
            $stock_tracking->store_id = $store_id;
            $stock_tracking->updated_by = Auth::user()->id;
            $stock_tracking->out_mode = 'Requisition Issued';
            $stock_tracking->updated_at = date('Y-m-d');
            $stock_tracking->movement = 'IN';
            $stock_tracking->save();

            $remain_qty = $qty_req - $qty_given;

            RequisitionDetail::where('req_id', $req_id)
                ->where('product', $product_id)
                ->update(array('quantity_given' => $qty_given));
        }

        $req = Requisition::findOrFail($req_id);
        $req->remarks = $remarks;
        $req->updated_by = Auth::user()->id;
        $req->status = 1;
        $req->save();


        session()->flash("alert-success", "Requisition Accepted Successfully!");
        return back();
    }
}
