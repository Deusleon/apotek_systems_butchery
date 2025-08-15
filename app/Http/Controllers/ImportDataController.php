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
use Illuminate\Http\Response;
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

    public function downloadTemplate_old()
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
    public function downloadTemplate()
    {
        $file = public_path() . "/fileStore/import_template/import_data_template.xlsx";
        $headers = array(
            'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );
        return response()->download($file, 'import_data_template.xlsx', $headers);
    }

    public function previewImport(Request $request)
    {
        \Log::info('Request reaching preview route', [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'files' => $request->allFiles(),
            'post_size' => $request->server('CONTENT_LENGTH'),
            'max_post_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize')
        ]);
        
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'file' => [
                    'required',
                    'file',
                    'mimes:xlsx,xls,csv',
                    'max:20480' // 20MB in kilobytes
                ],
                'price_category_id' => 'required|exists:price_categories,id',
                'store_id' => 'required|exists:inv_stores,id',
                'supplier_id' => 'required|exists:inv_suppliers,id',
            ], [
                'file.required' => 'Please select a file to import',
                'file.file' => 'The uploaded file is invalid',
                'file.mimes' => 'The file must be an Excel file (xlsx, xls) or CSV file',
                'file.max' => 'The file size must not exceed 20MB',
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', ['errors' => $validator->errors()->toArray()]);
                return back()->withErrors($validator)->withInput();
            }

            // Ensure file is present and valid
            if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
                \Log::error('File upload failed - file not present or invalid');
                return back()->withErrors(['file' => 'The file upload failed. Please try again.'])->withInput();
            }

            $file = $request->file('file');
            
            // Store the file temporarily
            $path = $file->storeAs('temp', uniqid() . '_' . $file->getClientOriginalName(), 'public');
            
            \Log::info('File stored temporarily', [
                'original_name' => $file->getClientOriginalName(),
                'temp_path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension()
            ]);

            // Read Excel file
            try {
                \Log::info('Reading Excel file from path: ' . storage_path('app/public/' . $path));
                $excel_raw_data = Excel::toArray(null, storage_path('app/public/' . $path));
                
                if (empty($excel_raw_data) || !isset($excel_raw_data[0]) || empty($excel_raw_data[0])) {
                    \Log::error('Excel file is empty or invalid');
                    return back()->withErrors(['file' => 'The uploaded file appears to be empty or invalid'])->withInput();
                }

                \Log::info('Successfully read Excel file', ['row_count' => count($excel_raw_data[0])]);
                
                $preview_data = [];
                
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

                if (empty($preview_data)) {
                    \Log::warning('No valid data rows found in file');
                    return back()->withErrors(['file' => 'No valid data rows found in the file'])->withInput();
                }

                // Store preview data in session
                Session::put('import_preview', [
                    'data' => $preview_data,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'price_category_id' => $request->price_category_id,
                    'store_id' => $request->store_id,
                    'supplier_id' => $request->supplier_id
                ]);

                // Flash success message
                Session::flash('success', 'File uploaded successfully. Please review the data below.');

                return view('import.preview', [
                    'preview_data' => $preview_data,
                    'store_id' => $request->store_id,
                    'price_category_id' => $request->price_category_id,
                    'supplier_id' => $request->supplier_id,
                    'temp_file' => $path // Changed to use the full path
                ]);

            } catch (\Exception $e) {
                \Log::error('Excel reading failed: ' . $e->getMessage());
                \Log::error('Stack trace: ' . $e->getTraceAsString());
                return back()->withErrors(['file' => 'Failed to read the Excel file. Please ensure it is not corrupted.'])->withInput();
            }

        } catch (\Exception $e) {
            \Log::error('Preview generation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['file' => 'An error occurred while processing your file. Please try again.'])->withInput();
        }
    }

    private function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }

    public function recordImport(Request $request)
    {
        \Log::info('Starting import process');
        
        // Get the import preview data from session
        $preview = Session::get('import_preview');
        
        if (!$preview || !isset($preview['file_path'])) {
            \Log::error('No import data found in session');
            return back()->with('error', 'No import data found. Please try uploading your file again.');
        }

        $file_path = storage_path('app/public/' . $preview['file_path']);
        
        if (!file_exists($file_path)) {
            \Log::error('Import file not found at path: ' . $file_path);
            return back()->with('error', 'Import file not found. Please try uploading your file again.');
        }

        try {
            \Log::info('Reading Excel file for import', ['file_path' => $file_path]);
            $excel_raw_data = Excel::toArray(null, $file_path);
            
            if (empty($excel_raw_data) || !isset($excel_raw_data[0])) {
                \Log::error('Excel file is empty or invalid');
                return back()->with('error', 'The file appears to be empty or invalid.');
            }

            \Log::info('Creating import history record');
            // Create import history record
            $import_history = ImportHistory::create([
                'file_name' => $preview['file_name'],
                'store_id' => $preview['store_id'],
                'price_category_id' => $preview['price_category_id'],
                'supplier_id' => $preview['supplier_id'],
                'total_records' => count($excel_raw_data[0]) - 1, // Minus header
                'status' => 'processing',
                'created_by' => Auth::id(),
                'started_at' => now(),
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

            DB::statement('ALTER TABLE inv_products AUTO_INCREMENT = 100000;');

            \Log::info('Starting to process rows', ['total_rows' => count($excel_raw_data[0]) - 1]);

            foreach ($excel_raw_data[0] as $index => $row) {
                if ($index === 0) continue; // Skip header
                
                \Log::info('Processing row', ['row_number' => $index + 1, 'data' => $row]);

                try {
                    DB::beginTransaction();

                    // Validate row
                    $validation_errors = $this->validateRow($row, $index + 1);
                    if (!empty($validation_errors)) {
                        throw new Exception(implode(", ", $validation_errors));
                    }

                    // Clean and prepare data
                    $buy_price = preg_replace('/[^\d.]/', '', $row[11]);
                    $sell_price = preg_replace('/[^\d.]/', '', $row[12]);
                    $quantity = preg_replace('/[^\d.]/', '', $row[13]);

                    \Log::info('Cleaned data', [
                        'buy_price' => $buy_price,
                        'sell_price' => $sell_price,
                        'quantity' => $quantity
                    ]);

                    // Check if product exists by barcode
                    $product = Product::where('barcode', $row[0])->first();
                    
                    if ($product) {
                        \Log::info('Product exists, updating', ['product_id' => $product->id]);
                    } else {
                        \Log::info('Creating new product');
                    }

                    // Get or create category
                    $category = Category::firstOrCreate(
                        ['name' => $row[8]],
                        ['created_by' => Auth::id()]
                    );

                    \Log::info('Category processed', ['category_id' => $category->id, 'category_name' => $category->name]);

                    // Create or update product
                    $product_data = [
                        'barcode' => $row[0],
                        'name' => $row[1],
                        'brand' => $row[2],
                        'pack_size' => $row[3],
                        'unit' => $row[4],
                        'min_stock' => $row[5],
                        'max_stock' => $row[6],
                        'category_id' => $category->id,
                        'type' => $row[9] ?? 'stockable',
                        'status' => isset($row[10]) ? ($row[10] ? 'active' : 'inactive') : 'active',
                        'updated_by' => Auth::id()
                    ];

                    if (!$product) {
                        $product_data['created_by'] = Auth::id();
                        $product = Product::create($product_data);
                        \Log::info('New product created', ['product_id' => $product->id]);
                    } else {
                        $product->update($product_data);
                        \Log::info('Existing product updated', ['product_id' => $product->id]);
                    }

                    // Create or update current stock
                    if ($quantity > 0) {
                        $current_stock = CurrentStock::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'store_id' => $preview['store_id']
                            ],
                            [
                                'quantity' => $quantity,
                                'updated_by' => Auth::id()
                            ]
                        );

                        \Log::info('Current stock processed', ['current_stock_id' => $current_stock->id]);

                        // Create stock tracking record
                        StockTracking::create([
                            'product_id' => $product->id,
                            'store_id' => $preview['store_id'],
                            'quantity' => $quantity,
                            'type' => 'import',
                            'reference_id' => $import_history->id,
                            'created_by' => Auth::id()
                        ]);

                        \Log::info('Stock tracking record created');
                    }

                    // Create or update price list
                    $price_list = PriceList::updateOrCreate(
                        [
                            'stock_id' => $current_stock->id,
                            'price_category_id' => $preview['price_category_id']
                        ],
                        [
                            'price' => $sell_price,
                            'status' => 1,
                            'updated_by' => Auth::id()
                        ]
                    );

                    \Log::info('Price list processed', ['price_list_id' => $price_list->id]);

                    DB::commit();
                    $successful_records++;
                    $processed_rows[] = $index + 1;
                    \Log::info('Row processed successfully');

                } catch (\Exception $e) {
                    DB::rollBack();
                    $failed_records++;
                    $error_log[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                    \Log::error('Row processing failed', [
                        'row_number' => $index + 1,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }

                // Update import history progress
                $progress = round(($index / (count($excel_raw_data[0]) - 1)) * 100);
                $import_history->update([
                    'progress' => $progress,
                    'successful_records' => $successful_records,
                    'failed_records' => $failed_records,
                    'error_log' => json_encode($error_log),
                    'processed_rows' => json_encode($processed_rows)
                ]);
            }

            // Update final status
            $import_history->update([
                'status' => 'completed',
                'completed_at' => now(),
                'progress' => 100
            ]);

            \Log::info('Import completed', [
                'successful_records' => $successful_records,
                'failed_records' => $failed_records,
                'total_records' => count($excel_raw_data[0]) - 1
            ]);

            // Clean up temporary file
            if (file_exists($file_path)) {
                unlink($file_path);
                \Log::info('Temporary file deleted', ['file_path' => $file_path]);
            }

            Session::forget('import_preview');

            $message = "Import completed. ";
            $message .= "Successfully imported {$successful_records} products. ";
            if ($failed_records > 0) {
                $message .= "Failed to import {$failed_records} products. Check import history for details.";
                return redirect()->route('import-data')->with('warning', $message);
            } else {
                return redirect()->route('import-data')->with('success', $message);
            }

        } catch (\Exception $e) {
            \Log::error('Import process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function validateRow($row, $row_number)
    {
        \Log::info('Validating row', ['row_number' => $row_number]);
        
        $errors = [];

        // Required fields
        if (empty($row[0])) $errors[] = "Product code is required";
        if (empty($row[1])) $errors[] = "Product name is required";
        if (empty($row[3])) $errors[] = "Pack size is required";
        if (empty($row[4])) $errors[] = "Unit is required";
        if (empty($row[8])) $errors[] = "Category is required";

        // Numeric validations
        if (!empty($row[5]) && !is_numeric($row[5])) $errors[] = "Min stock must be numeric";
        if (!empty($row[6]) && !is_numeric($row[6])) $errors[] = "Max stock must be numeric";
        if (!empty($row[11]) && !is_numeric(preg_replace('/[^\d.]/', '', $row[11]))) $errors[] = "Buy price must be numeric";
        if (!empty($row[12]) && !is_numeric(preg_replace('/[^\d.]/', '', $row[12]))) $errors[] = "Sell price must be numeric";
        if (!empty($row[13]) && !is_numeric(preg_replace('/[^\d.]/', '', $row[13]))) $errors[] = "Quantity must be numeric";

        // Min/Max stock validation
        if (!empty($row[5]) && !empty($row[6]) && $row[5] > $row[6]) {
            $errors[] = "Min stock cannot be greater than max stock";
        }

        // Price validation
        $buy_price = !empty($row[11]) ? preg_replace('/[^\d.]/', '', $row[11]) : 0;
        $sell_price = !empty($row[12]) ? preg_replace('/[^\d.]/', '', $row[12]) : 0;
        if ($buy_price > 0 && $sell_price > 0 && $buy_price >= $sell_price) {
            $errors[] = "Buy price must be less than sell price";
        }

        if (!empty($errors)) {
            \Log::warning('Row validation failed', [
                'row_number' => $row_number,
                'errors' => $errors
            ]);
        } else {
            \Log::info('Row validation passed', ['row_number' => $row_number]);
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
