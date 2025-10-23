<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\PriceCategory;
use App\Product;
use App\PriceList;
use App\Setting;
use App\Sale;
use App\SalesDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Yajra\DataTables\DataTables;

class PriceListController extends Controller {

    public function index( Request $request ) {
        if (!Auth()->user()->checkPermission('View Price List')) {
            abort(403, 'Access Denied');
        }
        $store_id = current_store_id();
        $price_categories = PriceCategory::all();
        $batch_enabled = Setting::where( 'id', 110 )->value( 'value' );

        $query = DB::table( 'inv_current_stock' )
        ->join( 'inv_products', 'inv_current_stock.product_id', '=', 'inv_products.id' )
        ->join( 'sales_prices', 'inv_current_stock.id', '=', 'sales_prices.stock_id' )
        ->join( 'price_categories', 'sales_prices.price_category_id', '=', 'price_categories.id' )
        ->select(
            'inv_products.name as product_name',
            'inv_products.brand as brand',
            'inv_products.pack_size as pack_size',
            'inv_products.sales_uom as sales_uom',
            'inv_current_stock.unit_cost as unit_cost',
            'sales_prices.price as price',
            // DB::raw( '((sales_prices.price - inv_current_stock.unit_cost) / inv_current_stock.unit_cost) * 100 as profit' ),
            DB::raw("
                CASE 
                    WHEN inv_current_stock.unit_cost > 0 
                    THEN ((sales_prices.price - inv_current_stock.unit_cost) / inv_current_stock.unit_cost) * 100 
                    ELSE 0 
                END as profit
            "),
            'inv_current_stock.id as id',
            'price_categories.name as price_category_name',
            'sales_prices.price_category_id as price_category_id'
        )
        ->where( 'inv_current_stock.quantity', '>', 0 );
        if (!is_all_store()) {
            $query->where('inv_current_stock.store_id', $store_id);
        }

        $current_stocks = $query->get();

        // This query is for the 'History' view - with error handling for missing table
        try {
            $stocks = DB::table( 'stock_details' )
            ->join( 'inv_products', 'inv_products.id', '=', 'stock_details.product_id' )
            ->select( 'product_name as name', 'inv_products.brand as brand', 'inv_products.pack_size as pack_size', 'inv_products.sales_uom as sales_uom', 'unit_cost', 'selling_price as price', 'stock_details.updated_at', 'price_category_id' )
            ->where( 'store_id', $store_id )
            ->get();
        } catch (\Exception $e) {
            Log::warning('stock_details table query failed, using empty collection: ' . $e->getMessage());
            $stocks = collect(); // Return empty collection if table doesn't exist
        }

        return view( 'stock_management.price_list.index' )->with( [
            'price_categories' => $price_categories,
            'stocks' => $stocks,
            'current_stocks' => $current_stocks,
            'batch_enabled' => $batch_enabled
        ] );
    }

    public function fetchPriceList( Request $request ) {
        $store_id = current_store_id();
        $categoryId = $request->category_id;
        $type = $request->type;

      if ($type === 'pending') {
        $query = DB::table('inv_current_stock as cs')
        ->leftJoin('sales_prices as sp', function ($join) use ($categoryId) {
            $join->on('cs.id', '=', 'sp.stock_id')
                 ->where('sp.price_category_id', '=', $categoryId);
        })
        ->join('inv_products as p', 'cs.product_id', '=', 'p.id')
        ->select(
            'p.name as product_name',
            'p.brand as brand',
            'p.pack_size as pack_size',
            'p.sales_uom as sales_uom',
            'cs.unit_cost as unit_cost',
            'cs.batch_number as batch_number',
            DB::raw('0 as price'), 
            DB::raw('0 as profit'),
            'cs.id as id',
            DB::raw('NULL as price_category_id') 
        )
        ->where('cs.quantity', '>', 0)
        ->where('cs.unit_cost', '>', 0)
        ->whereNull('sp.id');
        if (!is_all_store()) {
            $query->where('cs.store_id', $store_id);
        }
        $stocks = $query->get();
        } elseif ($type === '1') {
            // Current
            $query = DB::table('inv_current_stock as cs')
                ->join('sales_prices as sp', 'cs.id', '=', 'sp.stock_id')
                ->join('inv_products as p', 'cs.product_id', '=', 'p.id')
                ->select(
                    'p.name as product_name',
                    'p.brand as brand',
                    'p.pack_size as pack_size',
                    'p.sales_uom as sales_uom',
                    'cs.unit_cost as unit_cost',
                    'cs.batch_number as batch_number',
                    'sp.price as price',
                    DB::raw('((sp.price - cs.unit_cost)/cs.unit_cost)*100 as profit'),
                    'cs.id as id',
                    'sp.price_category_id as price_category_id'
                )
                ->where('sp.price_category_id', $categoryId)
                ->where('cs.unit_cost', '>', 0)
                ->where('cs.quantity', '>', 0);
                if (!is_all_store()) {
                    $query->where('cs.store_id', $store_id);
                }
                $stocks = $query->get();
        } else {
            $query = DB::table('inv_current_stock as cs')
                ->join('sales_prices as sp', 'cs.id', '=', 'sp.stock_id')
                ->join('inv_products as p', 'cs.product_id', '=', 'p.id')
                ->select(
                    'p.name as product_name',
                    'p.brand as brand',
                    'p.pack_size as pack_size',
                    'p.sales_uom as sales_uom',
                    'cs.unit_cost as unit_cost',
                    'cs.batch_number as batch_number',
                    'sp.price as price',
                    DB::raw('((sp.price - cs.unit_cost)/cs.unit_cost)*100 as profit'),
                    'cs.id as id',
                    'sp.price_category_id as price_category_id',
                    'cs.created_at as purchased_at',
                    'sp.created_at as price_date'
                )
                ->where('sp.price_category_id', $categoryId)
                ->where('cs.unit_cost', '>', 0)
                ->where('cs.quantity', '>', 0)
                ->orderBy('cs.batch_number', 'desc')
                ->orderBy('sp.created_at', 'desc');
                if (!is_all_store()) {
                    $query->where('cs.store_id', $store_id);
                }
                $stocks = $query->get();
        }
        return response()->json( $stocks );
    }

    public function filteredPriceList() {
        $store_id = current_store_id();
        $stocks = DB::table( 'inv_current_stock' )
        ->join( 'inv_products', 'inv_current_stock.product_id', '=', 'inv_products.id' )
        ->join( 'sales_prices', 'inv_current_stock.id', '=', 'sales_prices.stock_id' )
        ->select( 'inv_current_stock.product_id', 'inv_products.name',
        'inv_current_stock.unit_cost',
        'sales_prices.price' )
        ->where( 'inv_current_stock.store_id', $store_id )
        ->groupBy( [ 'inv_current_stock.product_id' ] )
        ->get();

        return $stocks;
    }

    public function updateFormer( Request $request ) {
        if ( $request->id != null ) {
            $prices = PriceList::find( $request->id );
            $prices->stock_id = $request->stock_id;
            $prices->price = $request->sell_price;
            $prices->price_category_id = $request->price_category;
            $prices->status = intval( $request->status );

            $prices->save();

            session()->flash( 'alert-success', 'Price updated successfully!' );
            return redirect()->route( 'price-list.index' );
        } else {
            $this->store( $request );
            session()->flash( 'alert-success', 'Price updated successfully!' );
            return redirect()->route( 'price-list.index' );
        }
    }

    // public function update( Request $request ) {
    //     $request->validate( [
    //         'unit_cost' => 'required|numeric|min:0',
    //         'sell_price' => 'required|numeric|min:0',
    //         'price_category_id' => 'required|exists:price_categories,id',
    //         'selected_type' => 'required|string',
    //     ] );

    //     if ( !isset( $request->id ) ) {
    //         return redirect()->back()->with( 'alert-danger', 'Invalid request.' );
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $current_stock = CurrentStock::findOrFail( $request->id );

    //         $current_stock->update( [
    //             'unit_cost' => $request->unit_cost
    //         ] );

    //         if ( $request->selected_type === 'pending' ) {
    //             // Insert new price record
    //             PriceList::create( [
    //                 'stock_id' => $request->id,
    //                 'price_category_id' => $request->price_category_id,
    //                 'price' => $request->sell_price,
    //                 'created_by' => Auth::user()->id,
    //                 'updated_by' => Auth::user()->id,
    //             ] );
    //         } else if ( $request->selected_type === '1' ) {
    //             // Update existing record
    //             PriceList::where( 'stock_id', $request->id )
    //             ->update( [
    //                 'price_category_id' => $request->price_category_id,
    //                 'price' => $request->sell_price,
    //                 'updated_by' => Auth::user()->id,
    //             ] );
    //         }
    //         DB::commit();

    //         session()->flash( 'alert-success', 'Price updated successfully!' );
    //         return redirect()->route( 'price-list.index' );

    //     } catch ( \Exception $e ) {
    //         DB::rollBack();
    //         Log::error( 'Error updating price list: ' . $e->getMessage() );
    //         session()->flash( 'alert-danger', 'Failed to update price. Please try again.' );
    //         return redirect()->back()->withInput();
    //     }
    // }

    public function update(Request $request)
{
    $request->validate([
        'unit_cost' => 'required|numeric|min:0',
        'sell_price' => 'required|numeric|min:0',
        'price_category_id' => 'required|exists:price_categories,id',
        'selected_type' => 'required|string',
    ]);

    if (!isset($request->id)) {
        return redirect()->back()->with('alert-danger', 'Invalid request.');
    }

    DB::beginTransaction();
    try {
        // load the current stock (the batch being edited)
        $current_stock = CurrentStock::findOrFail($request->id);

        // update unit cost for this batch only (as before)
        $current_stock->update([
            'unit_cost' => $request->unit_cost
        ]);

        // get all stock ids for the same product (all batches of this product)
        $productId = $current_stock->product_id;
        $allStockIds = CurrentStock::where('product_id', $productId)->pluck('id')->toArray();

        $priceCategoryId = $request->price_category_id;
        $newPrice = $request->sell_price;
        $userId = Auth::user()->id;

        if ($request->selected_type === 'pending') {
            // Create PriceList for the current stock if not exists
            if (!PriceList::where('stock_id', $current_stock->id)
                          ->where('price_category_id', $priceCategoryId)
                          ->exists()) {
                PriceList::create([
                    'stock_id' => $current_stock->id,
                    'price_category_id' => $priceCategoryId,
                    'price' => $newPrice,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            } else {
                // if it already exists, update it
                PriceList::where('stock_id', $current_stock->id)
                    ->where('price_category_id', $priceCategoryId)
                    ->update([
                        'price' => $newPrice,
                        'updated_by' => $userId,
                    ]);
            }

            // For all other batches of the same product: create PriceList only where it DOES NOT exist
            $otherStockIds = array_diff($allStockIds, [$current_stock->id]);

            foreach ($otherStockIds as $sid) {
                $exists = PriceList::where('stock_id', $sid)
                    ->where('price_category_id', $priceCategoryId)
                    ->exists();

                if (! $exists) {
                    PriceList::create([
                        'stock_id' => $sid,
                        'price_category_id' => $priceCategoryId,
                        'price' => $newPrice,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                }
            }
        } elseif ($request->selected_type === '1') {
            // Update existing PriceList records for this price category across ALL batches of same product
            PriceList::whereIn('stock_id', $allStockIds)
                ->where('price_category_id', $priceCategoryId)
                ->update([
                    'price' => $newPrice,
                    'updated_by' => $userId,
                ]);
        }

        DB::commit();

        session()->flash('alert-success', 'Price updated successfully!');
        return redirect()->route('price-list.index');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error updating price list: ' . $e->getMessage());
        session()->flash('alert-danger', 'Failed to update price. Please try again.');
        return redirect()->back()->withInput();
    }
}


    public function store( Request $request ) {
        $prices = new PriceList;
        $prices->stock_id = $request->stock_id;
        $prices->price = $request->sell_price;
        $prices->price_category_id = $request->price_category;
        $prices->status = intval( $request->status );

        $prices->save();

        session()->flash( 'alert-success', 'Price updated successfully!' );
        return redirect()->route( 'price-list.index' );
    }

    public function destroy( Request $request ) {

    }

    public function priceHistory( Request $request ) {
        if ( $request->ajax() ) {
            try {
                $prices = DB::table( 'stock_details' )
                ->select( '*' )
                ->where( 'product_id', ' = ', $request->product_id )
                ->where( 'price_category_id', $request->price_category_id )
                ->orderBy( 'id', 'desc' )
                ->take( 5 )
                ->get();

                return json_decode( $prices, true );
            } catch (\Exception $e) {
                Log::warning('priceHistory query failed: ' . $e->getMessage());
                return response()->json(['error' => 'Price history not available'], 500);
            }

        }

    }

    public function priceCategory( Request $request ) {
        if ( $request->ajax() ) {
            $check = array();
            $price_list = PriceList::select( 'sales_prices.id as id', 'stock_id', 'price' )->where( 'price_category_id', $request->category_id )
            ->join( 'inv_current_stock', 'inv_current_stock.id', ' = ', 'sales_prices.stock_id' )
            ->join( 'inv_products', 'inv_products.id', ' = ', 'inv_current_stock.product_id' )
            ->orderBy( 'stock_id', 'desc' )
            ->where( 'product_id', $request->product_id )
            ->first( 'price' );

            if ( $price_list == null ) {
                array_push( $check, array(
                    'id' => $price_list,
                    'price' => '0',
                    'stock_id' => $price_list,
                ) );
                return $check;
            } else {
                array_push( $check, array(
                    'id' => $price_list->id,
                    'price' => $price_list->price,
                    'stock_id' => $price_list->stock_id,
                ) );
                return $check;
            }

        }
    }

    public function allPriceList( Request $request ) {

        $columns = array(
            0 => 'name',
            1 => 'unit_cost',
            2 => 'price',
            3 => 'name',
        );

        $category_id = $request->price_category;

        $totalData = PriceList::where( 'price_category_id', $category_id )
        ->join( 'inv_current_stock', 'inv_current_stock.id', ' = ', 'sales_prices.stock_id' )
        ->join( 'inv_products', 'inv_products.id', ' = ', 'inv_current_stock.product_id' )
        ->where( 'quantity', '>', 0 )
        ->Where( 'inv_products.status', '1' )
        ->groupBy( 'product_id' )
        ->get()
        ->count();

        $totalFiltered = $totalData;

        $limit = $request->input( 'length' );
        $start = $request->input( 'start' );
        $order = $columns[ $request->input( 'order.0.column' ) ];
        $dir = $request->input( 'order.0.dir' );

        if ( empty( $request->input( 'search.value' ) ) ) {
            $products = PriceList::where( 'price_category_id', $category_id )
            ->join( 'inv_current_stock', 'inv_current_stock.id', ' = ', 'sales_prices.stock_id' )
            ->join( 'inv_products', 'inv_products.id', ' = ', 'inv_current_stock.product_id' )
            ->where( 'quantity', '>', 0 )
            ->Where( 'inv_products.status', '1' )
            ->groupby( 'product_id' )
            ->offset( $start )
            ->limit( $limit )
            ->orderBy( $order, $dir )
            ->get();
        } else {
            $search = $request->input( 'search.value' );

            $products = PriceList::where( 'price_category_id', $category_id )
            ->join( 'inv_current_stock', 'inv_current_stock.id', ' = ', 'sales_prices.stock_id' )
            ->join( 'inv_products', 'inv_products.id', ' = ', 'inv_current_stock.product_id' )
            ->where( 'quantity', '>', 0 )
            ->where( 'unit_cost', 'LIKE', "%{$search}%" )
            ->orWhere( 'inv_products.name', 'LIKE', "%{$search}%" )
            ->orWhere( 'price', 'LIKE', "%{$search}%" )
            ->Where( 'inv_products.status', '1' )
            ->groupby( 'product_id' )
            ->offset( $start )
            ->limit( $limit )
            ->orderBy( $order, $dir )
            ->get();

            $totalFiltered = PriceList::where( 'price_category_id', $category_id )
            ->join( 'inv_current_stock', 'inv_current_stock.id', ' = ', 'sales_prices.stock_id' )
            ->join( 'inv_products', 'inv_products.id', ' = ', 'inv_current_stock.product_id' )
            ->where( 'quantity', '>', 0 )
            ->where( 'unit_cost', 'LIKE', "%{$search}%" )
            ->orWhere( 'inv_products.name', 'LIKE', "%{$search}%" )
            ->orWhere( 'price', 'LIKE', "%{$search}%" )
            ->Where( 'inv_products.status', '1' )
            ->groupby( 'sales_prices.stock_id' )
            ->count();
        }

        $data = array();
        if ( !empty( $products ) ) {
            foreach ( $products as $product ) {

                if ( $product->status != 0 ) {
                    try {
                        $datas = PriceList::select( 'stock_id', 'price' )
                        ->where( 'price_category_id', $category_id )
                        ->join( 'inv_current_stock', 'inv_current_stock.id', ' = ', 'sales_prices.stock_id' )
                        ->join( 'inv_products', 'inv_products.id', ' = ', 'inv_current_stock.product_id' )
                        ->orderBy( 'sales_prices.id', 'desc' )
                        ->where( 'product_id', $product->id )
                        ->first( 'price' );

                        $quantity = CurrentStock::where( 'product_id', $product->id )->sum( 'quantity' );

                        $nestedData[ 'name' ] = $datas->currentStock[ 'product' ][ 'name' ];
                        $nestedData[ 'unit_cost' ] = $datas->currentStock[ 'unit_cost' ];
                        $nestedData[ 'price' ] = $datas->price;
                        $nestedData[ 'quantity' ] = $quantity;
                        $nestedData[ 'id' ] = $datas->stock_id;
                        $nestedData[ 'product_id' ] = $product->id;

                        $data[] = $nestedData;
                    } catch ( Exception $exception ) {
                        Log::info( 'AllPriceRetrieveException', [ 'ErrorDetails'=>$exception ] );
                    }
                }

            }
        }

        try {
            $store_id = Auth::user()->store_id;
            $products = DB::table( 'stock_details' )
            ->select( 'id', 'product_name as name', 'unit_cost', 'selling_price as price', 'created_at' )
            ->where( 'store_id', $store_id )
            ->get();
        } catch (\Exception $e) {
            Log::warning('allPriceList stock_details query failed: ' . $e->getMessage());
            $products = collect(); // Return empty collection if table doesn't exist
        }

        $json_data = array(
            'draw' => intval( $request->input( 'draw' ) ),
            'recordsTotal' => intval( $totalData ),
            'recordsFiltered' => intval( $totalFiltered ),
            'data' => $products
        );

        echo json_encode( $json_data );

    }

}
