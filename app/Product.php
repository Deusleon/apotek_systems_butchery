<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'inv_products';
    protected $fillable = [
        'name',
        'barcode',
        'brand',
        'pack_size',
        'category_id',
        'sub_category_id',
        'generic_name',
        'standard_uom',
        'sales_uom',
        'purchase_uom',
        'indication',
        'dosage',
        'min_quantinty',
        'max_quantinty',
        'type',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function currentStock()
    {
        return $this->hasMany(CurrentStock::class, 'product_id');
    }

    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'product_id');
    }

    public function stockTransfer()
    {
        return $this->hasMany(StockTransfer::class, 'product_id');
    }

    public function incomingStock()
    {
        return $this->hasMany(GoodsReceiving::class, 'product_id');
    }

    public function quoteDetail()
    {
        return $this->hasMany(SalesQuoteDetail::class, 'product_id');
    }
}
