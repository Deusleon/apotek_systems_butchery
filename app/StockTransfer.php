<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model {
    protected $table = 'inv_stock_transfers';

    protected $fillable = [
        'transfer_no',
        'stock_id',
        'transfer_qty',
        'from_store',
        'to_store',
        'status',
        'remarks',
        'created_by',
        'approved_by',
        'acknowledged_by',
        'evidence'
    ];

    public function currentStock() {
        return $this->belongsTo( CurrentStock::class, 'stock_id' );
    }

    public function createdBy() {
        return $this->belongsTo( User::class, 'created_by' );
    }

    public function approvedBy() {
        return $this->belongsTo( User::class, 'approved_by' );
    }

    public function cancelledBy() {
        return $this->belongsTo( User::class, 'cancelled_by' );
    }
    
    public function updatedBy() {
        return $this->belongsTo( User::class, 'updated_by' );
    }
    public function acknowledgedBy() {
        return $this->belongsTo( User::class, 'acknowledged_by' );
    }

    public function fromStore() {
        return $this->belongsTo( Store::class, 'from_store' );
    }

    public function toStore() {
        return $this->belongsTo( Store::class, 'to_store' );
    }

    public function getRouteKeyName() {
        return 'transfer_no';
    }

    // Helper method to get all items for a transfer
    public function getAllItemsAttribute() {
        return self::with(['currentStock.product', 'fromStore', 'toStore'])
            ->where('transfer_no', $this->transfer_no)
            ->get();
    }
}