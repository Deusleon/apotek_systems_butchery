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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurrentStockController extends Controller
{

    public function index()
    {
        /*get default store*/
        $default_store = Setting::where('id', 122)->value('value');
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


        $stock_detail = CurrentStock::all();
        $price_Category = PriceCategory::all();
        $products = Product::all();
        $sale_price = PriceList::all();
        $adjustment_reason = AdjustmentReason::all('reason');
        $categories = Category::all();

        return view('stock_management.current_stock.index')->with([
            'current_stock' => $current_stock,
            'stock_details' => $stock_detail,
            'stores' => $stores,
            'products' => $products,
            'categories' => $categories,
            'reasons' => $adjustment_reason,
            'price_categories' => $price_Category,
            'sale_prices' => $sale_price,
            'default_store_id' => $default_store_id
        ]);
    }

    public function update(Request $request)
    {
        // check if sell price ids is present or create one
        if ($request->sales_id != null) {
            $prices = PriceList::find($request->sales_id);

            /*
             * if id present then check if price category is the same update else,
                create new sale price with new price category
            */
            if ($prices->price_category_id == $request->store_name) {
                //update
                $prices->stock_id = $request->stock_id;
                $prices->price = str_replace(',', '', $request->sell_price);
                $prices->price_category_id = $request->store_name;

                if ($request->page_pricing != null) {
                    $stock = CurrentStock::find($request->stock_id);
                    $stock->product_id = $request->product_id;
                    $stock->unit_cost = str_replace(',', '', $request->unit_cost);
                } else {
                    $stock = CurrentStock::find($request->id);
                    $stock->product_id = $request->product_id;
                    $stock->expiry_date = $request->expiry_date;
                    $stock->batch_number = $request->batch_number;
                    $stock->unit_cost = str_replace(',', '', $request->unit_cost);
                    $stock->quantity = str_replace(',', '', $request->quantity);
                    $stock->store_id = $request->store_id;
                    $stock->shelf_number = $request->shelf_number;
                }

                $stock->save();
                $prices->save();

            } else {
                //create new
                $prices = new PriceList;
                $prices->stock_id = $request->stock_id;
                $prices->price = str_replace(',', '', $request->sell_price);
                $prices->price_category_id = $request->store_name;
                $prices->status = 1;
                $prices->created_at = date('Y-m-d');

                if ($request->page_pricing != null) {
                    $stock = CurrentStock::find($request->stock_id);
                    $stock->product_id = $request->product_id;
                    $stock->unit_cost = str_replace(',', '', $request->unit_cost);
                } else {
                    $stock = CurrentStock::find($request->id);
                    $stock->product_id = $request->product_id;
                    $stock->expiry_date = $request->expiry_date;
                    $stock->batch_number = $request->batch_number;
                    $stock->unit_cost = str_replace(',', '', $request->unit_cost);
                    $stock->quantity = str_replace(',', '', $request->quantity);
                    $stock->store_id = $request->store_id;
                    $stock->shelf_number = $request->shelf_number;
                }


                $stock->save();
                $prices->save();
            }


            session()->flash("alert-success", "Stock Detail updated successfully!");
            return back();
        } else {

            $prices = new PriceList;
            $prices->stock_id = $request->stock_id;
            $prices->price = str_replace(',', '', $request->sell_price);
            $prices->price_category_id = $request->store_name;
            $prices->status = 1;
            $prices->created_at = date('Y-m-d');

            if ($request->page_pricing != null) {
                $stock = CurrentStock::find($request->stock_id);
                $stock->product_id = $request->product_id;
                $stock->unit_cost = str_replace(',', '', $request->unit_cost);
            } else {
                $stock = CurrentStock::find($request->id);
                $stock->product_id = $request->product_id;
                $stock->expiry_date = $request->expiry_date;
                $stock->batch_number = $request->batch_number;
                $stock->unit_cost = str_replace(',', '', $request->unit_cost);
                $stock->quantity = str_replace(',', '', $request->quantity);
                $stock->store_id = $request->store_id;
                $stock->shelf_number = $request->shelf_number;
            }


            $stock->save();
            $prices->save();

            session()->flash("alert-success", "Stock Detail updated successfully!");
            return back();
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
                $current_stock = PriceList::select('stock_id', 'price')
                    ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
                    ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                    ->orderBy('stock_id', 'desc')
                    ->where('product_id', $request->get("val"))
                    ->where('store_id', $request->store_id)
                    ->first('price');

                $quantity = CurrentStock::where('product_id', $request->get("val"))
                    ->where('store_id', $request->store_id)
                    ->sum('quantity');

                array_push($max_prices, array(
                    'name' => $current_stock->currentStock['product']['name'],
                    'unit_cost' => $current_stock->currentStock['unit_cost'],
                    'price' => $current_stock->price,
                    'quantity' => $quantity,
                    'stock_id' => $current_stock->stock_id,
                    'product_id' => $request->get("val")
                ));


                return $max_prices;


            } else {
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
                    ->where('category_id', $request->category)
                    ->where('store_id', $request->store_id)
                    ->groupBy('product_id')
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
                        ->where('category_id', $request->category)
                        ->where('store_id', $request->store_id)
                        ->groupBy('product_id')
                        ->havingRaw(DB::raw('sum(quantity) > 0'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
                } else {
                    $stocks = CurrentStock::select('product_id', DB::raw('sum(quantity) as quantity'))
                        ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
                        ->where('store_id', $request->store_id)
                        ->groupBy('product_id')
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

}
