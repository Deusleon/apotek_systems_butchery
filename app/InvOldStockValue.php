<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvOldStockValue extends Model
{
    protected $table = 'inv_old_stock_values';

    protected $fillable = [
        'product_id',
        'store_id',
        'price_category_id',
        'quantity',
        'buy_price',
        'sell_price',
        'snapshot_date',
    ];

    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }
}
