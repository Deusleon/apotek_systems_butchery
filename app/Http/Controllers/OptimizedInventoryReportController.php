<?php

namespace App\Http\Controllers;

use App\AdjustmentReason;
use App\Category;
use App\CurrentStock;
use App\IssueReturn;
use App\Product;
use App\Setting;
use App\StockAdjustment;
use App\StockIssue;
use App\StockTracking;
use App\StockTransfer;
use App\StockCountSchedule;
use App\Store;
use App\SalesDetail;
use App\Traits\PDFPerformanceTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade as PDF;

ini_set('max_execution_time', 1200); // 20 minutes
set_time_limit(1200);
ini_set('memory_limit', '1024M'); // 1GB

class OptimizedInventoryReportController extends Controller
{
    use PDFPerformanceTrait;

    public function __construct()
    {
        $this->middleware(['auth', 'permission:View Inventory Reports']);
    }

    /**
     * Optimized product ledger report with chunking
     */
    public function optimizedProductLedgerReport(Request $request)
    {
        $startTime = microtime(true);
        
        try {
            $productId = $request->product;
            $storeId = current_store_id();
            
            // Create optimized query
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
                    'product_ledger.date',
                    'product_ledger.user'
                )
                ->where('product_ledger.product_id', $productId);

            if (!is_all_store()) {
                $query->where('product_ledger.store_id', $storeId);
            }

            $query->orderBy('product_ledger.date', 'asc');

            // Get total count for progress tracking
            $totalRecords = $this->estimateTotalRecords(clone $query);
            
            // Create progress callback
            $progressCallback = $this->createProgressCallback('Product Ledger Processing', $totalRecords);

            // Stream data in chunks
            $processedData = [];
            $chunkSize = 500; // Process 500 records at a time
            
            $this->streamData($query, $chunkSize, function($chunk) use (&$processedData, $progressCallback) {
                foreach ($chunk as $row) {
                    $processedData[] = [
                        'date' => $row->date,
                        'name' => $row->product_name . ' ' . ($row->brand ?? '') . ' ' . ($row->pack_size ?? '') . ($row->sales_uom ?? ''),
                        'method' => $row->method,
                        'received' => $row->received,
                        'outgoing' => abs($row->outgoing),
                        'user' => $this->getUserName($row->user),
                    ];
                }
                
                // Update progress
                $progress = $progressCallback(count($chunk));
                if ($progress['progress'] % 20 === 0) { // Log every 20%
                    Log::info("Product Ledger Progress: {$progress['progress']}% - {$progress['processed']}/{$progress['total']}");
                }
            });

            // Calculate running balance
            $balance = 0;
            foreach ($processedData as &$row) {
                $balance += $row['received'] + $row['outgoing'];
                $row['balance'] = $balance;
            }

            $pharmacy = $this->getPharmacySettings();

            // Generate PDF with optimized memory usage
            $pdf = PDF::loadView(
                'inventory_reports.product_ledger_report_pdf', 
                compact('processedData', 'pharmacy')
            )
            ->setPaper('a4', '')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'chroot' => public_path(),
                'tempDir' => storage_path('app/temp'),
            ]);

            $this->logPerformance('Optimized Product Ledger Report', $startTime, count($processedData));
            $this->cleanupMemory();

            return $pdf->stream('optimized_product_ledger_report.pdf');

        } catch (\Exception $e) {
            Log::error('Optimized Product Ledger Report failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Report generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optimized inventory count sheet with streaming
     */
    public function optimizedInventoryCountSheet(Request $request)
    {
        $startTime = microtime(true);
        
        try {
            $storeId = current_store_id();
            $showQoH = $request->get('showQoH', true);
            $default_store = current_store()->name ?? 'Unknown Store';

            // Create optimized query
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

            if (!is_all_store()) {
                $query->where('cs.store_id', $storeId);
            }

            $query->orderBy('p.name', 'asc');

            // Get total count
            $totalRecords = $this->estimateTotalRecords(clone $query);
            $progressCallback = $this->createProgressCallback('Inventory Count Sheet Processing', $totalRecords);

            // Stream data and group by store
            $groupedData = [];
            $chunkSize = 1000;

            $this->streamData($query, $chunkSize, function($chunk) use (&$groupedData, $progressCallback) {
                foreach ($chunk as $stock) {
                    $storeKey = "Store_{$stock->store_id}";
                    
                    if (!isset($groupedData[$storeKey])) {
                        $groupedData[$storeKey] = [];
                    }
                    
                    $groupedData[$storeKey][] = [
                        'product_id' => $stock->product_id,
                        'product_name' => $stock->product_name,
                        'brand' => $stock->brand,
                        'pack_size' => $stock->pack_size,
                        'sales_uom' => $stock->sales_uom,
                        'shelf_no' => $stock->shelf_number,
                        'quantity_on_hand' => (float)$stock->quantity_on_hand,
                    ];
                }
                
                $progress = $progressCallback(count($chunk));
                if ($progress['progress'] % 15 === 0) {
                    Log::info("Inventory Count Progress: {$progress['progress']}%");
                }
            });

            if (empty($groupedData)) {
                return response()->view('error_pages.pdf_zero_data');
            }

            $pharmacy = $this->getPharmacySettings();

            // Generate PDF with memory optimization
            $pdf = PDF::loadView(
                'stock_management.daily_stock_count.inventory_count_sheet',
                compact('groupedData', 'showQoH', 'pharmacy', 'default_store')
            )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false, // Disable remote resources for security and speed
                'dpi' => 150, // Lower DPI for faster generation
                'defaultFont' => 'dejavusans',
            ]);

            $this->logPerformance('Optimized Inventory Count Sheet', $startTime, array_sum(array_map('count', $groupedData)));
            $this->cleanupMemory();

            return $pdf->stream('optimized_inventory_count_sheet.pdf');

        } catch (\Exception $e) {
            Log::error('Optimized Inventory Count Sheet failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Report generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optimized products details report with pagination
     */
    public function optimizedProductDetailsReport(Request $request)
    {
        $startTime = microtime(true);
        
        try {
            $categoryId = $request->category_name_detail;
            $storeId = current_store_id();
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 1000); // Process 1000 products at a time

            // Create base query
            $query = DB::table('inv_products as p')
                ->select(
                    'p.id as product_id',
                    'p.name',
                    'p.brand',
                    'p.pack_size',
                    'p.sales_uom',
                    'c.name as category'
                )
                ->leftJoin('inv_categories as c', 'p.category_id', '=', 'c.id');

            if ($categoryId) {
                $query->where('p.category_id', $categoryId);
            }

            if (!is_all_store()) {
                $query->leftJoin('inv_current_stock as cs', function($join) use ($storeId) {
                    $join->on('cs.product_id', '=', 'p.id')
                         ->where('cs.store_id', '=', $storeId);
                });
            }

            $query->orderBy('p.name', 'asc');

            // Get paginated results
            $offset = ($page - 1) * $perPage;
            $products = $query->limit($perPage)->offset($offset)->get();

            // Get total count
            $totalQuery = clone $query;
            $totalCount = $totalQuery->count();

            // Process data
            $processedData = [];
            foreach ($products as $product) {
                $processedData[] = [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'brand' => $product->brand ?? 'N/A',
                    'pack_size' => $product->pack_size ?? 'N/A',
                    'sales_uom' => $product->sales_uom ?? 'N/A',
                    'category' => $product->category ?? 'Uncategorized',
                ];
            }

            $pharmacy = $this->getPharmacySettings();

            // Generate PDF for current page
            $pdf = PDF::loadView(
                'inventory_reports.product_detail_report_pdf',
                compact('processedData', 'pharmacy', 'page', 'totalCount', 'perPage')
            )
            ->setPaper('a4', '')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'dpi' => 150,
            ]);

            $this->logPerformance('Optimized Product Details Report', $startTime, count($processedData));
            $this->cleanupMemory();

            $filename = "optimized_product_details_page_{$page}.pdf";
            return $pdf->stream($filename);

        } catch (\Exception $e) {
            Log::error('Optimized Product Details Report failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Report generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pharmacy settings efficiently
     */
    private function getPharmacySettings()
    {
        return cache()->remember('pharmacy_settings', 3600, function() {
            return [
                'name' => Setting::where('id', 100)->value('value') ?? 'Pharmacy',
                'address' => Setting::where('id', 106)->value('value') ?? '',
                'phone' => Setting::where('id', 107)->value('value') ?? '',
                'email' => Setting::where('id', 108)->value('value') ?? '',
                'logo' => Setting::where('id', 105)->value('value') ?? '',
                'tin_number' => Setting::where('id', 102)->value('value') ?? '',
            ];
        });
    }

    /**
     * Get user name efficiently
     */
    private function getUserName($userId)
    {
        if (!$userId) return 'System';
        
        return cache()->remember("user_name_{$userId}", 1800, function() use ($userId) {
            try {
                return DB::table('users')->where('id', $userId)->value('name') ?? 'Unknown';
            } catch (\Exception $e) {
                return 'Unknown';
            }
        });
    }

    /**
     * Generate report with progress tracking
     */
    public function generateWithProgress(Request $request, $reportType)
    {
        $request->validate([
            'callback_url' => 'required|url'
        ]);

        $callbackUrl = $request->callback_url;
        
        // Start background job
        dispatch(function() use ($request, $reportType, $callbackUrl) {
            try {
                $this->generateReportInBackground($request, $reportType, $callbackUrl);
            } catch (\Exception $e) {
                Log::error("Background report generation failed: " . $e->getMessage());
                $this->notifyCallback($callbackUrl, false, $e->getMessage());
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Report generation started in background',
            'status_url' => route('report.status', ['type' => $reportType])
        ]);
    }

    /**
     * Generate report in background
     */
    private function generateReportInBackground(Request $request, $reportType, $callbackUrl)
    {
        switch ($reportType) {
            case 'product_ledger':
                $this->optimizedProductLedgerReport($request);
                break;
            case 'inventory_count':
                $this->optimizedInventoryCountSheet($request);
                break;
            case 'product_details':
                $this->optimizedProductDetailsReport($request);
                break;
            default:
                throw new \Exception("Unknown report type: {$reportType}");
        }

        $this->notifyCallback($callbackUrl, true, 'Report generated successfully');
    }

    /**
     * Notify callback URL
     */
    private function notifyCallback($url, $success, $message)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $client->post($url, [
                'json' => [
                    'success' => $success,
                    'message' => $message,
                    'timestamp' => now()->toISOString()
                ],
                'timeout' => 10
            ]);
        } catch (\Exception $e) {
            Log::warning("Failed to notify callback URL: " . $e->getMessage());
        }
    }
}