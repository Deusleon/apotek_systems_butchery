<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\ProductStoreRequest;
use App\Product;
use App\SubCategory;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index()
    {

        $products = Product::all();
        $category = Category::all();
        $sub_category = SubCategory::all();

        return view('masters.products.index')->with(['products' => $products,
            'categories' => $category,
            'sub_categories' => $sub_category]);
    }


    public function store(ProductStoreRequest $request)
    {

        $request->validated();

        $product = new Product;
        $product->name = $request->name;
        $product->barcode = $request->barcode;
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category;
        $product->generic_name = $request->generic_name;
        $product->standard_uom = $request->standardUoM;
        $product->sales_uom = $request->saleUoM;
        $product->purchase_uom = $request->purchaseUoM;
        $product->indication = $request->indication;
        $product->dosage = $request->dosage;
        $product->status = intval($request->status);
        $product->min_quantinty = $request->min_stock;
        $product->max_quantinty = $request->max_stock;

        $product->save();

        session()->flash("alert-success", "Product added successfully!");
        return back();
    }


    public function update(Request $request)
    {
        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->generic_name = $request->generic;
        $product->barcode = $request->barcode;
        $product->category_id = $request->category;
        $product->sub_category_id = $request->sub_category;
        $product->standard_uom = $request->standard_uom;
        $product->sales_uom = $request->sale_uom;
        $product->purchase_uom = $request->purchase_uom;
        $product->indication = $request->indication;
        $product->dosage = $request->dosage;
        $product->status = $request->status;
        $product->min_quantinty = str_replace(',', '', $request->min_stock);
        $product->max_quantinty = str_replace(',', '', $request->max_stock);
        try {
            $product->save();
            session()->flash("alert-success", "Product updated successfully!");
            return back();
        } catch (Exception $exception) {
            session()->flash("alert-danger", "Product exists!");
            return back();
        }

    }


    public function destroy(Request $request)
    {
        try {
            Product::destroy($request->product_id);
            session()->flash("alert-danger", "Product deleted successfully!");
            return back();
        } catch (Exception $exception) {
            $product = Product::find($request->product_id);
            $product->status = 0;
            $product->save();
            session()->flash("alert-danger", "Product deleted successfully!");
            return back();
        }
    }

    public function allProducts(Request $request)
    {

        $columns = array(
            0 => 'inv_products.name',
            1 => 'category_id',
            2 => 'inv_products.created_at',
            3 => 'created_at',
        );

        $totalData = Product::where('status', '1')->count();

        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $products = Product::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $products = Product::select('inv_products.name', 'category_id', 'status', 'inv_products.id', 'barcode'
                , 'indication', 'dosage', 'generic_name', 'purchase_uom', 'sales_uom', 'standard_uom', 'min_quantinty',
                'max_quantinty', 'sub_category_id', 'created_at')
                ->join('inv_categories', 'inv_categories.id', '=', 'inv_products.category_id')
                ->orWhere('inv_products.name', 'LIKE', "%{$search}%")
                ->orWhere('inv_categories.name', 'LIKE', "%{$search}%")
                ->orWhere('barcode', 'LIKE', "%{$search}%")
                ->orWhere('created_at', 'LIKE', "%{$search}%")
                ->Where('status', '1')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = Product::select('inv_products.name', 'category_id', 'status', 'inv_products.id', 'barcode'
                , 'indication', 'dosage', 'generic_name', 'purchase_uom', 'sales_uom', 'standard_uom', 'min_quantinty',
                'max_quantinty', 'sub_category_id', 'created_at')
                ->join('inv_categories', 'inv_categories.id', '=', 'inv_products.category_id')
                ->orWhere('inv_products.name', 'LIKE', "%{$search}%")
                ->orWhere('inv_categories.name', 'LIKE', "%{$search}%")
                ->orWhere('barcode', 'LIKE', "%{$search}%")
                ->orWhere('created_at', 'LIKE', "%{$search}%")
                ->Where('status', '1')
                ->count();
        }

        $data = array();
        if (!empty($products)) {
            foreach ($products as $product) {
//                $show =  route('posts.show',$post->id);
//                $edit =  route('posts.edit',$post->id);

                if ($product->status != 0) {
                    $nestedData['name'] = $product->name;
                    $nestedData['category'] = $product->category['name'];
                    $nestedData['status'] = $product->status;
                    $nestedData['id'] = $product->id;
                    $nestedData['barcode'] = $product->barcode;
                    $nestedData['indication'] = $product->indication;
                    $nestedData['dosage'] = $product->dosage;
                    $nestedData['generic'] = $product->generic_name;
                    $nestedData['purchase'] = $product->purchase_uom;
                    $nestedData['sale'] = $product->sales_uom;
                    $nestedData['standard'] = $product->standard_uom;
                    $nestedData['min'] = $product->min_quantinty;
                    $nestedData['max'] = $product->max_quantinty;
                    $nestedData['sub_category'] = $product->subCategory['name'];
                    $nestedData['category_id'] = $product->category_id;
                    $nestedData['sub_category_id'] = $product->sub_category_id;
                    $nestedData['date'] = date('Y-m-d', strtotime($product->created_at));

                    $data[] = $nestedData;
                }

//                $nestedData['options'] = "&emsp;<a href='{$show}' title='SHOW' ><span class='glyphicon glyphicon-list'></span></a>
//                                          &emsp;<a href='{$edit}' title='EDIT' ><span class='glyphicon glyphicon-edit'></span></a>";

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

    public function productCategoryFilter(Request $request)
    {
        if ($request->ajax()) {
            $sub_categories = SubCategory::where('category_id', $request->category_id)->get();
            return json_decode($sub_categories, true);
        }
    }

    public function storeProduct(Request $request)
    {
        if ($request->ajax()) {
            $product = new Product;
            $product->name = $request->name;
            $product->barcode = $request->barcode;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->generic_name = $request->generic_name;
            $product->standard_uom = $request->standardUoM;
            $product->sales_uom = $request->saleUoM;
            $product->purchase_uom = $request->purchaseUoM;
            $product->indication = $request->indication;
            $product->dosage = $request->dosage;
            $product->status = 1;
            $product->min_quantinty = str_replace(',', '', $request->min_stock);
            $product->max_quantinty = str_replace(',', '', $request->max_stock);

            try {
                $product->save();
                $message = array();
                array_push($message, array(
                    'message' => 'success'
                ));
                return $message;
            } catch (Exception $exception) {
                $message = array();
                array_push($message, array(
                    'message' => 'failed'
                ));
                return $message;
            }

        }
    }

    public function statusFilter(Request $request)
    {
        if ($request->ajax()) {
            $formatted_product = array();
            $products = Product::where('status', $request->status)->get();
            foreach ($products as $product) {
                array_push($formatted_product, array(
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category['name'],
                    'barcode' => $product->barcode,
                    'indication' => $product->indication,
                    'dosage' => $product->dosage,
                    'generic' => $product->generic_name,
                    'purchase' => $product->purchase_uom,
                    'sale' => $product->sales_uom,
                    'standard' => $product->standard_uom,
                    'min' => $product->min_quantinty,
                    'max' => $product->max_quantinty,
                    'sub_category' => $product->subCategory['name'],
                    'category_id' => $product->category_id,
                    'sub_category_id' => $product->sub_category_id,
                    'date' => date('Y-m-d', strtotime($product->created_at))
                ));
            }
            return $formatted_product;
        }
    }

    public function statusActivate(Request $request)
    {
        if ($request->ajax()) {
            $product = Product::find($request->product_id);
            $product->status = 1;

            if ($product->save()) {
                $message = array();
                array_push($message, array(
                    'message' => 'success'
                ));
                return $message;
            }

        }
    }

}
