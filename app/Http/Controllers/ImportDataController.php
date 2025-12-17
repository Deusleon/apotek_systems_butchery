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
use App\Setting;
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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class ImportDataController extends Controller {
    public function index() {
        if ( !Auth()->user()->checkPermission( 'Products Import' ) ) {
            abort( 403, 'Access Denied' );
        }

        $stores = Store::where( 'name', '<>', 'ALL' )->get();
        $import_history = ImportHistory::with( [ 'store', 'priceCategory', 'supplier', 'creator' ] )
        ->orderBy( 'created_at', 'desc' )
        ->take( 10 )
        ->get();

        return view( 'import.index', compact( 'stores',  'import_history' ) );
    }
    public function importData() {
        $categories = Category::all();
        $price_categories = PriceCategory::all();
        $suppliers = Supplier::orderby( 'name', 'ASC' )->get();
        $stores = Store::where( 'name', '<>', 'ALL' )->get();
        $import_history = ImportHistory::with( [ 'store', 'priceCategory', 'supplier', 'creator' ] )
        ->orderBy( 'created_at', 'desc' )
        ->take( 10 )
        ->get();

        return view( 'import.import_stocks', compact( 'categories', 'price_categories', 'stores', 'suppliers', 'import_history' ) );
    }
    public function downloadStockTemplate() {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue( 'A1', 'Product Code' );
        $sheet->setCellValue( 'B1', 'Product Name' );
        $sheet->setCellValue( 'C1', 'Buy Price' );
        $sheet->setCellValue( 'D1', 'Sell Price' );
        $sheet->setCellValue( 'E1', 'Quantity' );
        if ( Setting::where( 'id', 123 )->value( 'value' ) === 'YES' ) {
            $sheet->setCellValue( 'F1', 'Expiry' );
        }
        // Fetch all products from database
        $products = Product::orderBy( 'name' )->get();

        $row = 2;
        foreach ( $products as $product ) {
            $sheet->setCellValue( 'A'.$row, $product->id );
            // Product Code ( ID )
            $sheet->setCellValue( 'B'.$row, $product->name );
            // Product Name
            $sheet->setCellValue( 'C'.$row, '' );
            // Buy Price ( empty for user input )
            $sheet->setCellValue( 'D'.$row, '' );
            // Sell Price ( empty for user input )
            $sheet->setCellValue( 'E'.$row, '' );
            // Quantity ( empty for user input )
            if ( Setting::where( 'id', 123 )->value( 'value' ) === 'YES' ) {
                $sheet->setCellValue( 'F'.$row, '' );
                // Expiry ( empty for user input )
            }

            $row++;
        }

        // If no products exist, add a sample row
        if ( $products->isEmpty() ) {
            $sheet->setCellValue( 'A2', '1' );
            $sheet->setCellValue( 'B2', 'Sample Product - Please add products first' );
            $sheet->setCellValue( 'C2', '' );
            $sheet->setCellValue( 'D2', '' );
            $sheet->setCellValue( 'E2', '' );
            if ( Setting::where( 'id', 123 )->value( 'value' ) === 'YES' ) {
                $sheet->setCellValue( 'F2', '' );
            }
        }

        $writer = new Xlsx( $spreadsheet );
        $fileName = 'stock_import_template.xlsx';

        // Use Laravel's response()->download() for proper headers
        $tempFile = tempnam(sys_get_temp_dir(), 'stock_template');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;
        filename = "' . $fileName . '"'
        ])->deleteFileAfterSend(true);
    }
    public function downloadTemplate() {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $is_detailed = Setting::where( 'id', 127 )->value( 'value' );

        // Headers with proper column names matching Excel structure
        if ($is_detailed === 'Detailed') {
            $headers = [
                // 'A1' => 'code',
                'A1' => 'product_name',
                'B1' => 'barcode',
                'C1' => 'brand',
                'D1' => 'pack_size',
                'E1' => 'category',
                'F1' => 'unit',
                'G1' => 'min_stock',
                'H1' => 'max_stock'
            ];
        }

        if ($is_detailed === 'Normal') {
            $headers = [
            // 'A1' => 'code',
            'A1' => 'product_name',
            'B1' => 'barcode',
            'C1' => 'category',
            // 'E1' => 'Unit',
            'D1' => 'min_stock',
        ];
    }

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        if( $is_detailed === 'Detailed' ) {
            // Sample row for Detailed
            // $sheet->setCellValue( 'A2', '100001' );
            $sheet->setCellValue( 'A2', 'Sample Product' );
            $sheet->setCellValueExplicit('B2', '1234567890123', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue( 'C2', 'Sample Brand' );
            $sheet->setCellValue( 'D2', '500' );
            $sheet->setCellValue( 'E2', 'ml' );
            $sheet->setCellValue( 'F2', 'General' );
            $sheet->setCellValue( 'G2', '10' );
            $sheet->setCellValue( 'H2', '100' );
        }
        if( $is_detailed === 'Normal' ) {
            // Sample row for Normal
            // $sheet->setCellValue( 'A2', '100001' );
            $sheet->setCellValue( 'A2', 'Sample Product' );
            $sheet->setCellValueExplicit('B2', '1234567890123', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue( 'C2', 'BEVERAGE' );
            // $sheet->setCellValue( 'E2', 'ml' );
            $sheet->setCellValue( 'D2', '10' );
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'products_import_template.xlsx';

        // Use Laravel's response()->download() for proper headers
        $tempFile = tempnam( sys_get_temp_dir(), 'products_template' );
        $writer->save( $tempFile );

        return response()->download( $tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ] )->deleteFileAfterSend( true );
    }
    public function previewStockImport( Request $request ) {

        try {
            // Validate request
            $validator = Validator::make( $request->all(), [
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
            ] );

            if ( $validator->fails() ) {
                Log::error( 'Validation failed:', [ 'errors' => $validator->errors()->toArray() ] );
                return back()->withErrors( $validator )->withInput();
            }

            // Ensure file is present and valid
            if ( !$request->hasFile( 'file' ) || !$request->file( 'file' )->isValid() ) {
                Log::error( 'File upload failed - file not present or invalid' );
                return back()->withErrors( [ 'file' => 'The file upload failed. Please try again.' ] )->withInput();
            }

            $file = $request->file( 'file' );

            // Store the file temporarily
            $path = $file->storeAs( 'temp', uniqid() . '_' . $file->getClientOriginalName(), 'public' );

            Log::info( 'File stored temporarily', [
                'original_name' => $file->getClientOriginalName(),
                'temp_path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension()
            ] );

            // Read Excel file
            try {
                Log::info( 'Reading Excel file from path: ' . storage_path( 'app/public/' . $path ) );
                $excel_raw_data = Excel::toArray( null, storage_path( 'app/public/' . $path ) );

                if ( empty( $excel_raw_data ) || !isset( $excel_raw_data[ 0 ] ) || empty( $excel_raw_data[ 0 ] ) ) {
                    Log::error( 'Excel file is empty or invalid' );
                    return back()->withErrors( [ 'file' => 'The uploaded file appears to be empty or invalid' ] )->withInput();
                }

                Log::info( 'Successfully read Excel file', [ 'row_count' => count( $excel_raw_data[ 0 ] ) ] );

                $preview_data = [];

                foreach ( $excel_raw_data[ 0 ] as $index => $row ) {

                    if ( Setting::where( 'id', 123 )->value( 'value' ) === 'YES' ) {
                        $expectedColumns = 6;
                    } else {
                        $expectedColumns = 5;
                    }
                    $actualColumns = count( $row );

                    if ( $actualColumns < $expectedColumns ) {
                        return redirect()->route( 'import-stock' )->with( 'error', 'The file uploaded is missing required columns. Download template and fill all the required columns.' );
                    }

                    if ( $index === 0 ) continue;
                    // Skip header row
                    if ( empty( $row[ 0 ] ) ) continue;
                    // Skip empty rows

                    $row_number = $index + 1;
                    $row_errors = $this->validateStockRow( $row, $row_number );

                    $preview_data[] = [
                        'row_number' => $row_number,
                        'data' => $row,
                        'errors' => $row_errors
                    ];
                }

                if ( empty( $preview_data ) ) {
                    Log::warning( 'No valid data rows found in file' );
                    return back()->withErrors( [ 'file' => 'No valid data rows found in the file' ] )->withInput();
                }

                Log::info( 'Imported Data', $preview_data );
                // Store preview data in session
                Session::put( 'import_preview', [
                    'data' => $preview_data,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'price_category_id' => $request->price_category_id,
                    'store_id' => $request->store_id,
                    'supplier_id' => $request->supplier_id
                ] );

                return view( 'import.preview', [
                    'preview_data' => $preview_data,
                    'store_id' => $request->store_id,
                    'price_category_id' => $request->price_category_id,
                    'supplier_id' => $request->supplier_id,
                    'temp_file' => $path,
                    'alert_success' => 'File uploaded successfully. Please review the data below.',
                    'msgTyp' => 'success'
                ] );

            } catch ( \Exception $e ) {
                Log::error( 'Excel reading failed: ' . $e->getMessage() );
                Log::error( 'Stack trace: ' . $e->getTraceAsString() );
                return back()->withErrors( [ 'file' => 'Failed to read the Excel file. Please ensure it is not corrupted.' ] )->withInput();
            }

        } catch ( \Exception $e ) {
            Log::error( 'Preview generation failed: ' . $e->getMessage() );
            Log::error( 'Stack trace: ' . $e->getTraceAsString() );
            return back()->withErrors( [ 'file' => 'An error occurred while processing your file. Please try again.' ] )->withInput();
        }
    }
    public function previewImport( Request $request ) {
        try {
            // Validate request
            $validator = Validator::make( $request->all(), [
                'file' => [
                    'required',
                    'file',
                    'mimes:xlsx,xls,csv',
                    'max:20480'
                ],
            ], [
                'file.required' => 'Please select a file to import',
                'file.file' => 'The uploaded file is invalid',
                'file.mimes' => 'The file must be an Excel file (xlsx, xls) or CSV file',
                'file.max' => 'The file size must not exceed 20MB',
            ] );

            if ( $validator->fails() ) {
                Log::error( 'Validation failed:', [ 'errors' => $validator->errors()->toArray() ] );
                return back()->withErrors( $validator )->withInput();
            }

            // Ensure file is present and valid
            if ( !$request->hasFile( 'file' ) || !$request->file( 'file' )->isValid() ) {
                Log::error( 'File upload failed - file not present or invalid' );
                return back()->withErrors( [ 'file' => 'The file upload failed. Please try again.' ] )->withInput();
            }

            $file = $request->file( 'file' );

            // Store the file temporarily
            $path = $file->storeAs( 'temp', uniqid() . '_' . $file->getClientOriginalName(), 'public' );

            Log::info( 'File stored temporarily', [
                'original_name' => $file->getClientOriginalName(),
                'temp_path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension()
            ] );

            // Read Excel file
            try {
                Log::info( 'Reading Excel file from path: ' . storage_path( 'app/public/' . $path ) );
                $excel_raw_data = Excel::toArray( null, storage_path( 'app/public/' . $path ) );

                if ( empty( $excel_raw_data ) || !isset( $excel_raw_data[ 0 ] ) || empty( $excel_raw_data[ 0 ] ) ) {
                    Log::error( 'Excel file is empty or invalid' );
                    return back()->withErrors( [ 'file' => 'The uploaded file appears to be empty or invalid' ] )->withInput();
                }

                Log::info( 'Successfully read Excel file', [ 'row_count' => count( $excel_raw_data[ 0 ] ) ] );

                $preview_data = [];

                foreach ( $excel_raw_data[ 0 ] as $index => $row ) {

                    $is_detailed = Setting::where( 'id', 127 )->value( 'value' );

                    if ( $is_detailed === 'Detailed' ) {
                        $expectedColumns = 8;
                    } else if ( $is_detailed === 'Normal' ) {
                        $expectedColumns = 4;
                    }

                    $actualColumns = count( $row );

                    if ( $actualColumns < $expectedColumns ) {
                        return redirect()->route( 'import-products' )->with( 'error', 'The file uploaded is missing required columns. Download template and fill all the required columns.' );
                    }

                    if ( $index === 0 ) continue;
                    // Skip header row
                    if ( empty( $row[ 0 ] ) ) continue;
                    // Skip empty rows

                    $row_number = $index + 1;
                    if ( $is_detailed === 'Detailed' ) {
                        $row_errors = $this->validateRow( $row, $row_number );
                    } else if ( $is_detailed === 'Normal' ) {
                        $row_errors = $this->validateRowNormal( $row, $row_number );
                    }

                    $preview_data[] = [
                        'row_number' => $row_number,
                        'data' => $row,
                        'errors' => $row_errors
                    ];
                }

                if ( empty( $preview_data ) ) {
                    Log::warning( 'No valid data rows found in file' );
                    return back()->withErrors( [ 'file' => 'No valid data rows found in the file' ] )->withInput();
                }

                Log::info( 'Imported Data', $preview_data );
                // Store preview data in session
                Session::put( 'import_preview', [
                    'data' => $preview_data,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'store_id' => $request->store_id
                ] );

                return view( 'import.preview_products', [
                    'preview_data' => $preview_data,
                    'store_id' => $request->store_id,
                    'temp_file' => $path,
                    'is_detailed' => $is_detailed,
                    'alert_success' => 'File uploaded successfully. Please review the data below.',
                    'msgTyp' => 'success'
                ] );

            } catch ( \Exception $e ) {
                Log::error( 'Excel reading failed: ' . $e->getMessage() );
                Log::error( 'Stack trace: ' . $e->getTraceAsString() );
                return back()->withErrors( [ 'file' => 'Failed to read the Excel file. Please ensure it is not corrupted.' ] )->withInput();
            }

        } catch ( \Exception $e ) {
            Log::error( 'Preview generation failed: ' . $e->getMessage() );
            Log::error( 'Stack trace: ' . $e->getTraceAsString() );
            return back()->withErrors( [ 'file' => 'An error occurred while processing your file. Please try again.' ] )->withInput();
        }
    }
    private function getUploadErrorMessage( $errorCode ) {
        switch ( $errorCode ) {
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
    public function recordImport( Request $request ) {
        $start_time = microtime( true );
        Log::info( 'Starting products import process', [ 'start_time' => $start_time ] );

        // Get the import preview data from session
        $preview = Session::get( 'import_preview' );

        if ( !$preview || !isset( $preview[ 'file_path' ] ) ) {
            Log::error( 'No import data found in session' );
            return redirect()->route( 'import-products' )->with( 'error', 'No import data found. Please try uploading your file again.' );
        }

        $file_path = storage_path( 'app/public/' . $preview[ 'file_path' ] );

        if ( !file_exists( $file_path ) ) {
            Log::error( 'Import file not found at path: ' . $file_path );
            return redirect()->route( 'import-products' )->with( 'error', 'Import file not found. Please try uploading your file again.' );
        }

        try {
            Log::info( 'Reading Excel file for products import', [ 'file_path' => $file_path ] );

            // Use the improved ProductsImport class
            $import = new \App\Imports\ProductsImport();
            try {
                Excel::import( $import, $file_path );
            } catch ( \Maatwebsite\Excel\Validators\ValidationException $e ) {
                $failures = $e->failures();

                $messages = [];
                foreach ( $failures as $failure ) {
                    $messages[] = "On row {$failure->row()}, " . implode( ', ', $failure->errors() );
                }
                return redirect()->route( 'import-products' )->with( 'error', $messages[ 0 ] );
                // dd( $messages );
            }

            $results = $import->getResults();
            $successful_records = $results[ 'success_count' ];
            $failed_records = $results[ 'fail_count' ];
            $errors = $results[ 'errors' ];

            // Create import history record
            $import_history = ImportHistory::create( [
                'file_name' => $preview[ 'file_name' ],
                'store_id' => 1,
                'price_category_id' => null,
                'supplier_id' => null,
                'total_records' => $successful_records + $failed_records,
                'status' => 'completed',
                'created_by' => Auth::id(),
                'started_at' => now(),
                'successful_records' => $successful_records,
                'failed_records' => $failed_records,
                'progress' => 100,
                'error_log' => json_encode( $errors ),
                'processed_rows' => json_encode( range( 1, $successful_records + $failed_records ) ),
                'completed_at' => now()
            ] );

            $end_time = microtime( true );
            Log::info( 'Products import completed', [
                'successful_records' => $successful_records,
                'failed_records' => $failed_records,
                'total_records' => $successful_records + $failed_records,
                'total_time' => $end_time - $start_time,
                'errors' => $errors
            ] );

            // Clean up temporary file
            if ( file_exists( $file_path ) ) {
                unlink( $file_path );
                Log::info( 'Temporary file deleted', [ 'file_path' => $file_path ] );
            }

            Session::forget( 'import_preview' );

            $message = 'Import completed. ';
            $message .= 'Successfully imported '.number_format( $successful_records, 0 ).' products.';
            if ( $failed_records > 0 ) {
                $message .= "Failed to import {$failed_records} products. Check import history for details.";
                return redirect()->route( 'import-products' )->with( 'warning', $message );
            } else {
                return redirect()->route( 'import-products' )->with( 'success', $message );
            }

        } catch ( \Exception $e ) {
            Log::error( 'Products import process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file_path' => $file_path,
                'file_name' => $preview[ 'file_name' ] ?? null,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ] );
            return redirect()->route( 'import-products' )->with( 'error', 'Import failed: ' . $e->getMessage() );
        }
    }
    public function recordStockImport( Request $request ) {
        $start_time = microtime( true );
        // Log::info( 'Starting stock import process', [ 'start_time' => $start_time ] );

        // Get the import preview data from session
        $preview = Session::get( 'import_preview' );

        if ( !$preview || !isset( $preview[ 'file_path' ] ) ) {
            Log::error( 'No import data found in session' );
            return redirect()->route( 'import-data' )->with( 'error', 'No import data found. Please try uploading your file again.' );
        }

        $file_path = storage_path( 'app/public/' . $preview[ 'file_path' ] );

        if ( !file_exists( $file_path ) ) {
            Log::error( 'Import file not found at path: ' . $file_path );
            return redirect()->route( 'import-data' )->with( 'error', 'Import file not found. Please try uploading your file again.' );
        }

        try {
            Log::info( 'Reading Excel file for stock import', [ 'file_path' => $file_path ] );
            $excel_raw_data = Excel::toArray( null, $file_path );

            if ( empty( $excel_raw_data ) || !isset( $excel_raw_data[ 0 ] ) ) {
                Log::error( 'Excel file is empty or invalid' );
                return back()->with( 'error', 'The file appears to be empty or invalid.' );
            }

            $total_rows = count( $excel_raw_data[ 0 ] ) - 1;
            // Minus header
            // Log::info( 'Creating import history record', [ 'total_rows' => $total_rows ] );

            // Create import history record
            $import_history = ImportHistory::create( [
                'file_name' => $preview[ 'file_name' ],
                'store_id' => $preview[ 'store_id' ],
                'price_category_id' => $preview[ 'price_category_id' ],
                'supplier_id' => $preview[ 'supplier_id' ],
                'total_records' => $total_rows,
                'status' => 'processing',
                'created_by' => Auth::id(),
                'started_at' => now(),
                'successful_records' => 0,
                'failed_records' => 0,
                'progress' => 0,
                'error_log' => '',
                'processed_rows' => json_encode( [] )
            ] );

            $successful_records = 0;
            $failed_records = 0;
            $error_log = [];
            $processed_rows = [];

            // Log::info( 'Starting to process stock rows in batches', [ 'total_rows' => $total_rows ] );

            // Process in batches of 50 rows for stock import ( smaller batches due to more operations )
            $batch_size = 50;
            $batches = array_chunk( array_slice( $excel_raw_data[ 0 ], 1 ), $batch_size );
            // Skip header

            foreach ( $batches as $batch_index => $batch ) {
                $batch_start_time = microtime( true );
                Log::info( 'Processing stock batch', [
                    'batch_index' => $batch_index + 1,
                    'batch_size' => count( $batch ),
                    'start_time' => $batch_start_time
                ] );

                foreach ( $batch as $row_index => $row ) {
                    $global_index = ( $batch_index * $batch_size ) + $row_index + 1;
                    // +1 for header offset

                    try {
                        DB::beginTransaction();

                        // Validate row
                        $validation_errors = $this->validateStockRow( $row, $global_index );
                        if ( !empty( $validation_errors ) ) {
                            throw new Exception( implode( ', ', $validation_errors ) );
                        }

                        // Clean and prepare data
                        $buy_price = preg_replace( '/[^\d.]/', '', $row[ 2 ] );
                        $sell_price = preg_replace( '/[^\d.]/', '', $row[ 3 ] );
                        $quantity = preg_replace( '/[^\d.]/', '', $row[ 4 ] );
                        $total_profit = ( $sell_price - $buy_price ) * $quantity;

                        // 1. Create incoming_stock record first
                        $incoming_stock = new GoodsReceiving;
                        $incoming_stock->product_id = $row[ 0 ];
                        $incoming_stock->supplier_id = $preview[ 'supplier_id' ];
                        $incoming_stock->invoice_no = null;
                        $incoming_stock->batch_number = now()->format( 'Y-m-d' );
                        if ( Setting::where( 'id', 123 )->value( 'value' ) === 'YES' ) {
                            $incoming_stock->expire_date = $row[ 5 ];
                        } else {
                            $incoming_stock->expire_date = null;
                        }
                        $incoming_stock->quantity = $quantity;
                        $incoming_stock->unit_cost = $buy_price;
                        $incoming_stock->total_cost = $buy_price * $quantity;
                        $incoming_stock->store_id = $preview[ 'store_id' ];
                        $incoming_stock->total_sell = $sell_price * $quantity;
                        $incoming_stock->item_profit = $total_profit;
                        $incoming_stock->created_by = Auth::user()->id;
                        $incoming_stock->sell_price = $sell_price;
                        $incoming_stock->created_at = date( 'Y-m-d' );
                        $incoming_stock->save();

                        // Create stock record
                        $stock_data = [
                            'product_id' => $row[ 0 ],
                            'unit_cost' => $row[ 2 ],
                            'quantity' => $row[ 4 ],
                            'expiry_date' => $row[ 5 ] ?? null,
                            'store_id' => $preview[ 'store_id' ],
                            'created_by' => Auth::id(),
                            'incoming_stock_id' => $incoming_stock->id
                        ];

                        $stock = CurrentStock::create( $stock_data );

                        // Create stock tracking record
                        $stockTrack = StockTracking::create( [
                            'stock_id'=> $stock->id,
                            'product_id' => $stock->product_id,
                            'out_mode'=> 'Stock Import',
                            'quantity' => $stock->quantity,
                            'store_id' => $preview[ 'store_id' ],
                            'created_by' => Auth::id(),
                            'updated_by' => Auth::id(),
                            'updated_at' => date( 'Y-m-d' ),
                            'movement' => 'IN',
                        ] );

                        // Create or update price list
                        $price_list = PriceList::updateOrCreate(
                            [
                                'stock_id' => $stock->id,
                                'price_category_id' => $preview[ 'price_category_id' ]
                            ],
                            [
                                'price' => $sell_price,
                                'status' => 1,
                                'created_by' => Auth::id(),
                                'updated_by' => Auth::id()
                            ]
                        );

                        DB::commit();
                        $successful_records++;
                        $processed_rows[] = $global_index;

                    } catch ( \Exception $e ) {
                        DB::rollBack();
                        $failed_records++;
                        $error_log[] = 'Row ' . $global_index . ': ' . $e->getMessage();
                        Log::error( 'Stock row processing failed', [
                            'row_number' => $global_index,
                            'error' => $e->getMessage()
                        ] );
                    }
                }

                // Update progress after each batch ( less frequent updates )
                $progress = round( ( ( $batch_index + 1 ) * $batch_size / $total_rows ) * 100 );
                if ( $progress > 100 ) $progress = 100;

                $import_history->update( [
                    'progress' => $progress,
                    'successful_records' => $successful_records,
                    'failed_records' => $failed_records,
                    'error_log' => json_encode( $error_log ),
                    'processed_rows' => json_encode( $processed_rows )
                ] );

                $batch_end_time = microtime( true );
                Log::info( 'Stock batch processed', [
                    'batch_index' => $batch_index + 1,
                    'batch_time' => $batch_end_time - $batch_start_time,
                    'progress' => $progress
                ] );
            }

            // Update final status
            $import_history->update( [
                'status' => 'completed',
                'completed_at' => now(),
                'progress' => 100
            ] );

            $end_time = microtime( true );
            Log::info( 'Stock import completed', [
                'successful_records' => $successful_records,
                'failed_records' => $failed_records,
                'total_records' => $total_rows,
                'total_time' => $end_time - $start_time
            ] );

            // Clean up temporary file
            if ( file_exists( $file_path ) ) {
                unlink( $file_path );
                Log::info( 'Temporary file deleted', [ 'file_path' => $file_path ] );
            }

            Session::forget( 'import_preview' );

            $message = 'Successfully imported '.number_format( $successful_records ).' stock records. ';
            if ( $failed_records > 0 ) {
                $message .= 'Failed to import '.number_format( $failed_records ).' records. Check import history for details.';
                return redirect()->route( 'import-data' )->with( 'warning', $message );
            } else {
                return redirect()->route( 'import-data' )->with( 'success', $message );
            }

        } catch ( Exception $e ) {
            Log::error( 'Stock import process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ] );
            return redirect()->route( 'import-data' )->with( 'error', 'Import failed: ' . $e->getMessage() );
        }
    }
    private function validateRow( $row, $row_number ) {
        // Log::info( 'Validating row', [ 'row_number' => $row_number ] );

        $errors = [];

        // Required fields
        if ( empty( $row[ 0 ] ) ) $errors[] = 'Product name is required';
        if ( empty( $row[ 4 ] ) ) $errors[] = 'Category is required';

        // Numeric validations
        if ( !empty( $row[ 3 ] ) && !is_numeric( $row[ 3 ] ) ) $errors[] = 'Pack Size must be numeric';
        if ( !empty( $row[ 6 ] ) && !is_numeric( $row[ 6 ] ) ) $errors[] = 'Min stock must be numeric';
        if ( !empty( $row[ 7 ] ) && !is_numeric( $row[ 7 ] ) ) $errors[] = 'Max stock must be numeric';

        // Min/Max stock validation
        if ( !empty( $row[ 6 ] ) && !empty( $row[ 7 ] ) && $row[ 6 ] > $row[ 7 ] ) {
            $errors[] = 'Min stock cannot be greater than max stock';
        }

        // Uniqueness validation ( name+category )
        if ( !empty( $row[ 0 ] ) && !empty( $row[ 4 ] ) ) {
            $exists = DB::table( 'inv_products' )
            ->where( 'name', $row[ 0 ] )
            ->where( 'category_id', $row[ 4 ] )
            ->exists();

            if ( $exists ) {
                $errors[] = "Product '{$row[0]}' already exists in this category";
            }
        }

        if ( !empty( $errors ) ) {
            Log::warning( 'Row validation failed', [
                'row_number' => $row_number,
                'errors' => $errors
            ] );
        } else {
            // Log::info( 'Row validation passed', [ 'row_number' => $row_number ] );
        }

        return $errors;
    }
    private function validateRowNormal( $row, $row_number ) {
        // Log::info( 'Validating row', [ 'row_number' => $row_number ] );

        $errors = [];

        // Required fields
        if ( empty( $row[ 0 ] ) ) $errors[] = 'Product name is required';
        if ( empty( $row[ 2 ] ) ) $errors[] = 'Category is required';

        // Numeric validations
        if ( !empty( $row[ 3 ] ) && !is_numeric( $row[ 3 ] ) ) $errors[] = 'Min stock must be numeric';

        // Uniqueness validation ( name+category )
        if ( !empty( $row[ 0 ] ) && !empty( $row[ 2 ] ) ) {
            $exists = DB::table( 'inv_products' )
            ->where( 'name', $row[ 0 ] )
            ->where( 'category_id', $row[ 2 ] )
            ->exists();

            if ( $exists ) {
                $errors[] = "Product '{$row[0]}' already exists in this category";
            }
        }

        if ( !empty( $errors ) ) {
            Log::warning( 'Row validation failed', [
                'row_number' => $row_number,
                'errors' => $errors
            ] );
        } else {
            // Log::info( 'Row validation passed', [ 'row_number' => $row_number ] );
        }

        return $errors;
    }
    private function validateStockRow( $row, $row_number ) {
        // Log::info( 'Validating row', [ 'row_number' => $row_number ] );
        $productCode = trim( $row[ 0 ] ?? '' );
        $productName = trim( $row[ 1 ] ?? '' );
        $buyPrice    = $row[ 2 ] ?? null;
        $sellPrice   = $row[ 3 ] ?? null;
        $quantity    = $row[ 4 ] ?? null;
        $errors = [];

        // Required fields
        if ( empty( $row[ 0 ] ) ) $errors[] = 'Product code is required';
        if ( empty( $row[ 1 ] ) ) $errors[] = 'Product name is required';
        if ( $buyPrice === '' || $buyPrice === null ) {
            $errors[] = 'Buy price is required';
        }
        if ( $sellPrice === '' || $sellPrice === null ) {
            $errors[] = 'Sell price is required';
        }
        if ( $quantity === '' || $quantity === null ) {
            $errors[] = 'Quantity is required';
        }

        // Database existence validation
        if ( $productCode !== '' ) {
            $product = DB::table( 'inv_products' )->where( 'id', $productCode )->first();

            if ( !$product ) {
                $errors[] = "Product code {$productCode} does not exist in the database.";
            } elseif ( strcasecmp( $product->name, $productName ) !== 0 ) {
                $errors[] = "Product name '{$productName}' does not match the name for product code {$productCode} '{$product->name}'.";
            }
        }

        // Numeric validations
        if ( !empty( $row[ 2 ] ) && !is_numeric( preg_replace( '/[^\d.]/', '', $row[ 2 ] ) ) ) $errors[] = 'Buy price must be numeric';
        if ( !empty( $row[ 3 ] ) && !is_numeric( preg_replace( '/[^\d.]/', '', $row[ 3 ] ) ) ) $errors[] = 'Sell price must be numeric';
        if ( !empty( $row[ 4 ] ) && !is_numeric( preg_replace( '/[^\d.]/', '', $row[ 4 ] ) ) ) $errors[] = 'Quantity must be numeric';

        // Price validation
        $buy_price = !empty( $row[ 2 ] ) ? preg_replace( '/[^\d.]/', '', $row[ 2 ] ) : 0;
        $sell_price = !empty( $row[ 3 ] ) ? preg_replace( '/[^\d.]/', '', $row[ 3 ] ) : 0;
        if ( $buy_price > 0 && $sell_price > 0 && $buy_price >= $sell_price ) {
            $errors[] = 'Buy price must be less than sell price';
        }

        if ( !empty( $errors ) ) {
            Log::warning( 'Row validation failed', [
                'row_number' => $row_number,
                'errors' => $errors
            ] );
        } else {
            // Log::info( 'Row validation passed', [ 'row_number' => $row_number ] );
        }

        return $errors;
    }
    public function showPreview() {
        $preview_data = Session::get( 'import_preview' );
        if ( !$preview_data ) {
            return redirect()->route( 'import-data' )
            ->with( 'error', 'No preview data available. Please upload a file first.' );
        }

        return view( 'import.preview', [
            'preview_data' => $preview_data[ 'data' ],
            'store_id' => $preview_data[ 'store_id' ],
            'price_category_id' => $preview_data[ 'price_category_id' ],
            'supplier_id' => $preview_data[ 'supplier_id' ],
            'temp_file' => $preview_data[ 'file_name' ]
        ] );
    }

}
