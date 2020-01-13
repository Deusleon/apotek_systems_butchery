<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $table = 'sales_prices';
    public $timestamps = false;

    public function currentStock()
    {
        return $this->belongsTo(CurrentStock::class, 'stock_id');
    }

    public function priceCategory()
    {
        return $this->belongsTo(PriceCategory::class, 'price_category_id');
    }

}
