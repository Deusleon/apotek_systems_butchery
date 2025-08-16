<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\PriceCategory;
use App\Product;
use App\PriceList;
use App\Sale;
use App\SalesDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Yajra\DataTables\DataTables;

class PriceListController extends Controller
{

    public function index(Request $request)
    {

        $price_categories = PriceCategory::all();

        $current_stocks = DB::table('inv_current_stock')
            ->join('inv_products', 'inv_current_stock.product_id', '=', 'inv_products.id')
            ->join('sales_prices', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
            ->join('price_categories', 'sales_prices.price_category_id', '=', 'price_categories.id')
            ->select(
                'inv_products.name as product_name',
                'inv_products.brand as brand',
                'inv_products.pack_size as pack_size',
                'inv_products.sales_uom as sales_uom',
                'inv_current_stock.unit_cost as unit_cost',
                'sales_prices.price as price',
                DB::raw('((sales_prices.price - inv_current_stock.unit_cost) / inv_current_stock.unit_cost) * 100 as profit'),
                'inv_current_stock.id as id',
                'price_categories.name as price_category_name',
                'sales_prices.price_category_id as price_category_id'
            )
            ->where('inv_current_stock.quantity', '>', 0)
            ->get();

        // This query is for the 'History' view, it should be separate
        $store_id = Auth::user()->store_id;
        $stocks = DB::table('stock_details')
            ->select('product_name as name','unit_cost','selling_price as price','updated_at','price_category_id')
            ->where('store_id',$store_id)
            ->get();

        return view('stock_management.price_list.index')->with([
            'price_categories' => $price_categories,
            'stocks' => $stocks,
            'current_stocks' => $current_stocks
        ]);
    }


    public function filteredPriceList()
    {
        $store_id = Auth::user()->store_id;
        $stocks = DB::table('inv_current_stock')
            ->join('inv_products','inv_current_stock.product_id','=','inv_products.id')
            ->join('sales_prices','inv_current_stock.id','=','sales_prices.stock_id')
            ->select('inv_current_stock.product_id','inv_products.name',
                'inv_current_stock.unit_cost',
                'sales_prices.price')
            ->where('inv_current_stock.store_id',$store_id)
            ->groupBy(['inv_current_stock.product_id'])
            ->get();

        return $stocks;
    }

    public function updateFormer(Request $request)
    {
        if ($request->id != null) {
            $prices = PriceList::find($request->id);
            $prices->stock_id = $request->stock_id;
            $prices->price = $request->sell_price;
            $prices->price_category_id = $request->price_category;
            $prices->status = intval($request->status);

            $prices->save();

            session()->flash("alert-success", "Price updated successfully!");
            return redirect()->route('price-list.index');
        } else {
            $this->store($request);
            session()->flash("alert-success", "Price updated successfully!");
            return redirect()->route('price-list.index');
        }
    }

       public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:100',
            'pack_size' => 'required|string|max:50',
            'unit_cost' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'price_category_id' => 'required|exists:price_categories,id',
        ]);

        if ($request->id != null && isset($request->id)) {
            DB::beginTransaction();
            try {
                //Get the product ID for the Product updated
                $current_stock = CurrentStock::findOrFail($request->id);
                $product_id = $current_stock->product_id;

                // Update name, brand and pack size for the product
                Product::where('id', $product_id)
                    ->update([
                        'name' => $request->name,
                        'brand' => $request->brand,
                        'pack_size' => $request->pack_size,
                    ]);

                //Update Buying Price for the specific stock item
                $current_stock->update([
                    'unit_cost' => $request->unit_cost
                ]);

                //Get all the stock IDS that has the same product with + Quantity > 0
                PriceList::where('stock_id', $request->id)
                ->where('price_category_id', $request->price_category_id)
                ->update([
                    'price' => $request->sell_price
                ]);

                DB::commit();



                session()->flash("alert-success", "Price updated successfully!");
                return redirect()->route('price-list.index');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error updating price list: ' . $e->getMessage());
                session()->flash("alert-danger", "Failed to update price. Please try again.");
                return redirect()->back()->withInput();
            }
        } else {
            // This part seems to be for creating a new one, might need review if it's used.
            // For now, focusing on the update.
            return redirect()->back()->with('alert-danger', 'Invalid request.');
        }
    }

    public function store(Request $request)
    {
        $prices = new PriceList;
        $prices->stock_id = $request->stock_id;
        $prices->price = $request->sell_price;
        $prices->price_category_id = $request->price_category;
        $prices->status = intval($request->status);

        $prices->save();

        session()->flash("alert-success", "Price updated successfully!");
        return redirect()->route('price-list.index');
    }

    public function destroy(Request $request)
    {

    }

    public function priceHistory(Request $request)
    {
        if ($request->ajax()) {
            $prices = DB::table('stock_details')
                ->select('*')
                ->where('product_id', '=', $request->product_id)
                ->where('price_category_id', $request->price_category_id)
                ->orderBy('id', 'desc')
                ->take(5)
                ->get();


            return json_decode($prices, true);

        }

    }

    public function priceCategory(Request $request)
    {
        if ($request->ajax()) {
            $check = array();
            $price_list = PriceList::select('sales_prices.id as id', 'stock_id', 'price')->where('price_category_id', $request->category_id)
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->orderBy('stock_id', 'desc')
                ->where('product_id', $request->product_id)
                ->first('price');

            if ($price_list == null) {
                array_push($check, array(
                    'id' => $price_list,
                    'price' => '0',
                    'stock_id' => $price_list,
                ));
                return $check;
            } else {
                array_push($check, array(
                    'id' => $price_list->id,
                    'price' => $price_list->price,
                    'stock_id' => $price_list->stock_id,
                ));
                return $check;
            }

        }
    }

    public function allPriceList(Request $request)
    {

        $columns = array(
            0 => 'name',
            1 => 'unit_cost',
            2 => 'price',
            3 => 'name',
        );

        $category_id = $request->price_category;

        $totalData = PriceList::where('price_category_id', $category_id)
            ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
            ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
            ->where('quantity', '>', 0)
            ->Where('inv_products.status', '1')
            ->groupBy('product_id')
            ->get()
            ->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $products = PriceList::where('price_category_id', $category_id)
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->where('quantity', '>', 0)
                ->Where('inv_products.status', '1')
                ->groupby('product_id')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $products = PriceList::where('price_category_id', $category_id)
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->where('quantity', '>', 0)
                ->where('unit_cost', 'LIKE', "%{$search}%")
                ->orWhere('inv_products.name', 'LIKE', "%{$search}%")
                ->orWhere('price', 'LIKE', "%{$search}%")
                ->Where('inv_products.status', '1')
                ->groupby('product_id')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = PriceList::where('price_category_id', $category_id)
                ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                ->where('quantity', '>', 0)
                ->where('unit_cost', 'LIKE', "%{$search}%")
                ->orWhere('inv_products.name', 'LIKE', "%{$search}%")
                ->orWhere('price', 'LIKE', "%{$search}%")
                ->Where('inv_products.status', '1')
                ->groupby('sales_prices.stock_id')
                ->count();
        }

        $data = array();
        if (!empty($products)) {
            foreach ($products as $product) {

                if ($product->status != 0) {
                    try {
                        $datas = PriceList::select('stock_id', 'price')
                            ->where('price_category_id', $category_id)
                            ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                            ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                            ->orderBy('sales_prices.id', 'desc')
                            ->where('product_id', $product->id)
                            ->first('price');

                        $quantity = CurrentStock::where('product_id', $product->id)->sum('quantity');


                        $nestedData['name'] = $datas->currentStock['product']['name'];
                        $nestedData['unit_cost'] = $datas->currentStock['unit_cost'];
                        $nestedData['price'] = $datas->price;
                        $nestedData['quantity'] = $quantity;
                        $nestedData['id'] = $datas->stock_id;
                        $nestedData['product_id'] = $product->id;

                        $data[] = $nestedData;
                    } catch (Exception $exception) {
                        Log::info("AllPriceRetrieveException",['ErrorDetails'=>$exception]);
                    }
                }

            }
        }

        $store_id = Auth::user()->store_id;
        $products = DB::table('stock_details')
            ->select('id','product_name as name','unit_cost','selling_price as price','created_at')
            ->where('store_id',$store_id)
            ->get();

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $products
        );

        echo json_encode($json_data);


    }

}
