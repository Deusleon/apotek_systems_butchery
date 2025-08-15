<?php

namespace App\Http\Controllers;

use App\Category;
use App\CurrentStock;
use App\Exports\ImportTemplate;
use App\GoodsReceiving;
use App\ImportHistory;
use App\PriceCategory;
use App\PriceList;
use App\Product;
use App\StockTracking;
use App\Store;
use App\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

class ImportDataController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $price_categories = PriceCategory::all();
        $suppliers = Supplier::orderby('name', 'ASC')->get();
        $stores = Store::where('name', '<>', 'ALL')->get();
        $import_history = ImportHistory::with(['store', 'priceCategory', 'supplier', 'creator'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('import.index', compact('categories', 'price_categories', 'stores', 'suppliers', 'import_history'));
    }

    public function downloadTemplate()
    {
        $file = public_path() . "/fileStore/import_template/import_data_template.xlsx";
        $headers = array(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );
        return Response::download($file, 'import_data_template.xlsx', $headers);
    }

    public function previewImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                'file',
                'mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,text/plain',
                'mimes:xlsx,xls,csv'
            ],
            'price_category_id' => 'required|exists:price_categories,id',
            'store_id' => 'required|exists:inv_stores,id',
            'supplier_id' => 'required|exists:inv_suppliers,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $excel_raw_data = Excel::toArray(null, request()->file('file'));
            $preview_data = [];
            $errors = [];
            
            foreach ($excel_raw_data[0] as $index => $row) {
                if ($index === 0) continue; // Skip header row
                if (empty($row[0])) continue; // Skip empty rows

                $row_number = $index + 1;
                $row_errors = $this->validateRow($row, $row_number);
                
                $preview_data[] = [
                    'row_number' => $row_number,
                    'data' => $row,
                    'errors' => $row_errors
                ];
            }

            // Store preview data in session
            Session::put('import_preview', [
                'data' => $preview_data,
                'file_name' => $request->file('file')->getClientOriginalName(),
                'price_category_id' => $request->price_category_id,
                'store_id' => $request->store_id,
                'supplier_id' => $request->supplier_id
            ]);

            return view('import.preview', [
                'preview_data' => $preview_data,
                'store_id' => $request->store_id,
                'price_category_id' => $request->price_category_id,
                'supplier_id' => $request->supplier_id,
                'temp_file' => $request->file('file')->getClientOriginalName()
            ]);

        } catch (Exception $e) {
            Log::error('Preview generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate preview: ' . $e->getMessage());
        }
    }

    public function recordImport(Request $request)
    {
        $pending_to_save = array();
        if ($request->file('file')) {
            $excel_raw_data = Excel::toArray(null, request()->file('file'));

            $loop_count = 1;
            foreach ($excel_raw_data as $raw_data) {
                unset($raw_data[0]); // Skip header row
                foreach ($raw_data as $data) {
                    $loop_count++;
                    // Clean numeric data
                    $data[2] = preg_replace('/[^\d.]/', '', $data[2]); // unit price
                    $data[3] = preg_replace('/[^\d.]/', '', $data[3]); // sell price
                    $data[4] = preg_replace('/[^\d.]/', '', $data[4]); // quantity

                    try {
                        // Handle expiry date
                        if ($data[5] != null) {
                            $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data[5]);
                            $excel_date = $excelDate->format('Y-m-d');
                        } else {
                            $excel_date = null;
                        }

                        if (is_numeric($data[2]) && is_numeric($data[3]) && is_numeric($data[4])) {
                            // Check category name if exists
                            $category_id = Category::where('name', $data[7])->value('id');
                            if (!$category_id) {
                                $category_id = $request->category_id; // Fallback to selected category
                            }

                            array_push($pending_to_save, array(
                                'name' => $data[1],
                                'category_id' => $category_id,
                                'price_category_id' => $request->price_category_id,
                                'store_id' => $request->store_id,
                                'unit_price' => $data[2],
                                'sell_price' => $data[3],
                                'quantity' => $data[4],
                                'date' => $excel_date,
                                'barcode' => $data[6],
                                'category_ids' => $category_id
                            ));
                        } else {
                            if ($data[1] != null) {
                                // Invalid numeric data
                                $pending_to_save = [];
                                session()->flash("alert-danger", "Item row " . $loop_count . " has wrong entry!");
                                return back();
                            }
                        }
                    } catch (Exception $e) {
                        $pending_to_save = [];
                        session()->flash("alert-danger", "Date format error!");
                        return back();
                    }
                }
            }

            // Save the data
            DB::statement('ALTER TABLE inv_products AUTO_INCREMENT = 100000;');
            foreach ($pending_to_save as $data) {
                try {
                    DB::beginTransaction();

                    // Save product
                    $product = new Product();
                    $product->name = $data['name'];
                    $product->category_id = $data['category_ids']; // Use the category_id we found or fallback
                    $product->barcode = $data['barcode'];
                    $product->type = 'stockable';
                    $product->status = 1;
                    $product->save();

                    // Generate batch number
                    $batch_number = 'BATCH-' . date('Ymd') . '-' . str_pad($product->id, 4, '0', STR_PAD_LEFT);

                    // Save incoming stock
                    $incoming = new GoodsReceiving();
                    $incoming->product_id = $product->id;
                    $incoming->quantity = $data['quantity'];
                    $incoming->supplier_id = $request->supplier_id;
                    $incoming->expire_date = $data['date'];
                    $incoming->unit_cost = $data['unit_price'];
                    $incoming->sell_price = $data['sell_price'];
                    $incoming->total_cost = $data['quantity'] * $data['unit_price'];
                    $incoming->total_sell = $data['quantity'] * $data['sell_price'];
                    $incoming->item_profit = $data['quantity'] * ($data['sell_price'] - $data['unit_price']);
                    $incoming->batch_number = $batch_number;
                    $incoming->grn = 'GRN-' . date('Ymd') . '-' . str_pad($product->id, 4, '0', STR_PAD_LEFT);
                    $incoming->invoice_no = 'INV-IMPORT-' . date('Ymd') . '-' . str_pad($product->id, 4, '0', STR_PAD_LEFT);
                    $incoming->status = 1;
                    $incoming->store_id = $data['store_id'];
                    $incoming->created_by = Auth::user()->id;
                    $incoming->save();

                    // Save current stock
                    $current_stock = new CurrentStock();
                    $current_stock->product_id = $product->id;
                    $current_stock->quantity = $data['quantity'];
                    $current_stock->unit_cost = $data['unit_price'];
                    $current_stock->sell_price = $data['sell_price'];
                    $current_stock->total_cost = $data['quantity'] * $data['unit_price'];
                    $current_stock->total_sell = $data['quantity'] * $data['sell_price'];
                    $current_stock->item_profit = $data['quantity'] * ($data['sell_price'] - $data['unit_price']);
                    $current_stock->batch_number = $batch_number;
                    $current_stock->expire_date = $data['date'];
                    $current_stock->store_id = $data['store_id'];
                    $current_stock->status = 1;
                    $current_stock->created_by = Auth::user()->id;
                    $current_stock->save();

                    // Save price list
                    $price_list = new PriceList();
                    $price_list->stock_id = $current_stock->id;
                    $price_list->price_category_id = $data['price_category_id'];
                    $price_list->price = $data['sell_price'];
                    $price_list->status = 1;
                    $price_list->created_at = date('Y-m-d H:m:s');
                    $price_list->save();

                    // Save stock tracking
                    $tracking = new StockTracking();
                    $tracking->stock_id = $current_stock->id;
                    $tracking->product_id = $product->id;
                    $tracking->quantity = $data['quantity'];
                    $tracking->out_mode = 'Stock Taking';
                    $tracking->store_id = $data['store_id'];
                    $tracking->updated_by = Auth::user()->id;
                    $tracking->updated_at = date('Y-m-d');
                    $tracking->movement = 'IN';
                    $tracking->save();

                    DB::commit();

                } catch (Exception $e) {
                    DB::rollBack();
                    
                    // Clean up on duplicate entry
                    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    GoodsReceiving::truncate();
                    PriceList::truncate();
                    StockTracking::truncate();
                    CurrentStock::truncate();
                    Product::truncate();
                    DB::statement('ALTER TABLE inv_products AUTO_INCREMENT = 100000;');
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    
                    $pending_to_save = [];
                    session()->flash("alert-danger", $data['name'] . " has duplicate record!");
                    return back();
                }
            }

            // Create import history record
            $import_history = new ImportHistory();
            $import_history->store_id = $request->store_id;
            $import_history->price_category_id = $request->price_category_id;
            $import_history->supplier_id = $request->supplier_id;
            $import_history->created_by = Auth::user()->id;
            $import_history->save();
        }

        session()->flash("alert-success", "Data Imported Successfully!");
        return back();
    }

    private function validateRow($row, $row_number)
    {
        $errors = [];
        
        // Required fields
        if (empty($row[0])) $errors[] = "Product Code (Barcode) is required";
        if (empty($row[1])) $errors[] = "Product Name is required";
        if (empty($row[2])) $errors[] = "Brand is required";
        if (empty($row[3])) $errors[] = "Pack Size is required";
        if (empty($row[4])) $errors[] = "Unit is required";
        if (!isset($row[5]) || $row[5] === '') $errors[] = "Min Stock is required";
        if (!isset($row[6]) || $row[6] === '') $errors[] = "Max Stock is required";
        if (!isset($row[11]) || $row[11] === '') $errors[] = "Buy Price is required";
        if (!isset($row[12]) || $row[12] === '') $errors[] = "Sell Price is required";
        if (!isset($row[13]) || $row[13] === '') $errors[] = "Quantity is required";
        if (!isset($row[14]) || $row[14] === '') $errors[] = "Expiry is required";

        // Check for duplicates based on barcode
        if (!empty($row[0])) {
            $existingProduct = Product::where('barcode', trim($row[0]))->first();
            if ($existingProduct) {
                $errors[] = "Product with this barcode already exists";
            }
        }

        // Check for duplicates based on name, brand, and pack size
        if (!empty($row[1]) && !empty($row[2]) && !empty($row[3])) {
            $existingProduct = Product::where('name', trim($row[1]))
                ->where('brand', trim($row[2]))
                ->where('pack_size', trim($row[3]))
                ->first();

            if ($existingProduct) {
                $errors[] = "Product already exists with this Name, Brand and Pack Size combination";
            }
        }

        // Numeric validations
        if (isset($row[5]) && (!is_numeric($row[5]) || $row[5] < 0)) {
            $errors[] = "Min Stock must be a non-negative number";
        }
        if (isset($row[6]) && (!is_numeric($row[6]) || $row[6] < 0)) {
            $errors[] = "Max Stock must be a non-negative number";
        }
        if (isset($row[5]) && isset($row[6]) && is_numeric($row[5]) && is_numeric($row[6]) && $row[5] > $row[6]) {
            $errors[] = "Min Stock cannot be greater than Max Stock";
        }

        // Price validations
        if (isset($row[11]) && (!is_numeric($row[11]) || $row[11] < 0)) {
            $errors[] = "Buy Price must be a non-negative number";
        }
        if (isset($row[12]) && (!is_numeric($row[12]) || $row[12] < 0)) {
            $errors[] = "Sell Price must be a non-negative number";
        }
        if (isset($row[11]) && isset($row[12]) && is_numeric($row[11]) && is_numeric($row[12]) && $row[11] > $row[12]) {
            $errors[] = "Buy Price cannot be greater than Sell Price";
        }

        // Quantity validation
        if (isset($row[13]) && (!is_numeric($row[13]) || $row[13] < 0)) {
            $errors[] = "Quantity must be a non-negative number";
        }

        // Expiry date validation
        if (!empty($row[14])) {
            $expiry = trim($row[14]);
            if (!preg_match('/^[A-Za-z]{3}\/\d{2}$/', $expiry)) {
                $errors[] = "Expiry must be in MMM/YY format (e.g. Jun/22)";
            }
        }

        // Type validation
        if (!empty($row[9])) {
            $type = strtolower(trim($row[9]));
            if (!in_array($type, ['stockable', 'consumable'])) {
                $errors[] = "Type must be either 'stockable' or 'consumable'";
            }
        }

        // Status validation
        if (isset($row[10])) {
            $status = (int)$row[10];
            if (!in_array($status, [0, 1])) {
                $errors[] = "Status must be either 0 or 1";
            }
        }

        return $errors;
    }

    public function showPreview()
    {
        $preview_data = Session::get('import_preview');
        if (!$preview_data) {
            return redirect()->route('import-data')
                ->with('error', 'No preview data available. Please upload a file first.');
        }

        return view('import.preview', [
            'preview_data' => $preview_data['data'],
            'store_id' => $preview_data['store_id'],
            'price_category_id' => $preview_data['price_category_id'],
            'supplier_id' => $preview_data['supplier_id'],
            'temp_file' => $preview_data['file_name']
        ]);
    }
}
