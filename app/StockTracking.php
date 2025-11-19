<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockTracking extends Model
{

    protected $table = 'inv_stock_tracking';
    public $timestamps = false;

    protected $fillable = [
        'stock_id',
        'product_id',
        'quantity',
        'out_mode',
        'store_id',
        'created_by',
        'updated_by',
        'movement',
        'updated_at',
    ];

    public function currentStock()
    {
        return $this->belongsTo(CurrentStock::class, 'stock_id');
    }

    public function user()
    {
      return $this->belongsTo(User::class,'updated_by');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

}
