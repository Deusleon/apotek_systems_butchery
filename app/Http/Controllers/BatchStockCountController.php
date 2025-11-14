<?php

namespace App\Http\Controllers;

use App\CurrentStock;
use App\Product;
use App\StockAdjustmentLog;
use App\StockTracking;
use App\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class BatchStockCountController extends Controller
{
    public function index()
    {
        return view('stock_management.batch_stock_count.index');
    }

    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:20480' // 20MB in kilobytes
            ],
            'store_id' => 'required|exists:inv_stores,id',
        ], [
            'file.required' => 'Please select a file to import',
            'file.file' => 'The uploaded file is invalid',
            'file.mimes' => 'The file must be an Excel file (xlsx, xls) or CSV file',
            'file.max' => 'The file size must not exceed 20MB',
        ]);

        if ($validator->fails()) {
            Log::error('Batch Stock Count Preview Validation Failed:', ['errors' => $validator->errors()->toArray()]);
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('file');
            $path = $file->storeAs('temp', uniqid() . '_' . $file->getClientOriginalName(), 'public');

            $excel_raw_data = Excel::toArray(null, storage_path('app/public/' . $path));

            if (empty($excel_raw_data) || !isset($excel_raw_data[0]) || empty($excel_raw_data[0])) {
                return back()->withErrors(['file' => 'The uploaded file appears to be empty or invalid'])->withInput();
            }

            $preview_data = [];
            foreach ($excel_raw_data[0] as $index => $row) {
                if ($index === 0) continue; // Skip header row
                if (empty($row[0])) continue; // Skip empty rows

                $row_number = $index + 1;
                $row_errors = $this->validateRow($row, $row_number);
                
                $product_id = $row[0]; // Assuming product_id is in the first column
                $physical_stock = $row[1]; // Assuming physical_stock is in the second column

                // Fetch current QOH for comparison in preview
                $currentStock = CurrentStock::where('product_id', $product_id)
                                            ->where('store_id', $request->store_id)
                                            ->first();
                $qoh = $currentStock ? $currentStock->quantity : 0;
                $difference = $physical_stock - $qoh;

                $preview_data[] = [
                    'row_number' => $row_number,
                    'data' => $row,
                    'product_id' => $product_id,
                    'product_name' => Product::find($product_id)->name ?? 'N/A',
                    'physical_stock' => $physical_stock,
                    'qoh' => $qoh,
                    'difference' => $difference,
                    'errors' => $row_errors
                ];
            }

            if (empty($preview_data)) {
                return back()->withErrors(['file' => 'No valid data rows found in the file'])->withInput();
            }

            Session::put('batch_stock_count_preview', [
                'data' => $preview_data,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'store_id' => $request->store_id,
            ]);

            Session::flash('success', 'File uploaded successfully. Please review the data below.');

            return view('stock_management.batch_stock_count.preview', [
                'preview_data' => $preview_data,
                'store_id' => $request->store_id,
                'temp_file' => $path
            ]);

        } catch (Exception $e) {
            Log::error('Batch Stock Count Preview Generation Failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return back()->with('error', 'Failed to generate preview: ' . $e->getMessage());
        }
    }

    public function process(Request $request)
    {
        $preview = Session::get('batch_stock_count_preview');

        if (!$preview || !isset($preview['file_path'])) {
            return back()->with('error', 'No import data found. Please try uploading your file again.');
        }

        $file_path = storage_path('app/public/' . $preview['file_path']);

        if (!file_exists($file_path)) {
            return back()->with('error', 'Import file not found. Please try uploading your file again.');
        }

        try {
            $excel_raw_data = Excel::toArray(null, $file_path);

            if (empty($excel_raw_data) || !isset($excel_raw_data[0])) {
                return back()->with('error', 'The file appears to be empty or invalid.');
            }

            $successful_records = 0;
            $failed_records = 0;
            $error_log = [];

            DB::beginTransaction();
            foreach ($excel_raw_data[0] as $index => $row) {
                if ($index === 0) continue; // Skip header

                try {
                    $row_number = $index + 1;
                    $validation_errors = $this->validateRow($row, $row_number);
                    if (!empty($validation_errors)) {
                        throw new Exception(implode(", ", $validation_errors));
                    }

                    $productId = $row[0];
                    $physicalStock = $row[1];
                    $store_id = $preview['store_id'];

                    $currentStock = CurrentStock::where('product_id', $productId)
                                                ->where('store_id', $store_id)
                                                ->first();

                    if ($currentStock) {
                        $previousQuantity = $currentStock->quantity;
                        $difference = $physicalStock - $previousQuantity;
                        $newQuantity = $physicalStock;

                        if ($difference != 0) {
                            $currentStock->quantity = $newQuantity;
                            $currentStock->save();

                            StockAdjustmentLog::create([
                                'current_stock_id' => $currentStock->id,
                                'user_id' => Auth::id(),
                                'store_id' => $store_id,
                                'previous_quantity' => $previousQuantity,
                                'new_quantity' => $newQuantity,
                                'adjustment_quantity' => abs($difference),
                                'adjustment_type' => $difference > 0 ? 'increase' : 'decrease',
                                'reason' => 'Batch Stock Count Adjustment',
                                'notes' => 'Adjusted via batch stock count import',
                                'reference_number' => 'BSC-ADJ-' . time() . '-' . $productId
                            ]);

                            StockTracking::create([
                                'product_id' => $productId,
                                'store_id' => $store_id,
                                'quantity' => $difference,
                                'tracking_type' => 'batch_stock_count_adjustment',
                                'tracking_id' => $currentStock->id, 
                                'user_id' => Auth::id(),
                                'created_by' => Auth::id(),
                                'updated_by' => Auth::id(),
                                'description' => 'Batch stock count adjustment from imported file'
                            ]);
                        }
                        $successful_records++;
                    } else {
                        // If product stock entry doesn't exist, create it if physical stock > 0
                        if ($physicalStock > 0) {
                             $newStock = CurrentStock::create([
                                'product_id' => $productId,
                                'store_id' => $store_id,
                                'quantity' => $physicalStock,
                                'unit_cost' => 0, // Default or fetch from product, needs design decision
                                'batch_number' => 'N/A',
                                'expiry_date' => null,
                                'shelf_number' => 'N/A',
                                'created_by' => Auth::id()
                            ]);

                             StockAdjustmentLog::create([
                                'current_stock_id' => $newStock->id,
                                'user_id' => Auth::id(),
                                'store_id' => $store_id,
                                'previous_quantity' => 0,
                                'new_quantity' => $physicalStock,
                                'adjustment_quantity' => $physicalStock,
                                'adjustment_type' => 'increase',
                                'reason' => 'Initial Stock from Batch Count',
                                'notes' => 'New stock record created via batch stock count import',
                                'reference_number' => 'BSC-NEW-' . time() . '-' . $productId
                            ]);

                            StockTracking::create([
                                'product_id' => $productId,
                                'store_id' => $store_id,
                                'quantity' => $physicalStock,
                                'tracking_type' => 'batch_stock_count_new_entry',
                                'tracking_id' => $newStock->id,
                                'user_id' => Auth::id(),
                                'created_by' => Auth::id(),
                                'updated_by' => Auth::id(),
                                'description' => 'New stock entry from batch stock count import'
                            ]);
                            $successful_records++;
                        } else {
                             throw new Exception('Product ID ' . $productId . ' not found in current stock and physical stock is zero.');
                        }
                    }
                } catch (Exception $e) {
                    $failed_records++;
                    $error_log[] = 'Row ' . $row_number . ': ' . $e->getMessage();
                    Log::error('Batch Stock Count Row Processing Failed: ' . $e->getMessage(), ['row_data' => $row]);
                }
            }
            DB::commit();

            // Update import history (if we had a dedicated import history table for this feature)
            // For now, we will just return success/failure messages.

            $message = "Batch stock count processed. Successful: {$successful_records}, Failed: {$failed_records}.";
            if ($failed_records > 0) {
                $message .= " Check error log for details.";
                return redirect()->route('batch-stock-count.index')->with('warning', $message);
            } else {
                return redirect()->route('batch-stock-count.index')->with('success', $message);
            }

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Batch Stock Count Process Failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            return back()->with('error', 'Batch stock count failed: ' . $e->getMessage());
        }
    }

    private function validateRow($row, $row_number)
    {
        $errors = [];

        // Column A: Product ID (required, numeric, exists in products table)
        if (empty($row[0])) {
            $errors[] = "Row " . $row_number . ": Product ID is required.";
        } elseif (!is_numeric($row[0])) {
            $errors[] = "Row " . $row_number . ": Product ID must be numeric.";
        } elseif (!Product::where('id', $row[0])->exists()) {
            $errors[] = "Row " . $row_number . ": Product ID '" . $row[0] . "' does not exist.";
        }

        // Column B: Physical Stock (required, numeric, non-negative)
        if (empty($row[1]) && $row[1] !== 0 && $row[1] !== '0') {
            $errors[] = "Row " . $row_number . ": Physical Stock is required.";
        } elseif (!is_numeric($row[1])) {
            $errors[] = "Row " . $row_number . ": Physical Stock must be numeric.";
        } elseif ($row[1] < 0) {
            $errors[] = "Row " . $row_number . ": Physical Stock cannot be negative.";
        }

        return $errors;
    }
} 