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
        try {
            \Log::info('Starting template download');
            $template = new ImportTemplate();
            
            // Log the headings
            \Log::info('Template headings:', $template->headings());
            
            // Generate the CSV file instead of Excel
            return \Excel::download($template, 'product_import_template.csv', \Maatwebsite\Excel\Excel::CSV, [
                'Content-Type' => 'text/csv',
            ]);
        } catch (\Exception $e) {
            \Log::error('Template generation failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return back()->with('error', 'Failed to generate template: ' . $e->getMessage());
        }
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
        $preview_data = json_decode($request->preview_data, true);
        if (!$preview_data) {
            return back()->with('error', 'Please preview the import first');
        }

        try {
            DB::beginTransaction();

            // Create import history record with enhanced metadata
            $import_history = ImportHistory::create([
                'file_name' => $request->temp_file,
                'store_id' => $request->store_id,
                'price_category_id' => $request->price_category_id,
                'supplier_id' => $request->supplier_id,
                'total_records' => count($preview_data),
                'status' => 'processing',
                'created_by' => Auth::id(),
                'started_at' => now()->format('Y-m-d H:i:s'),
                'successful_records' => 0,
                'failed_records' => 0,
                'progress' => 0,
                'error_log' => '',
                'processed_rows' => json_encode([])
            ]);

            $successful_records = 0;
            $failed_records = 0;
            $error_log = [];
            $processed_rows = [];

            foreach ($preview_data as $row_data) {
                $row = $row_data['data'];

                try {
                    // Validate row data
                    $validation_errors = $this->validateRow($row, $row_data['row_number']);
                    if (!empty($validation_errors)) {
                        throw new Exception(implode(", ", $validation_errors));
                    }

                    // Create or update product
                    $product = new Product([
                        'barcode' => trim($row[0]),
                        'name' => trim($row[1]),
                        'brand' => trim($row[2]),
                        'pack_size' => trim($row[3]),
                        'unit' => trim($row[4]),
                        'min_stock' => (float)$row[5],
                        'max_stock' => (float)$row[6],
                        'category_id' => trim($row[7]),
                        'sub_category_id' => trim($row[8]),
                        'type' => strtolower(trim($row[9])) ?: 'stockable',
                        'status' => isset($row[10]) ? (int)$row[10] : 1
                    ]);
                    $product->save();

                    // Generate batch number
                    $batch_number = 'BATCH-' . date('Ymd') . '-' . str_pad($product->id, 4, '0', STR_PAD_LEFT);

                    // Create incoming stock record
                    $incoming_stock = new GoodsReceiving([
                        'product_id' => $product->id,
                        'quantity' => (float)$row[13],
                        'unit_cost' => (float)$row[11],
                        'sell_price' => (float)$row[12],
                        'total_cost' => (float)$row[11] * (float)$row[13],
                        'total_sell' => (float)$row[12] * (float)$row[13],
                        'item_profit' => ((float)$row[12] - (float)$row[11]) * (float)$row[13],
                        'supplier_id' => $request->supplier_id,
                        'batch_number' => $batch_number,
                        'expire_date' => trim($row[14]),
                        'grn' => 'GRN-' . date('Ymd') . '-' . str_pad($product->id, 4, '0', STR_PAD_LEFT),
                        'invoice_no' => 'INV-IMPORT-' . date('Ymd') . '-' . str_pad($product->id, 4, '0', STR_PAD_LEFT),
                        'status' => 1,
                        'store_id' => $request->store_id
                    ]);
                    $incoming_stock->save();

                    // Create current stock record
                    $current_stock = new CurrentStock([
                        'product_id' => $product->id,
                        'quantity' => (float)$row[13],
                        'unit_cost' => (float)$row[11],
                        'sell_price' => (float)$row[12],
                        'total_cost' => (float)$row[11] * (float)$row[13],
                        'total_sell' => (float)$row[12] * (float)$row[13],
                        'item_profit' => ((float)$row[12] - (float)$row[11]) * (float)$row[13],
                        'batch_number' => $batch_number,
                        'expire_date' => trim($row[14]),
                        'store_id' => $request->store_id,
                        'status' => 1
                    ]);
                    $current_stock->save();

                    // Create price list entry
                    $price_list = new PriceList([
                        'stock_id' => $current_stock->id,
                        'price_category_id' => $request->price_category_id,
                        'status' => 1
                    ]);
                    $price_list->save();

                    // Create stock tracking entry
                    $tracking = new StockTracking([
                        'stock_id' => $current_stock->id,
                        'product_id' => $product->id,
                        'quantity' => (float)$row[13],
                        'out_mode' => 'Initial Import',
                        'store_id' => $request->store_id
                    ]);
                    $tracking->save();

                    $successful_records++;
                    $processed_rows[] = [
                        'row' => $row_data['row_number'],
                        'product_id' => $product->id,
                        'status' => 'success'
                    ];

                } catch (Exception $e) {
                    $failed_records++;
                    $error_log[] = "Row {$row_data['row_number']}: " . $e->getMessage();
                    $processed_rows[] = [
                        'row' => $row_data['row_number'],
                        'status' => 'failed',
                        'error' => $e->getMessage()
                    ];
                }

                // Update import history progress
                $import_history->update([
                    'successful_records' => $successful_records,
                    'failed_records' => $failed_records,
                    'error_log' => implode("\n", $error_log),
                    'processed_rows' => json_encode($processed_rows),
                    'status' => 'processing',
                    'progress' => ($successful_records + $failed_records) / count($preview_data) * 100
                ]);
            }

            // Calculate final status
            $final_status = $failed_records === 0 ? 'completed' : 
                          ($successful_records === 0 ? 'failed' : 'completed_with_errors');

            // Update final status and completion time
            $import_history->update([
                'status' => $final_status,
                'completed_at' => now()->format('Y-m-d H:i:s'),
                'processing_time' => $import_history->started_at ? now()->diffInSeconds($import_history->started_at) : 0,
                'final_summary' => json_encode([
                    'total_records' => count($preview_data),
                    'successful_records' => $successful_records,
                    'failed_records' => $failed_records,
                    'success_rate' => count($preview_data) > 0 ? ($successful_records / count($preview_data)) * 100 : 0,
                    'error_count' => count($error_log)
                ])
            ]);

            DB::commit();
            Session::forget('import_preview');

            $message = "Import completed. Successfully imported {$successful_records} products.";
            if ($failed_records > 0) {
                $message .= " Failed to import {$failed_records} products.";
            }

            return redirect()->route('import-data')->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Import error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            if (isset($import_history)) {
                $import_history->update([
                    'status' => 'failed',
                    'error_log' => $e->getMessage(),
                    'completed_at' => now()->format('Y-m-d H:i:s'),
                    'processing_time' => $import_history->started_at ? now()->diffInSeconds($import_history->started_at) : 0
                ]);
            }

            return back()->with('error', 'Import failed: ' . $e->getMessage())->withInput();
        }
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
