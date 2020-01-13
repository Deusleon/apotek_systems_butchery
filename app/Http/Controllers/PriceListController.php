<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\PriceCategory;
use App\PriceList;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceListController extends Controller
{

    public function index()
    {

        $max_prices = array();
        $products = PriceList::where('price_category_id', 1)
            ->join('inv_current_stock', 'inv_current_stock.id', '=', 'sales_prices.stock_id')
            ->join('inv_products', 'inv_products.id', '=', 'inv_current_stock.product_id')
            ->where('quantity', '>', 0)
            ->Where('inv_products.status', '1')
            ->select('inv_products.id as id', 'name')
            ->groupBy('product_id')
            ->get();

        foreach ($products as $product) {
            $data = PriceList::select('stock_id', 'price')->where('price_category_id', 1)
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

        $data = PriceList::all();

        $price_categories = PriceCategory::all();

        return view('stock_management.price_list.index')->with([
            'data' => $data,
            'max_prices' => $max_prices,
            'price_categories' => $price_categories
        ]);
    }

    public function update(Request $request)
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

                    }
                }

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
