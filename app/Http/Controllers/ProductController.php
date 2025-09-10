<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\ProductStoreRequest;
use App\Product;
use App\SubCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade as PDF;
use App\Exports\ProductsExport;

class ProductController extends Controller
{

    public function index()
    {

        $products = Product::all();
        $category = Category::all();
        $sub_category = SubCategory::all();


        foreach ($products as $product)
        {
            $stock_id = DB::table('inv_current_stock')->where('product_id',$product->id);

            if($stock_id->count() > 0){
                $stock_count = DB::table('sales_details')->where('stock_id',$stock_id->first()->id)->count();
            }

            if($stock_id->count() == 0)
            {
                $stock_count = 0;
            }



            if($stock_count>0){
                $product['transaction_status']="active";
            }

            if($stock_count==0)
            {
                $product['transaction_status']="inactive";
            }
        }

        return view('stock_management.products.index')->with([
            'products' => $products,
            'categories' => $category,
            'sub_categories' => $sub_category
        ]);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:50',
            'brand' => 'required|string|max:100',
            'pack_size' => 'required|string|max:50',
            'category' => 'required|exists:inv_categories,id',
            'sale_uom' => 'required|string|max:50',
            'min_quantinty' => 'required|numeric|min:0',
            'max_quantinty' => 'required|numeric|min:0',
            'product_type' => 'required|in:stockable,consumable',
            'status' => 'required|in:0,1'
        ]);

        try {
            $product = new Product();
            $product->name = $request->name;
            $product->barcode = $request->barcode;
            $product->brand = $request->brand;
            $product->pack_size = $request->pack_size;
            $product->category_id = $request->category;
            $product->sales_uom = $request->sale_uom;
            $product->min_quantinty = str_replace(',', '', $request->min_quantinty);
            $product->max_quantinty = str_replace(',', '', $request->max_quantinty);
            $product->type = $request->product_type;
            $product->status = $request->status;
            $product->save();

            session()->flash("alert-success", "Product created successfully!");
            return back();
        } catch (\Exception $e) {
            session()->flash("alert-danger", "Failed to create product. Please try again.");
            return back()->withInput();
        }
    }


    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:50',
            'brand' => 'nullable|string|max:100',
            'pack_size' => 'required|string|max:50',
            'category' => 'required|exists:inv_categories,id',
            'sale_uom' => 'required|string|max:50',
            'min_quantinty' => 'nullable|numeric|min:0',
            'max_quantinty' => 'nullable|numeric|min:0',
            // 'product_type' => 'nullable|in:stockable,consumable',
            'status' => 'nullable|in:0,1'
        ]);

        try {
            $product = Product::findOrFail($request->id);
            $product->name = $request->name;
            $product->barcode = $request->barcode;
            $product->brand = $request->brand;
            $product->pack_size = $request->pack_size;
            $product->category_id = $request->category;
            $product->sales_uom = $request->sale_uom;
            $product->min_quantinty = str_replace(',', '', $request->min_quantinty);
            $product->max_quantinty = str_replace(',', '', $request->max_quantinty);
            $product->type = $request->product_type;
            $product->status = $request->status;
            $product->save();

            session()->flash("alert-success", "Product updated successfully!");
            return back();
        } catch (\Exception $e) {
            session()->flash("alert-danger", "Failed to update product. Please try again.");
            return back()->withInput();
        }
    }


    public function destroy(Request $request)
    {
        $stock_id = DB::table('inv_current_stock')->where('product_id',$request->product_id);

        if($stock_id->count() > 0){
            $stock_count = DB::table('sales_details')->where('stock_id',$stock_id->first()->id)->count();
        }

        if($stock_id->count() == 0)
        {
            $stock_count = 0;
        }


        if($stock_count>0)
        {
            $product = Product::find($request->product_id);
            $product->status = 0;
            $product->save();
            session()->flash("alert-success", "Product de-activated successfully!");
            return back();
        }

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
        try {
            $columns = array(
                0 => 'name',
                1 => 'brand',
                2 => 'pack_size',
                3 => 'category_id'
            );

            $query = Product::with('category');

            // Apply filters
            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $totalData = $query->count();
            $totalFiltered = $totalData;

            // Handle search
            if ($request->filled('search.value')) {
                $search = $request->input('search.value');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('brand', 'LIKE', "%{$search}%")
                      ->orWhere('pack_size', 'LIKE', "%{$search}%")
                      ->orWhereHas('category', function($q) use ($search) {
                          $q->where('name', 'LIKE', "%{$search}%");
                      });
                });
                $totalFiltered = $query->count();
            }

            // Handle ordering
            if ($request->filled('order.0.column')) {
                $orderColumn = $columns[$request->input('order.0.column')];
                $orderDir = $request->input('order.0.dir');
                if ($orderColumn === 'category_id') {
                    $query->join('inv_categories', 'inv_categories.id', '=', 'inv_products.category_id')
                          ->orderBy('inv_categories.name', $orderDir)
                          ->select('inv_products.*');
                } else {
                    $query->orderBy($orderColumn, $orderDir);
                }
            }

            // Handle pagination
            $limit = $request->input('length');
            $start = $request->input('start');
            $products = $query->skip($start)->take($limit)->get();

            $data = [];
            foreach ($products as $product) {
                $data[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'brand' => $product->brand,
                    'pack_size' => $product->pack_size,
                    'category' => [
                        'name' => $product->category ? $product->category->name : ''
                    ],
                    'barcode' => $product->barcode,
                    'sales_uom' => $product->sales_uom,
                    'min_quantinty' => $product->min_quantinty,
                    'max_quantinty' => $product->max_quantinty,
                    'type' => $product->type,
                    'status' => $product->status,
                    'category_id' => $product->category_id
                ];
                // Debugging: Log the product data before sending
                Log::info('Product Data for ' . $product->name, [
                    'min_quantinty' => $product->min_quantinty,
                    'max_quantinty' => $product->max_quantinty,
                    'sales_uom' => $product->sales_uom
                ]);
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Product listing error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while fetching products'
            ], 500);
        }
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
            $product->npk_ratio = $request->npk_ratio;
            $product->brand = $request->brand;
            $product->pack_size = $request->pack_size;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->generic_name = $request->generic_name;
            $product->standard_uom = $request->standardUoM;
            $product->sales_uom = $request->sale_uom;
            $product->purchase_uom = $request->purchaseUoM;
            $product->indication = $request->indication;
            $product->dosage = $request->dosage;
            $product->status = 1;
            $product->min_quantinty = str_replace(',', '', $request->min_quantinty);
            $product->max_quantinty = str_replace(',', '', $request->max_quantinty);

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

    public function export(Request $request)
    {
        // Increase memory limit
        ini_set('memory_limit', '512M');
        
        try {
            Log::info('Starting export process');
            Log::info('Export format: ' . $request->format);

            // Only select the columns we need
            $query = Product::select('name', 'brand', 'pack_size', 'category_id', 'type', 'status', 'min_quantinty', 'max_quantinty')
                ->with(['category' => function($q) {
                    $q->select('id', 'name');
                }])
                ->when($request->filled('category'), function($q) use ($request) {
                    return $q->where('category_id', $request->category);
                })
                ->when($request->filled('type'), function($q) use ($request) {
                    return $q->where('type', $request->type);
                })
                ->when($request->filled('status'), function($q) use ($request) {
                    return $q->where('status', $request->status);
                });

            $totalCount = $query->count();
            Log::info('Total products count: ' . $totalCount);

            if ($totalCount === 0) {
                return back()->with('error', 'No products found to export');
            }

            switch ($request->format) {
                case 'pdf':
                    Log::info('Generating PDF');
                    try {
                        // Get all products at once since we'll build a single HTML document
                        $products = $query->get();
                        
                        // Calculate number of pages
                        $productsPerPage = 100;
                        $totalPages = ceil($products->count() / $productsPerPage);
                        
                        // Build HTML for all pages
                        $html = '';
                        for ($page = 1; $page <= $totalPages; $page++) {
                            $pageProducts = $products->forPage($page, $productsPerPage);
                            
                            $html .= view('exports.products_pdf', [
                                'products' => $pageProducts,
                                'date' => date('Y-m-d H:i:s'),
                                'page' => $page,
                                'total_pages' => $totalPages
                            ])->render();
                            
                            // Add page break between pages, except for the last page
                            if ($page < $totalPages) {
                                $html .= '<div style="page-break-after: always;"></div>';
                            }
                        }

                        // Create PDF from the complete HTML
                        $pdf = PDF::loadHTML($html);
                        $pdf->setPaper('a4', 'landscape');
                        
                        // Disable SSL verification for local development
                        if (app()->environment('local')) {
                            config(['dompdf.options.ssl_verifier' => false]);
                        }

                        return $pdf->stream('products_'.date('Y-m-d').'.pdf');
                    } catch (\Exception $e) {
                        Log::error('Error generating PDF: ' . $e->getMessage());
                        Log::error('Stack trace: ' . $e->getTraceAsString());
                        throw $e;
                    }

                case 'excel':
                    Log::info('Generating Excel');
                    return Excel::download(new ProductsExport($query), 'products_'.date('Y-m-d').'.xlsx');

                case 'csv':
                    Log::info('Generating CSV');
                    return Excel::download(new ProductsExport($query), 'products_'.date('Y-m-d').'.csv');

                default:
                    return back()->with('error', 'Invalid export format');
            }
        } catch (Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while exporting products: ' . $e->getMessage());
        }
    }

}
