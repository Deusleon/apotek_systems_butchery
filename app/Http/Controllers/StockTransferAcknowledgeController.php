<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\PriceList;
use App\Product;
use App\Setting;
use App\StockTracking;
use App\StockTransfer;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockTransferAcknowledgeController extends Controller
 {

    public function index( $transfer_no )
 {
        $stores = Store::where( 'name', '<>', 'ALL' )->get();
        $store_id = Auth::user()->store_id;
        $all_transfers = StockTransfer::where( 'status', '=', 'approved' )
        ->where( 'transfer_no', '=', $transfer_no )
        ->get();

        return view( 'stock_management.stock_transfer_acknowledge.index' )->with( [
            'stores' => $stores,
            'all_transfers' => $all_transfers
        ] );
    }

    public function fetchTransferToAcknowledge( $transfer_no )
 {
        Log::info( 'Fetching transfer to acknowledge', [ 'transfer_no' => $transfer_no ] );
        $transfer = StockTransfer::where( 'transfer_no', $transfer_no )
        ->where( 'status', '=', 'approved' )
        ->with( [ 'currentStock.product', 'fromStore', 'toStore' ] )
        ->get();

        if ( $transfer->isEmpty() ) {
            return response()->json( [ 'error' => 'Transfer not found' ], 404 );
        }

        return response()->json( $transfer );
    }

    public function store( Request $request )
 {

    }

    public function destroy( Request $request )
 {

    }

    public function transferFilterDetailComplete( Request $request )
 {

        $from = $request->get( 'from_val' );
        $to = $request->get( 'to_val' );

        if ( $request->ajax() ) {

            if ( $from != 0 && $to != 0 ) {

                $results = StockTransfer::where( 'status', '=', '1' )
                ->where( 'from_store', '=', $from )
                ->where( 'to_store', '=', $to )
                ->get();

                foreach ( $results as $value ) {
                    $value->toStore;
                    $value->fromStore;
                    $value->currentStock[ 'product' ];

                }

                return json_decode( $results, true );

            } else if ( $from == 0 && $to != 0 ) {

                // $results = StockTransfer::where( 'status', '=', '1' )->where( 'to_store', '=', $to )->get();

                $results = StockTransfer::where( 'status', '=', '1' )
                ->where( 'to_store', '=', $to )
                ->get();

                foreach ( $results as $value ) {
                    $value->toStore;
                    $value->fromStore;
                    $value->currentStock[ 'product' ];
                }

                return json_decode( $results, true );

            } else if ( $from != 0 && $to == 0 ) {

                // $results = StockTransfer::where( 'status', '=', '1' )->where( 'from_store', '=', $from )->get();

                $results = StockTransfer::where( 'status', '=', '1' )
                ->where( 'from_store', '=', $from )
                ->get();

                foreach ( $results as $value ) {
                    $value->toStore;
                    $value->fromStore;
                    $value->currentStock[ 'product' ];
                }

                return json_decode( $results, true );

            }

        }

    }

    public function stockTransferComplete( Request $request )
 {

        if ( $request->ajax() ) {
            //update the transfer table
            if ( $this->update( $request ) == true ) {
                //return updated table
                $this->transferFilter( $request );
            }
        }

    }

    public function update( Request $request )
    {

    }

    public function acknowledgeTransfer( Request $request )
 {
        // Validate basic inputs ( adjust rules as needed )
        $request->validate( [
            'transfer_no' => 'required|string',
            'from_id' => 'required|integer',
            'to_id' => 'required|integer',
            // Expecting array of transfers like transfers[ 0 ][ id ], transfers[ 0 ][ accepted_qty ], etc.
            'transfers' => 'required|array',
        ] );

        $transferNo = $request->input( 'transfer_no' );
        $fromStore = ( int )$request->input( 'from_id' );
        $toStore = ( int )$request->input( 'to_id' );
        $submittedTransfers = $request->input( 'transfers', [] );
        $remarkText = $request->input( 'remarks' ) ?? "Acknowledge transfer {$transferNo}";

        // Use DB transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // 2 ) Tafuta inv_stock_transfers kwa transfer_no, from na to
            $transfers = StockTransfer::where( 'transfer_no', $transferNo )
            ->where( 'from_store', $fromStore )
            ->where( 'to_store', $toStore )
            ->get();

            if ( $transfers->isEmpty() ) {
                DB::rollBack();
                return back()->with( 'alert-danger', 'No transfer records found.' );
            }

            // Map submitted accepted_qty by transfer id ( or index ) for convenience
            // Expecting structure: transfers[ index ][ id ], transfers[ index ][ accepted_qty ]
            $acceptedMap = [];
            foreach ( $submittedTransfers as $i => $t ) {
                if ( isset( $t[ 'id' ] ) ) {
                    $acceptedMap[ $t[ 'id' ] ] = ( int ) str_replace( ',', '', ( $t[ 'accepted_qty' ] ?? 0 ) );
                } elseif ( isset( $t[ 'stock_id' ] ) ) {
                    // fallback if front-end used stock_id as identifier
                    $acceptedMap[ $t[ 'stock_id' ] ] = ( int ) str_replace( ',', '', ( $t[ 'accepted_qty' ] ?? 0 ) );
                } else {
                    // fallback by index: we try to use numeric index mapping
                    $acceptedMap[ $i ] = ( int ) str_replace( ',', '', ( $t[ 'accepted_qty' ] ?? 0 ) );
                }
            }

            // For each transfer item, apply acknowledge logic
            foreach ( $transfers as $index => $transfer ) {
                // stock_id originally referenced in inv_stock_transfers ( the source/current stock record )
                $stockId = $transfer->stock_id;

                // Attempt to find the accepted_qty submitted for this transfer.
                // We try by transfer->id, then by stock_id, then by index.
                $acceptedToday = 0;
                if ( isset( $acceptedMap[ $transfer->id ] ) ) {
                    $acceptedToday = max( 0, ( int )$acceptedMap[ $transfer->id ] );
                } elseif ( isset( $acceptedMap[ $stockId ] ) ) {
                    $acceptedToday = max( 0, ( int )$acceptedMap[ $stockId ] );
                } elseif ( isset( $acceptedMap[ $index ] ) ) {
                    $acceptedToday = max( 0, ( int )$acceptedMap[ $index ] );
                }

                $transferedQty = ( int ) str_replace( ',', '', $transfer->transfer_qty );

                // previously accepted ( may be null )
                $alreadyAccepted = ( int ) ( $transfer->accepted_qty ?? 0 );

                // new total accepted after this acknowledge action
                $newAcceptedTotal = $alreadyAccepted + $acceptedToday;

                // remaining quantity ( not accepted ) from the original transferred quantity
                $remaining = $transferedQty - $newAcceptedTotal;
                if ( $remaining < 0 ) $remaining = 0;

                // 2b ) Get the source current stock ( the stock record referenced by stock_id )
                $sourceStock = CurrentStock::find( $stockId );

                // if ( $sourceStock ) {
                // 4 ) Update current stock of the store transfer inapoelekea ( source adjustments ):
                // Assumption: when transfer was created you previously reduced the source stock.
                // Now we return the 'remaining' back to source stock quantity.
                // ( If your flow is different adjust this logic. )
                // $sourceStock->quantity = ( int )$sourceStock->quantity + $remaining;
                // $sourceStock->save();
                // }

                // 4 ) Update or create current stock entry for the destination store ( to_store )
                // Find existing CurrentStock for same product_id in the destination store.
                $productId = $transfer->currentStock[ 'product_id' ] ?? ( $sourceStock->product_id ?? null );

                if ( $productId === null ) {
                    // skip if product cannot be determined
                    continue;
                }

                // $destStock = CurrentStock::where( 'product_id', $productId )
                // ->where( 'store_id', $toStore )
                // ->first();

                // if ( $destStock ) {
                //     // add acceptedToday to existing destination stock
                //     $destStock->quantity = ( int )$destStock->quantity + $acceptedToday;
                // } else {
                // create a new current stock record for the destination store
                $destStock = new CurrentStock();
                $destStock->product_id = $productId;
                // no expiry, no batch ( as requested ). Set unit_cost to source if available, else 0
                $destStock->expiry_date = null;
                $destStock->batch_number = null;
                $destStock->unit_cost = $sourceStock->unit_cost ?? 0;
                $destStock->quantity = $acceptedToday;
                $destStock->store_id = $toStore;
                $destStock->mode = 'Stock Transfer';
                $destStock->created_by = Auth::id();
                // }
                $destStock->save();

                // 3 ) Update StockTransfer record accepted_qty and status
                $transfer->accepted_qty = $newAcceptedTotal;
                $transfer->notes = $remarkText;
                $transfer->acknowledged_by = Auth::id();
                $transfer->acknowledged_at = now();
                // Decide status: completed if fully accepted else acknowledged
                // NOTE: adapt these status values to match your system ( strings vs numeric ).
                if ( $newAcceptedTotal >= $transferedQty ) {
                    $transfer->status = 'completed';
                } else {
                    $transfer->status = 'acknowledged';
                }
                $transfer->save();

                // 5 ) StockTracking: record movements
                // a ) If there is remaining ( i.e., returned to source ), record it for source store as IN
                // if ( $remaining > 0 && $sourceStock ) {
                //     $stkTrack = new StockTracking();
                //     $stkTrack->stock_id = $stockId;
                //     $stkTrack->product_id = $productId;
                //     $stkTrack->quantity = $remaining;
                //     $stkTrack->store_id = $sourceStock->store_id ?? $fromStore;
                //     $stkTrack->updated_by = Auth::id();
                //     $stkTrack->out_mode = 'Stock Transfer Acknowledged - Remaining Returned';
                //     $stkTrack->updated_at = now()->toDateString();
                //     $stkTrack->movement = 'IN';
                //     $stkTrack->save();
                // }

                // b ) If there is acceptedToday > 0, record IN to destination store
                if ( $acceptedToday > 0 ) {
                    $stkTrack2 = new StockTracking();
                    // we use the original stock_id reference to show source link;
                    $stkTrack2->stock_id = $stockId;
                    $stkTrack2->product_id = $productId;
                    $stkTrack2->quantity = $acceptedToday;
                    $stkTrack2->store_id = $toStore;
                    $stkTrack2->created_by = Auth::id();
                    $stkTrack2->out_mode = ( $newAcceptedTotal >= $transferedQty ) ? 'Stock Transfer Completed' : 'Stock Transfer Acknowledged';
                    $stkTrack2->updated_at = now()->toDateString();
                    $stkTrack2->movement = 'IN';
                    $stkTrack2->save();
                }
            }
            // end foreach transfers

            DB::commit();

            session()->flash( 'alert-success', 'Transfer(s) acknowledged successfully!' );
            return redirect()->route( 'stock-transfer-history' );

        } catch ( \Exception $e ) {
            DB::rollBack();
            Log::error( 'AcknowledgeTransfer error: ' . $e->getMessage(), [
                'transfer_no' => $transferNo,
                'from' => $fromStore,
                'to' => $toStore,
                'exception' => $e,
            ] );

            return back()->with( 'alert-danger', 'Failed to acknowledge transfer: ' . $e->getMessage() );
        }
    }

    public function filterTransfer( Request $request )
 {

        $from = $request->get( 'from_val' );
        $to = $request->get( 'to_val' );

        if ( $request->ajax() ) {

            $results = StockTransfer::with( [ 'fromStore', 'toStore' ] )
            ->select( DB::raw( '*, SUM(transfer_qty) as quantity, SUM(accepted_qty) as received_quantity, COUNT(*) as total_products , MIN(status) as status' ) )
            ->orderBy( 'created_at', 'Desc' )
            ->groupBy( 'transfer_no' )->get();
            if ( $from != 0 && $to != 0 ) {
                $results = StockTransfer::with( [ 'fromStore', 'toStore' ] )
                ->where( 'from_store', '=', $from )
                ->where( 'to_store', '=', $to )
                ->select( DB::raw( '*, SUM(transfer_qty) as quantity, SUM(accepted_qty) as received_quantity, COUNT(*) as total_products , MIN(status) as status' ) )
                ->orderBy( 'created_at', 'Desc' )
                ->groupBy( 'transfer_no' )->get();
            }

            if ( $from = 0 && $to != 0 ) {
                $results = StockTransfer::with( [ 'fromStore', 'toStore' ] )
                ->where( 'to_store', '=', $to )
                ->select( DB::raw( '*, SUM(transfer_qty) as quantity, SUM(accepted_qty) as received_quantity, COUNT(*) as total_products , MIN(status) as status' ) )
                ->orderBy( 'created_at', 'Desc' )
                ->groupBy( 'transfer_no' )->get();
            }

            if ( $from != 0 && $to = 0 ) {
                $results = StockTransfer::with( [ 'fromStore', 'toStore' ] )
                ->where( 'from_store', '=', $from )
                ->select( DB::raw( '*, SUM(transfer_qty) as quantity, SUM(accepted_qty) as received_quantity, COUNT(*) as total_products , MIN(status) as status' ) )
                ->orderBy( 'created_at', 'Desc' )
                ->groupBy( 'transfer_no' )->get();
            }

        }

        Log::info( 'FilteredData', [ 'Results'=>$results ] );

        return json_decode( $results, true );
    }
    public function transferFilter( Request $request )
 {

        $from = $request->get( 'from_val' );
        $to = $request->get( 'to_val' );

        if ( $request->ajax() ) {

            if ( $from != 0 && $to != 0 ) {

                $results = StockTransfer::with( [ 'fromStore', 'toStore' ] )
                ->select( DB::raw( '*,transfer_no,date_format(created_at,"%d-%m-%Y") as date,sum(transfer_qty) as transfer_qty' ) )
                ->where( 'to_store', '=', $to )
                ->where( 'from_store', '=', $from )
                ->groupby( 'transfer_no' )
                ->orderby( 'created_at', 'DESC' )
                ->get();

                Log::info( 'FilteredData2', [ 'Results'=>$results ] );

                return json_decode( $results, true );

            } else if ( $from == 0 && $to != 0 ) {

                $results = StockTransfer::with( [ 'fromStore', 'toStore' ] )
                ->select( DB::raw( '*,transfer_no,date_format(created_at,"%d-%m-%Y") as date,sum(transfer_qty) as transfer_qty' ) )
                ->where( 'to_store', '=', $to )
                ->groupby( 'transfer_no' )
                ->orderby( 'created_at', 'DESC' )
                ->get();

                Log::info( 'FilteredData2', [ 'Results'=>$results ] );

                return json_decode( $results, true );

            } else if ( $from != 0 && $to == 0 ) {

                $results = StockTransfer::with( [ 'fromStore', 'toStore' ] )
                ->select( DB::raw( '*,transfer_no,date_format(created_at,"%d-%m-%Y") as date,sum(transfer_qty) as transfer_qty' ) )
                ->where( 'from_store', '=', $from )
                ->groupby( 'transfer_no' )
                ->orderby( 'created_at', 'DESC' )
                ->get();

                Log::info( 'FilteredData2', [ 'Results'=>$results ] );

                return json_decode( $results, true );

            }

        }

    }

    public function stockTransferShow( Request $request )
 {

        $from = $request->get( 'from_val' );
        $to = $request->get( 'to_val' );
        $transfer_no = $request->get( 'transfer_no' );

        $to_name = '';
        $from_name = '';
        $product_name = '';
        $stores = array();
        $info = array();
        $products = Product::all();

        if ( $request->ajax() ) {
            if ( $from != 0 && $to != 0 ) {

                $results = StockTransfer::with( [ 'fromStore', 'toStore' ] )->
                join( 'inv_current_stock', 'inv_current_stock.id', '=', 'inv_stock_transfers.stock_id' )
                ->join( 'inv_products', 'inv_current_stock.product_id', '=', 'inv_products.id' )
                ->select( '*', 'inv_products.name as product_name' )
                ->where( 'transfer_no', $transfer_no )
                ->get();

                Log::info( 'DataLoaded', [ 'Results'=>$results ] );

                return json_decode( $results, true );

            } else {

                $results = StockTransfer::with( [ 'fromStore', 'toStore' ] )->join( 'inv_current_stock', 'inv_current_stock.id', '=', 'inv_stock_transfers.stock_id' )
                ->join( 'inv_products', 'inv_current_stock.product_id', '=', 'inv_products.id' )
                ->select( '*', 'inv_products.name as product_name' )
                ->where( 'transfer_no', $transfer_no )
                ->get();

                Log::info( 'DataLoaded', [ 'Results'=>$results ] );

                return json_decode( $results, true );

            }

        }
    }

}
