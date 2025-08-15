<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustmentLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'current_stock_id',
        'user_id',
        'store_id',
        'previous_quantity',
        'new_quantity',
        'adjustment_quantity',
        'adjustment_type',
        'reason',
        'notes',
        'reference_number'
    ];

    public function currentStock()
    {
        return $this->belongsTo(CurrentStock::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public static function log($currentStock, $adjustmentQuantity, $reason, $notes = null)
    {
        $adjustmentType = $adjustmentQuantity > 0 ? 'increase' : 'decrease';
        $previousQuantity = $currentStock->quantity;
        $newQuantity = $previousQuantity + $adjustmentQuantity;
        
        return self::create([
            'current_stock_id' => $currentStock->id,
            'user_id' => auth()->id(),
            'store_id' => $currentStock->store_id,
            'previous_quantity' => $previousQuantity,
            'new_quantity' => $newQuantity,
            'adjustment_quantity' => abs($adjustmentQuantity),
            'adjustment_type' => $adjustmentType,
            'reason' => $reason,
            'notes' => $notes,
            'reference_number' => 'ADJ-' . time()
        ]);
    }
} 