<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\ProductStoreRequest;
use App\Product;
use App\SubCategory;
use App\PriceCategory;
use App\CurrentStock;
use App\PriceList;
use App\StockAdjustment;
use App\StockTracking;
use App\StockAdjustmentLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade as PDF;
use App\Exports\ProductsExport;
use App\Exports\PriceTemplateExport;
use App\Exports\StockTemplateExport;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

    public function index()
    {
        if (!Auth()->user()->checkPermission('View Product List')) {
            abort(403, 'Access Denied');
        }
        $products = Product::all();
        $category = Category::orderBy('name', 'asc')->get();
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
        $min_quantinty = str_replace(',', '', $request->input('min_quantinty'));
        $max_quantinty = str_replace(',', '', $request->input('max_quantinty'));
        
        $this->validate($request, [
            'name' => 'required|string|max:255|unique:inv_products,name,NULL,id,category_id,' . $request->category,
            'barcode' => 'nullable|string|max:50|unique:inv_products,barcode',
            'brand' => 'nullable|string|max:100',
            'pack_size' => 'nullable|string|max:50',
            'category' => 'required|exists:inv_categories,id',
            'sale_uom' => 'nullable|string|max:50',
            'min_quantinty' => 'nullable|min:1',
            'max_quantinty' => 'nullable|min:1',
            'product_type' => 'nullable|in:stockable,consumable',
            'status' => 'nullable|in:0,1'
        ]);
        $product_exists = Product::where('name', $request->name)
                            ->where('category_id', $request->category)
                            ->first();
        if($product_exists){
            session()->flash("alert-success", "Product already registered!");
            return back();
        }

        try {
            $product = new Product();
            $product->name = $request->name;
            $product->barcode = $request->barcode;
            $product->brand = $request->brand;
            $product->pack_size = $request->pack_size;
            $product->category_id = $request->category;
            $product->sales_uom = $request->sale_uom;
            $product->min_quantinty = $min_quantinty;
            $product->max_quantinty = $max_quantinty;
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('inv_products', 'name')
                    ->ignore($request->id) 
                    ->where(function ($query) use ($request) {
                        return $query->where('category_id', $request->category);
                    }),
            ],         
            'barcode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('inv_products', 'barcode')->ignore($request->id),
            ],
            'brand' => 'nullable|string|max:100',
            'category' => 'required|exists:inv_categories,id',
            'sale_uom' => 'nullable|string|max:50',
            // 'pack_size' => 'nullable|string|max:50',
            // 'min_quantinty' => 'nullable|numeric|min:0',
            // 'max_quantinty' => 'nullable|numeric|min:0',
            // 'product_type' => 'nullable|in:stockable,consumable',
            'status' => 'nullable|in:0,1'
        ], [
        'name.unique' => 'Product name exist',
        'barcode.unique' => 'Product barcode exist',
    ]);
        $min_quantinty = str_replace(',', '', $request->input('min_quantinty')) ?? null;
        $max_quantinty = str_replace(',', '', $request->input('max_quantinty')) ?? null;
        $pack_size = str_replace(',', '', $request->input('pack_size')) ?? null;

        try {
            $product = Product::findOrFail($request->id);
            $product->name = $request->name;
            $product->barcode = $request->barcode;
            $product->brand = $request->brand;
            $product->pack_size = $pack_size;
            $product->category_id = $request->category;
            $product->sales_uom = $request->sale_uom;
            $product->min_quantinty = $min_quantinty;
            $product->max_quantinty = $max_quantinty;
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
        $stock_count = DB::table('inv_current_stock')->where('product_id',$request->product_id)->count();
        
        $stock_count2 = DB::table('inv_incoming_stock')->where('product_id', $request->product_id)->count();

        if($stock_count > 0 || $stock_count2 > 0 )
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
                if ($request->status === 'unused') {
                    // Products that have no stock records and no sales transactions
                    $query->whereDoesntHave('currentStock', function($q) {
                        $q->where('quantity', '>', 0);
                    })->whereDoesntHave('incomingStock');
                } else {
                    $query->where('status', $request->status);
                }
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
            $sub_categories = SubCategory::where('category_id', $request->category_id)
            ->orderBy('name', 'asc')            
            ->get();
            return json_decode($sub_categories, true);
        }
    }
    public function storeProduct(Request $request)
    {
        if ($request->ajax()) {

        $this->validate($request, [
            'name' => 'required|string|max:255|unique:inv_products,name,NULL,id,category_id,' . $request->category,
            'barcode' => 'nullable|string|max:50|unique:inv_products,barcode',
            'brand' => 'nullable|string|max:100',
            'pack_size' => 'nullable|string|max:50',
            'category' => 'required|exists:inv_categories,id',
            'sale_uom' => 'nullable|string|max:50',
            // 'min_quantinty' => 'nullable|numeric|min:0',
            // 'max_quantinty' => 'nullable|numeric|min:0',
            'product_type' => 'nullable|in:stockable,consumable',
            'status' => 'nullable|in:0,1'
        ]);
        
            $product = new Product;
            $product->name = $request->name;
            $product->barcode = $request->barcode;
            $product->npk_ratio = $request->npk_ratio;
            $product->brand = $request->brand;
            $product->pack_size = str_replace(',', '', $request->pack_size);
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
            Log::info('Export format: ' . $request->input('format'));

            // Validate store and price category selection
            if (!$request->filled('store')) {
                return back()->with('error', 'Please select a branch for export');
            }

            if (!$request->filled('price_category')) {
                return back()->with('error', 'Please select a price category for export');
            }

            // Only select the columns we need
            $query = Product::select('id', 'name', 'barcode')
                ->when($request->filled('category'), function($q) use ($request) {
                    return $q->where('category_id', $request->category);
                })
                ->when($request->filled('status'), function($q) use ($request) {
                    return $q->where('status', $request->status);
                })
                ->whereHas('currentStock', function($q) use ($request) {
                    $q->where('store_id', $request->store);
                });

            $totalCount = $query->count();
            Log::info('Total products count: ' . $totalCount);

            if ($totalCount === 0) {
                return back()->with('error', 'No Stock found to export');
            }

            switch ($request->input('format')) {
                case 'excel':
                    Log::info('Generating Excel with price category: ' . $request->input('price_category') . ' and Branch: ' . $request->input('store'));
                    return Excel::download(new ProductsExport($query->get(), $request->input('price_category'), $request->input('store')), 'stock_export_'.date('Y-m-d').'.xlsx');

                case 'csv':
                    Log::info('Generating CSV with price category: ' . $request->input('price_category') . ' and Branch: ' . $request->input('store'));
                    return Excel::download(new ProductsExport($query->get(), $request->input('price_category'), $request->input('store')), 'stock_export_'.date('Y-m-d').'.csv');

                default:
                    return back()->with('error', 'Invalid export format');
            }
        } catch (Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while exporting products: ' . $e->getMessage());
        }
    }
    public function exportForm()
    {
        if (!Auth()->user()->checkPermission('View Product List')) {
            abort(403, 'Access Denied');
        }

        $priceCategories = PriceCategory::all();
        $categories = Category::orderBy('name', 'asc')->get();

        return view('tools.export_products', compact('priceCategories', 'categories'));
    }
    public function uploadPriceForm()
    {
        if (!Auth()->user()->checkPermission('View Product List')) {
            abort(403, 'Access Denied');
        }

        $priceCategories = PriceCategory::all();

        return view('tools.upload_price', compact('priceCategories'));
    }

    public function downloadPriceTemplate()
    {
        if (!Auth()->user()->checkPermission('View Product List')) {
            abort(403, 'Access Denied');
        }

        return Excel::download(new PriceTemplateExport(), 'price_upload_template_' . date('Y-m-d') . '.xlsx');
    }

    public function downloadStockTemplate()
    {
        if (!Auth()->user()->checkPermission('View Product List')) {
            abort(403, 'Access Denied');
        }

        return Excel::download(new StockTemplateExport(), 'stock_upload_template_' . date('Y-m-d') . '.xlsx');
    }

    public function uploadStockForm()
    {
        if (!Auth()->user()->checkPermission('View Product List')) {
            abort(403, 'Access Denied');
        }

        $adjustmentReasons = \App\AdjustmentReason::all();
        $stores = \App\Store::all();

        return view('tools.upload_stock', compact('adjustmentReasons', 'stores'));
    }

    public function resetStockForm()
    {
        if (!Auth()->user()->checkPermission('View Product List')) {
            abort(403, 'Access Denied');
        }

        $adjustmentReasons = \App\AdjustmentReason::all();
        $stores = \App\Store::all();

        return view('tools.reset_stock', compact('adjustmentReasons', 'stores'));
    }

    public function uploadPrice(Request $request)
    {
        $request->validate([
            'price_category' => 'required|exists:price_categories,id',
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
        ]);

        // Prevent submission when in ALL branch
        if (is_all_store()) {
            return back()->with('error', 'Price upload is not allowed when in ALL branches. Please select a specific branch.');
        }

        $storeId = current_store_id();
        Log::info('Price upload initiated for store ID: ' . $storeId);

        // Check if the branch has any stock
        $totalStockCount = CurrentStock::where('store_id', $storeId)->where('quantity', '>', 0)->count();
        Log::info('Total stock count for store ID ' . $storeId . ': ' . $totalStockCount);

        if ($totalStockCount === 0) {
            Log::warning('Price upload terminated: No stock found in branch (store ID: ' . $storeId . ')');
            return back()->with('error', 'The selected branch has no stock. Price upload cannot proceed.');
        }

        try {
            $priceCategoryId = $request->price_category;
            $file = $request->file('file');

            // Parse the file
            $data = $this->parseFile($file, true);

            if (empty($data)) {
                return back()->with('error', 'The uploaded file is empty or contains no valid data.');
            }

            // Validate headers
            $headers = array_keys($data[0]);
            $expectedHeaders = ['code', 'product name', 'price'];
            Log::info('Validating file headers: ' . implode(', ', $headers));

            if (count($headers) !== 3 || !empty(array_diff($expectedHeaders, array_map('strtolower', $headers)))) {
                return back()->with('error', 'Invalid file format. Expected columns: code, product name,  price');
            }

            // Validate data structure and content
            $validationErrors = [];
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because index starts at 0 and we skip header

                // Check if all required columns have values
                if (empty(trim($row['code'] ?? '')) && empty(trim($row['product name'] ?? ''))) {
                    $validationErrors[] = "Row {$rowNumber}: Both code and product name cannot be empty";
                    continue;
                }

                // Validate price format
                $price = trim($row['price'] ?? '');
                if (empty($price)) {
                    $validationErrors[] = "Row {$rowNumber}: Price cannot be empty";
                    continue;
                }

                // Check if price is numeric
                if (!is_numeric(str_replace([',', ' '], '', $price))) {
                    $validationErrors[] = "Row {$rowNumber}: Invalid Price format '{$price}'";
                    continue;
                }

                // Check if price is positive
                $numericPrice = floatval(str_replace([',', ' '], '', $price));
                if ($numericPrice < 0) {
                    $validationErrors[] = "Row {$rowNumber}: Price cannot be negative '{$price}'";
                }
            }

            // If there are validation errors, return them
            if (!empty($validationErrors)) {
                $errorMessage = "File validation failed:\n\n";
                $errorMessage .= implode("\n", array_slice($validationErrors, 0, 1)); 
                if (count($validationErrors) > 1) {
                    $errorMessage .= "\n... and " . (count($validationErrors) - 1) . " more errors";
                }
                return back()->with('error', $errorMessage);
            }

            // Process the data
            $results = $this->processPriceUpload($data, $priceCategoryId);

            if($results){
                $successMessage = "Price upload completed successfully.";
                return back()->with('success', $successMessage);
            }

        } catch (\Exception $e) {
            Log::error('Price upload error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while processing the file. Please try again.');
        }
    }

    private function parseFile($file, $isPriceFile = false)
    {
        $extension = $file->getClientOriginalExtension();

        if ($extension === 'csv') {
            return $isPriceFile ? $this->parsePriceCsv($file) : $this->parseCsv($file);
        } else {
            return $isPriceFile ? $this->parsePriceExcel($file) : $this->parseExcel($file);
        }
    }

    private function parseCsv($file)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');

        // Skip header row
        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 3) {
                $data[] = [
                    'code' => trim($row[0]),
                    'product name' => trim($row[1]),
                    'quantity' => trim($row[2]),
                ];
            }
        }

        fclose($handle);
        return $data;
    }

    private function parseExcel($file)
    {
        $data = [];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Skip header row
        array_shift($rows);

        foreach ($rows as $row) {
            if (count($row) >= 3 && !empty($row[0])) {
                $data[] = [
                    'code' => trim($row[0]),
                    'product name' => trim($row[1]),
                    'quantity' => trim($row[2]),
                ];
            }
        }

        return $data;
    }
    
    private function parsePriceCsv($file)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');

        // Skip header row
        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 3) {
                $data[] = [
                    'code' => trim($row[0]),
                    'product name' => trim($row[1]),
                    'price' => trim($row[2]),
                ];
            }
        }

        fclose($handle);
        return $data;
    }

    private function parsePriceExcel($file)
    {
        $data = [];
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Skip header row
        array_shift($rows);

        foreach ($rows as $row) {
            if (count($row) >= 3 && !empty($row[0])) {
                $data[] = [
                    'code' => trim($row[0]),
                    'product name' => trim($row[1]),
                    'price' => trim($row[2]),
                ];
            }
        }

        return $data;
    }

    private function processPriceUpload($data, $priceCategoryId)
    {
        $results = [
            'processed' => 0,
            'updated' => 0,
            'created' => 0,
            'errors' => 0,
            'error_messages' => [],
        ];

        $storeId = current_store_id();
        $userId = Auth::id();

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                $results['processed']++;

                // Validate price
                $sellingPrice = $this->validateAndParsePrice($row['price']);
                if ($sellingPrice === false) {
                    $errorMsg = "Invalid price for product: {$row['code']} - {$row['product name']}";
                    Log::warning($errorMsg);
                    $results['error_messages'][] = $errorMsg;
                    $results['errors']++;
                    continue;
                }

                // Find product by name
                $product = $this->findProduct($row['product name']);
                if (!$product) {
                    $errorMsg = "Product not found: {$row['product name']}";
                    Log::warning($errorMsg);
                    $results['error_messages'][] = $errorMsg;
                    $results['errors']++;
                    continue;
                }

                // Get current stock for this product in the store
                $currentStocks = CurrentStock::where('product_id', $product->id)
                    ->where('quantity', '>', 0);

                if (!is_all_store()) {
                    $currentStocks->where('store_id', $storeId);
                }

                $currentStocks = $currentStocks->get();

                if ($currentStocks->isEmpty()) {
                    $errorMsg = "No stock found for product: {$product->name}";
                    Log::warning($errorMsg);
                    $results['error_messages'][] = $errorMsg;
                    $results['errors']++;
                    continue;
                }

                // Update prices for all stock batches of this product
                foreach ($currentStocks as $stock) {
                    $existingPrice = PriceList::where('stock_id', $stock->id)
                        ->where('price_category_id', $priceCategoryId)
                        ->first();

                    if ($existingPrice) {
                        $existingPrice->update([
                            'price' => $sellingPrice,
                            'updated_by' => $userId,
                        ]);
                        $results['updated']++;
                    } else {
                        PriceList::create([
                            'stock_id' => $stock->id,
                            'price_category_id' => $priceCategoryId,
                            'price' => $sellingPrice,
                            'created_by' => $userId,
                            'updated_by' => $userId,
                        ]);
                        $results['created']++;
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }
    private function validateAndParsePrice($price)
    {
        // Remove commas and spaces
        $price = str_replace([',', ' '], '', $price);

        // Check if it's a valid number
        if (!is_numeric($price)) {
            return false;
        }

        $price = floatval($price);

        // Check if positive
        if ($price < 0) {
            return false;
        }

        return $price;
    }

    private function findProduct($name)
    {
        // Log::info("Looking for product with code: '{$code}' and name: '{$name}'");

        // Finally try to find by name
        if (!empty($name)) {
            $product = Product::where('name', 'LIKE', "%{$name}%")->first();
            if ($product) {
                // Log::info("Found product by name: {$product->name} (ID: {$product->id})");
                return $product;
            }
        }

        // Log::warning("Product not found for code: '{$code}' and name: '{$name}'");
        return null;
    }

    public function uploadStock(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:inv_stores,id',
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
        ]);

        $storeId = $request->store_id;
        // Log::info('Stock upload initiated for store ID: ' . $storeId);

        // Check if the branch has any products
        $totalProductCount = Product::count();
        // Log::info('Total product count: ' . $totalProductCount);

        if ($totalProductCount === 0) {
            // Log::warning('Stock upload terminated: No products found');
            return back()->with('error', 'No products found in the system. Stock upload cannot proceed.');
        }

        try {
            $adjustmentReasonId = $request->adjustment_reason;
            $file = $request->file('file');
            Log::info('Uploaded file: ' . $file->getClientOriginalName());

            // Parse the file
            $data = $this->parseFile($file);

            if (empty($data)) {
                return back()->with('error', 'The uploaded file is empty or contains no valid data.');
            }

            // Validate headers
            $headers = array_keys($data[0]);
            $expectedHeaders = ['code', 'product name', 'quantity'];
            Log::info('Stock upload headers found: ' . implode(', ', $headers));
            Log::info('Stock upload expected headers: ' . implode(', ', $expectedHeaders));

            if (count($headers) !== 3 || !empty(array_diff($expectedHeaders, array_map('strtolower', $headers)))) {
                return back()->with('error', 'Invalid file format. Expected columns: code, product name, quantity. Found: ' . implode(', ', $headers));
            }

            // Validate data structure and content
            $validationErrors = [];
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because index starts at 0 and we skip header

                // Debug: Log the row data
                // Log::info("Processing row {$rowNumber}: " . json_encode($row));

                // Check if all required columns have values
                if (empty(trim($row['code'] ?? '')) && empty(trim($row['product name'] ?? ''))) {
                    $validationErrors[] = "Row {$rowNumber}: Code and Product Name cannot be empty";
                    continue;
                }

                // Validate quantity format
                $quantity = trim($row['quantity'] ?? '');
                if (empty($quantity)) {
                    $validationErrors[] = "Row {$rowNumber}: Quantity cannot be empty";
                    continue;
                }

                // Check if quantity is numeric
                if (!is_numeric(str_replace([',', ' '], '', $quantity))) {
                    $validationErrors[] = "Row {$rowNumber}: Invalid quantity format '{$quantity}'";
                    continue;
                }

                // Check if quantity is non-negative
                $numericQuantity = floatval(str_replace([',', ' '], '', $quantity));
                if ($numericQuantity < 0) {
                    $validationErrors[] = "Row {$rowNumber}: Quantity cannot be negative ('{$quantity}')";
                }
            }

            // If there are validation errors, return them
            if (!empty($validationErrors)) {
                $errorMessage = "File validation failed:\n\n";
                $errorMessage .= implode("\n", array_slice($validationErrors, 0, 5)); 
                if (count($validationErrors) > 5) {
                    $errorMessage .= "\n... and " . (count($validationErrors) - 5) . " more errors";
                }
                return back()->with('error', $errorMessage);
            }

            // Process the data
            $results = $this->processStockUpload($data, $adjustmentReasonId, $storeId);

            $successMessage = "Stock upload completed successfully.";

            if ($results['errors'] > 0) {
                $message = "\n\nErrors: {$results['errors']}";
                $message .= "\n" . $results['error_messages'][0];
                if (count($results['error_messages']) > 1) {
                    $message .= "\n... and " . (count($results['error_messages']) - 1) . " more errors";
                }
                Log::info('Stock upload completed with errors: ' . implode('; ', $results['error_messages']));
                // return back()->with('warning', $message);
            }

            return back()->with('success', $successMessage);

        } catch (Exception $e) {
            Log::error('Stock upload error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while processing the file. Please try again.');
        }
    }
    private function processStockUpload($data, $adjustmentReasonId, $storeId)
    {
        $results = [
            'processed' => 0,
            'updated' => 0,
            'created' => 0,
            'errors' => 0,
            'error_messages' => [],
        ];

        $userId = Auth::id();
        $adjustment_reason = \App\AdjustmentReason::find($adjustmentReasonId)->reason ?? '';

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                $results['processed']++;

                // Debug: Log the row being processed
                // Log::info("Processing stock upload row: " . json_encode($row));

                // Validate quantity
                $quantity = $this->validateAndParseQuantity($row['quantity']);
                if ($quantity === false) {
                    $errorMsg = "Invalid quantity for product: {$row['product name']}";
                    Log::warning($errorMsg);
                    $results['error_messages'][] = $errorMsg;
                    $results['errors']++;
                    continue;
                }

                // Find product by code or name
                $product = $this->findProduct($row['product name']);
                if (!$product) {
                    $errorMsg = "Product {$row['product name']} not found";
                    Log::warning($errorMsg);
                    $results['error_messages'][] = $errorMsg;
                    $results['errors']++;
                    continue;
                }

                // Log::info("Found product: {$product->name} (ID: {$product->id}) for quantity: {$quantity}");

                // Get current stock for this product in the store
                $currentStocks = CurrentStock::where('product_id', $product->id)
                    ->where('store_id', $storeId)
                    ->get();

                // Calculate total current quantity for this product in the store
                $totalCurrentQuantity = $currentStocks->sum('quantity');

                // If new quantity is different from current total, we need to adjust
                if ($quantity != $totalCurrentQuantity) {
                    $adjustmentQuantity = $quantity - $totalCurrentQuantity;

                    // Create stock adjustment record
                    $reference = 'STOCK-UPLOAD-' . time() . '-' . $results['processed'];

                    // Use the same logic as stock adjustment for summary adjustments
                    if ($adjustmentQuantity > 0) {
                        // INCREASE: Add to the latest batch or create new batch
                        $latestBatch = $currentStocks->sortByDesc('created_at')->first();

                        if ($latestBatch) {
                            $prevQty = (float) $latestBatch->quantity;
                            $latestBatch->quantity = $prevQty + $adjustmentQuantity;
                            $latestBatch->save();

                            // Log the adjustment
                            StockAdjustment::create([
                                'stock_id' => $latestBatch->id,
                                'quantity' => $adjustmentQuantity,
                                'type' => 'increase',
                                'reason' => 'Stock Upload Adjustment: ' . $adjustment_reason,
                                'description' => "Stock upload for product: {$product->name}",
                                'created_by' => $userId,
                            ]);

                            StockTracking::create([
                                'stock_id' => $latestBatch->id,
                                'product_id' => $latestBatch->product_id,
                                'out_mode' => 'Stock Upload: ' . $adjustment_reason,
                                'quantity' => $adjustmentQuantity,
                                'store_id' => $storeId,
                                'created_by' => $userId,
                                'updated_at' => now()->format('Y-m-d'),
                                'movement' => 'IN',
                            ]);

                            StockAdjustmentLog::create([
                                'current_stock_id' => $latestBatch->id,
                                'user_id' => $userId,
                                'store_id' => $storeId,
                                'previous_quantity' => $prevQty,
                                'new_quantity' => $latestBatch->quantity,
                                'adjustment_quantity' => $adjustmentQuantity,
                                'adjustment_type' => 'increase',
                                'reason' => 'Stock Upload Adjustment: ' . $adjustment_reason,
                                'reference_number' => $reference,
                            ]);

                            $results['updated']++;
                        } else {
                            // Create new stock batch
                            $newStock = CurrentStock::create([
                                'product_id' => $product->id,
                                'store_id' => $storeId,
                                'quantity' => $quantity,
                                'batch_number' => 'UPLOAD-' . time(),
                                'expiry_date' => null,
                            ]);

                            // Log the creation
                            StockAdjustment::create([
                                'stock_id' => $newStock->id,
                                'quantity' => $quantity,
                                'type' => 'increase',
                                'reason' => 'Stock Upload Adjustment: '.$adjustment_reason,
                                'description' => "Stock upload for product: {$product->name}",
                                'created_by' => $userId,
                            ]);

                            StockTracking::create([
                                'stock_id' => $newStock->id,
                                'product_id' => $newStock->product_id,
                                'out_mode' => 'Stock Upload: ' . $adjustment_reason,
                                'quantity' => $quantity,
                                'store_id' => $storeId,
                                'created_by' => $userId,
                                'movement' => 'IN',
                            ]);

                            StockAdjustmentLog::create([
                                'current_stock_id' => $newStock->id,
                                'user_id' => $userId,
                                'store_id' => $storeId,
                                'previous_quantity' => 0,
                                'new_quantity' => $quantity,
                                'adjustment_quantity' => $quantity,
                                'adjustment_type' => 'increase',
                                'reason' => 'Stock Upload Adjustment: '. $adjustment_reason,
                                'reference_number' => $reference,
                            ]);

                            $results['created']++;
                        }
                    } else {
                        // DECREASE: Remove from oldest batches
                        $toRemove = abs($adjustmentQuantity);
                        $removedTotal = 0;

                        $batchesToAdjust = $currentStocks->where('quantity', '>', 0)
                            ->sortBy('created_at'); // Oldest first

                        foreach ($batchesToAdjust as $batch) {
                            if ($toRemove <= 0) break;

                            $available = (float) $batch->quantity;
                            $deduct = min($available, $toRemove);
                            $prevQty = $batch->quantity;
                            $batch->quantity = $prevQty - $deduct;
                            $batch->save();

                            StockAdjustment::create([
                                'stock_id' => $batch->id,
                                'quantity' => $deduct,
                                'type' => 'decrease',
                                'reason' => 'Stock Upload Adjustment: '. $adjustment_reason,
                                'description' => "Stock upload for product: {$product->name}",
                                'created_by' => $userId,
                            ]);

                            StockTracking::create([
                                'stock_id' => $batch->id,
                                'product_id' => $batch->product_id,
                                'out_mode' => 'Stock Upload: ' . $adjustment_reason,
                                'quantity' => $deduct,
                                'store_id' => $storeId,
                                'created_by' => $userId,
                                'movement' => 'OUT',
                            ]);

                            StockAdjustmentLog::create([
                                'current_stock_id' => $batch->id,
                                'user_id' => $userId,
                                'store_id' => $storeId,
                                'previous_quantity' => $prevQty,
                                'new_quantity' => $batch->quantity,
                                'adjustment_quantity' => $deduct,
                                'adjustment_type' => 'decrease',
                                'reason' => 'Stock Upload Adjustment: ' . $adjustment_reason,
                                'reference_number' => $reference,
                            ]);

                            $removedTotal += $deduct;
                            $toRemove -= $deduct;
                        }

                        $results['updated']++;
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }
    private function validateAndParseQuantity($quantity)
    {
        // Remove commas and spaces
        $quantity = str_replace([',', ' '], '', $quantity);

        // Check if it's a valid number
        if (!is_numeric($quantity)) {
            return false;
        }

        $quantity = floatval($quantity);

        // Check if non-negative
        if ($quantity < 0) {
            return false;
        }

        return $quantity;
    }
    public function resetStock(Request $request)
    {
        $request->validate([
            'adjustment_reason' => 'required|exists:adjustment_reasons,id',
            'store_id' => 'required|exists:inv_stores,id',
            'password' => 'required|string|min:1|max:255',
        ]);

        $storeId = $request->store_id;
        $providedPassword = trim($request->input('password'));
        $correctPassword = config('app.db_clear_password');

        // Validate password configuration
        if (empty($correctPassword)) {
            Log::error('Database clear password not configured in config/app.php');
            return back()->with('error', 'Stock reset is not properly configured. Please contact system administrator.');
        }

        if ($providedPassword !== $correctPassword) {
            Log::warning('Failed stock reset attempt with incorrect password by user: ' . (auth()->user()->name ?? 'Unknown') . ' (ID: ' . (auth()->id() ?? 'Unknown') . ')');
            return back()->with('error', 'Incorrect password. Stock reset aborted.');
        }

        Log::info('Stock reset initiated for store ID: ' . $storeId);

        // Check if there are any stock records with quantity > 0 in the selected store
        $totalStockRecords = CurrentStock::where('store_id', $storeId)->where('quantity', '>', 0)->count();
        Log::info('Total stock records with quantity > 0 in store ID ' . $storeId . ': ' . $totalStockRecords);

        if ($totalStockRecords === 0) {
            $storeName = \App\Store::find($storeId)->name ?? 'Unknown Branch';
            Log::warning('Stock reset terminated: No stock records with quantity > 0 found in branch ' . $storeName . ')');
            return back()->with('error', "Branch '{$storeName}' has no stock to reset.");
        }

        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $reference = 'STOCK-RESET-' . time();
            $resetCount = 0;
            $totalQuantityReset = 0;

            // Get all stock batches with quantity > 0 in the current store
            $stockBatches = CurrentStock::where('store_id', $storeId)
                ->where('quantity', '>', 0)
                ->get();
                
            $storeName = \App\Store::find($storeId)->name ?? 'Unknown';
            $adjustment_reason = \App\AdjustmentReason::find($request->adjustment_reason)->reason ?? '';

            foreach ($stockBatches as $batch) {
                $prevQuantity = (float) $batch->quantity;
                $totalQuantityReset += $prevQuantity;

                // Create stock adjustment record
                StockAdjustment::create([
                    'stock_id' => $batch->id,
                    'quantity' => $prevQuantity,
                    'type' => 'decrease',
                    'reason' => 'Stock Reset: ' . $adjustment_reason,
                    'description' => "Complete stock reset for branch '{$storeName}' - Product: {$batch->product->name}",
                    'created_by' => $userId,
                ]);

                // Create stock tracking record
                StockTracking::create([
                    'stock_id' => $batch->id,
                    'product_id' => $batch->product_id,
                    'out_mode' => 'Stock Reset: ' . $adjustment_reason,
                    'quantity' => $prevQuantity,
                    'store_id' => $storeId,
                    'created_by' => $userId,
                    'updated_at' => now()->format('Y-m-d'),
                    'movement' => 'OUT',
                ]);

                // Create stock adjustment log
                StockAdjustmentLog::create([
                    'current_stock_id' => $batch->id,
                    'user_id' => $userId,
                    'store_id' => $storeId,
                    'previous_quantity' => $prevQuantity,
                    'new_quantity' => 0,
                    'adjustment_quantity' => $prevQuantity,
                    'adjustment_type' => 'decrease',
                    'reason' => 'Stock Reset: ' . $adjustment_reason,
                    'reference_number' => $reference,
                ]);

                // Set quantity to 0
                $batch->quantity = 0;
                $batch->save();

                $resetCount++;
            }

            DB::commit();

            $message = "Stock reset completed successfully all stock have been set to 0.\n\n";
            // $message .= "Stock batches reset: {$resetCount}\n";
            // $message .= "Total quantity reset: {$totalQuantityReset}\n";
            // $message .= "Reference: {$reference}\n\n";
            // $message .= "All stock quantities in the current branch have been set to 0.";

            Log::info("Stock reset completed for store ID {$storeId}: {$resetCount} batches, {$totalQuantityReset} total quantity");

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock reset error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while resetting stock. Please try again.');
        }
    }

}
