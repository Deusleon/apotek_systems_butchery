<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CurrentStock as currentStock;

class StockAdjustment extends Model
{
    protected $table = 'inv_stock_adjustments';

    public function currentStock()
    {
        return $this->belongsTo(currentStock::class, 'stock_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
