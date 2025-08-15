<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockTransferController;

/*
|--------------------------------------------------------------------------
| Stock Transfer Status Workflow Routes
|--------------------------------------------------------------------------
*/

// Status workflow routes
Route::prefix('stock-transfer')->middleware(['auth'])->group(function () {
    
    // Assign transfer to destination store
    Route::post('/{id}/assign', [StockTransferController::class, 'assignTransfer'])
        ->name('stock-transfer.assign')
        ->middleware('permission:assign_transfers');
    
    // Approve transfer
    Route::post('/{id}/approve', [StockTransferController::class, 'approveTransfer'])
        ->name('stock-transfer.approve')
        ->middleware('permission:approve_transfers');
    
    // Mark transfer as in transit
    Route::post('/{id}/in-transit', [StockTransferController::class, 'markInTransit'])
        ->name('stock-transfer.in-transit')
        ->middleware('permission:manage_transfers');
    
    // Acknowledge transfer receipt
    Route::post('/{id}/acknowledge', [StockTransferController::class, 'acknowledgeTransfer'])
        ->name('stock-transfer.acknowledge')
        ->middleware('permission:acknowledge_transfers');
    
    // Complete transfer
    Route::post('/{id}/complete', [StockTransferController::class, 'completeTransfer'])
        ->name('stock-transfer.complete')
        ->middleware('permission:complete_transfers');
    
    // Update status (general method)
    Route::post('/{id}/status', [StockTransferController::class, 'updateStatus'])
        ->name('stock-transfer.status')
        ->middleware('permission:manage_transfers');
});

?> 