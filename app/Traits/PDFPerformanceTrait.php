<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

trait PDFPerformanceTrait
{
    /**
     * Chunk large datasets for processing
     */
    protected function chunkData($query, $chunkSize = 1000, $callback = null)
    {
        return function($chunk) use ($callback) {
            if ($callback && is_callable($callback)) {
                $callback($chunk);
            }
        };
    }

    /**
     * Optimize queries for large datasets
     */
    protected function optimizeQuery($query, $limit = null, $offset = null)
    {
        $query->selectRaw('SQL_CALC_FOUND_ROWS *');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        if ($offset) {
            $query->offset($offset);
        }
        
        return $query;
    }

    /**
     * Stream large data sets to prevent memory issues
     */
    protected function streamData($query, $chunkSize = 100, $processCallback)
    {
        $offset = 0;
        $hasMore = true;
        
        while ($hasMore) {
            $chunk = clone $query;
            $chunk->limit($chunkSize)->offset($offset);
            $results = $chunk->get();
            
            if ($results->isEmpty()) {
                $hasMore = false;
                break;
            }
            
            $processCallback($results);
            $offset += $chunkSize;
            
            // Force garbage collection for large datasets
            if ($offset % ($chunkSize * 10) === 0) {
                gc_collect_cycles();
            }
        }
    }

    /**
     * Create optimized query for product ledger reports
     */
    protected function optimizeProductLedgerQuery($productId, $storeId = null, $limit = null, $offset = null)
    {
        $query = DB::table('product_ledger')
            ->join('inv_products', 'inv_products.id', '=', 'product_ledger.product_id')
            ->select(
                'product_ledger.product_id',
                'inv_products.name as product_name',
                'inv_products.brand',
                'inv_products.pack_size',
                'inv_products.sales_uom',
                'product_ledger.received',
                'product_ledger.outgoing',
                'product_ledger.method',
                'product_ledger.date'
            )
            ->where('product_ledger.product_id', $productId);

        if ($storeId && !is_all_store()) {
            $query->where('product_ledger.store_id', $storeId);
        }

        return $this->optimizeQuery($query, $limit, $offset);
    }

    /**
     * Optimize inventory count sheet queries
     */
    protected function optimizeInventoryCountQuery($storeId = null, $limit = null, $offset = null)
    {
        $query = DB::table('inv_current_stock as cs')
            ->join('inv_products as p', 'cs.product_id', '=', 'p.id')
            ->select(
                'cs.product_id',
                'p.name as product_name',
                'p.brand',
                'p.pack_size',
                'p.sales_uom',
                'cs.store_id',
                'cs.shelf_number',
                DB::raw('SUM(cs.quantity) as quantity_on_hand')
            )
            ->groupBy(['cs.product_id', 'cs.store_id', 'cs.shelf_number', 'p.name', 'p.brand', 'p.pack_size', 'p.sales_uom']);

        if ($storeId && !is_all_store()) {
            $query->where('cs.store_id', $storeId);
        }

        return $this->optimizeQuery($query, $limit, $offset);
    }

    /**
     * Get data in paginated chunks for PDF generation
     */
    protected function getPaginatedData($query, $page = 1, $perPage = 500)
    {
        $offset = ($page - 1) * $perPage;
        return $query->limit($perPage)->offset($offset)->get();
    }

    /**
     * Estimate total records for progress tracking
     */
    protected function estimateTotalRecords($baseQuery)
    {
        try {
            $countQuery = clone $baseQuery;
            $countQuery->selectRaw('COUNT(*) as total');
            $result = $countQuery->first();
            return $result->total ?? 0;
        } catch (\Exception $e) {
            Log::warning('Failed to estimate total records: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Log performance metrics
     */
    protected function logPerformance($operation, $startTime, $recordCount, $memoryUsage = null)
    {
        $endTime = microtime(true);
        $duration = $endTime - $startTime;
        
        Log::info("PDF Performance - {$operation}", [
            'duration_seconds' => round($duration, 2),
            'records_processed' => $recordCount,
            'memory_peak_mb' => $memoryUsage ? round($memoryUsage / 1024 / 1024, 2) : null,
            'records_per_second' => $duration > 0 ? round($recordCount / $duration, 2) : 0
        ]);
    }

    /**
     * Generate progress callback for long operations
     */
    protected function createProgressCallback($operation, $totalRecords)
    {
        $startTime = microtime(true);
        $processedRecords = 0;
        
        return function($currentRecords) use ($operation, $totalRecords, &$processedRecords, $startTime) {
            $processedRecords += $currentRecords;
            $progress = $totalRecords > 0 ? ($processedRecords / $totalRecords) * 100 : 0;
            
            // Log progress every 10%
            if (fmod($progress, 10) < 1 || $progress == 100) {
                $this->logPerformance("{$operation} Progress", $startTime, $processedRecords);
            }
            
            return [
                'progress' => round($progress, 1),
                'processed' => $processedRecords,
                'total' => $totalRecords,
                'eta' => $this->calculateETA($startTime, $processedRecords, $totalRecords)
            ];
        };
    }

    /**
     * Calculate estimated time remaining
     */
    protected function calculateETA($startTime, $processed, $total)
    {
        if ($processed == 0 || $total == 0) {
            return 'Calculating...';
        }
        
        $elapsed = microtime(true) - $startTime;
        $rate = $processed / $elapsed;
        $remaining = ($total - $processed) / $rate;
        
        if ($remaining < 60) {
            return round($remaining) . ' seconds';
        } elseif ($remaining < 3600) {
            return round($remaining / 60) . ' minutes';
        } else {
            return round($remaining / 3600) . ' hours';
        }
    }

    /**
     * Clean up memory after large operations
     */
    protected function cleanupMemory()
    {
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        
        // Clear Laravel's cache if it's consuming too much memory
        if (memory_get_usage(true) > 512 * 1024 * 1024) { // 512MB
            \Illuminate\Support\Facades\Cache::flush();
        }
    }
}