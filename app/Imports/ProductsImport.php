<?php

namespace App\Imports;

use App\Category;
use App\Product;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
 {
    private $errors = [];
    private $successCount = 0;
    private $failCount = 0;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function model( array $row )
 {
        // Skip empty rows
        if ( empty( array_filter( $row ) ) ) {
            return null;
        }

        try {
            // Get or create category
            $category = Category::firstOrCreate(
                [ 'name' => trim( $row[ 'category' ] ) ],
                [ 'created_by' => Auth::id() ]
            );

            // Check if product already exists by name + category
            $existingProduct = Product::where( 'name', trim( $row[ 'name' ] ) )
            ->where( 'category_id', $category->id )
            ->first();

            if ( $existingProduct ) {
                // Update existing product
                $existingProduct->update( [
                    'barcode' => $row[ 'barcode' ] ?? null,
                    'brand' => $row[ 'brand' ] ?? null,
                    'pack_size' => $row[ 'pack_size' ] ?? null,
                    'sales_uom' => $row[ 'unit' ] ?? null,
                    'min_quantinty' => $row[ 'min_stock' ] !== null ? ( int )$row[ 'min_stock' ] : null,
                    'max_quantinty' => $row[ 'max_stock' ] !== null ? ( int )$row[ 'max_stock' ] : null,
                    'updated_by' => Auth::id(),
                ] );
                $this->successCount++;
                return null;
                // Don't create new model
            }

            // Create new product
            $product = new Product([
                'barcode' => $row['barcode'] ?? null,
                'name' => trim($row['name']),
                'brand' => $row['brand'] ?? null,
                'pack_size' => $row['pack_size'] ?? null,
                'sales_uom' => $row['unit'] ?? null,
                'min_quantinty' => $row[ 'min_stock' ] !== null ? ( int )$row[ 'min_stock' ] : null,
                'max_quantinty' => $row[ 'max_stock' ] !== null ? ( int )$row[ 'max_stock' ] : null,
                'category_id' => $category->id,
                'type' => 'stockable',
                'status' => 1,
                'created_by' => Auth::id(),
            ]);

            $this->successCount++;
            return $product;

        } catch (\Exception $e) {
            $this->failCount++;
            $errorMessage = 'Row error: ' . $e->getMessage();
            $this->errors[] = $errorMessage;

            // Log detailed error information
            \Illuminate\Support\Facades\Log::error('Products Import Row Failed', [
                'row_data' => $row,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'timestamp' => now()
            ]);

            return null;
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'pack_size' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:50',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'name.required' => 'Product name is required',
            'category.required' => 'Category is required',
            'pack_size.numeric' => 'Pack size must be a number',
            'min_stock.numeric' => 'Min stock must be a number',
            'max_stock.numeric' => 'Max stock must be a number',
        ];
    }

    /**
     * Batch size for inserts
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Get import results
     */
    public function getResults()
    {
        return [
            'success_count' => $this->successCount,
            'fail_count' => $this->failCount,
            'errors' => $this->errors,
            ];
        }
    }
