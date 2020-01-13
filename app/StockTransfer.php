<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $table = 'inv_stock_transfers';

    public function currentStock()
    {
        return $this->belongsTo(CurrentStock::class, 'stock_id');
    }

    public function fromStore()
    {
        return $this->belongsTo(Store::class, 'from_store');
    }

    public function toStore()
    {
        return $this->belongsTo(Store::class, 'to_store');
    }


}
