<?php

// Add these routes to your web.php or create a separate file for optimized reports

use App\Http\Controllers\OptimizedInventoryReportController;

// Optimized Report Routes
Route::prefix('optimized-reports')->middleware(['auth', 'permission:View Inventory Reports'])->group(function () {
    
    // Product Ledger Reports
    Route::get('product-ledger', [OptimizedInventoryReportController::class, 'optimizedProductLedgerReport'])
         ->name('optimized.product-ledger');
    
    Route::post('product-ledger/progress', [OptimizedInventoryReportController::class, 'generateWithProgress'])
         ->name('optimized.product-ledger.progress');
    
    // Inventory Count Sheet Reports
    Route::get('inventory-count-sheet', [OptimizedInventoryReportController::class, 'optimizedInventoryCountSheet'])
         ->name('optimized.inventory-count-sheet');
    
    Route::post('inventory-count-sheet/progress', [OptimizedInventoryReportController::class, 'generateWithProgress'])
         ->name('optimized.inventory-count-sheet.progress');
    
    // Product Details Reports
    Route::get('product-details', [OptimizedInventoryReportController::class, 'optimizedProductDetailsReport'])
         ->name('optimized.product-details');
    
    Route::post('product-details/progress', [OptimizedInventoryReportController::class, 'generateWithProgress'])
         ->name('optimized.product-details.progress');
    
    // Report status tracking
    Route::get('status/{type}', [OptimizedInventoryReportController::class, 'getReportStatus'])
         ->name('optimized.report.status');
    
    // Background job management
    Route::get('background-jobs', [OptimizedInventoryReportController::class, 'getBackgroundJobs'])
         ->name('optimized.background-jobs');
    
    Route::delete('background-jobs/{jobId}', [OptimizedInventoryReportController::class, 'cancelBackgroundJob'])
         ->name('optimized.background-jobs.cancel');
});

// API routes for real-time progress updates
Route::prefix('api/optimized-reports')->middleware(['auth'])->group(function () {
    Route::get('progress/{jobId}', [OptimizedInventoryReportController::class, 'getJobProgress'])
         ->name('api.optimized.reports.progress');
    
    Route::get('jobs', [OptimizedInventoryReportController::class, 'getActiveJobs'])
         ->name('api.optimized.reports.jobs');
});

// Webhook callback for background job completion
Route::post('api/reports/callback', [OptimizedInventoryReportController::class, 'handleCallback'])
     ->name('api.reports.callback');