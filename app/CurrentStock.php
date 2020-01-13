<?php

namespace App;

use App\Product as product;
use App\StockAdjustment as stockAdjustment;
use App\Store as store;
use Illuminate\Database\Eloquent\Model;

class CurrentStock extends Model
{
    protected $table = 'inv_current_stock';

    public function product()
    {
        return $this->belongsTo(product::class, 'product_id');
    }

    public function stockTransfer()
    {
        return $this->hasMany(StockTransfer::class, 'stock_id');
    }

    public function store()
    {
        return $this->belongsTo(store::class);
    }

    public function stockAdjustment()
    {
        return $this->hasMany(stockAdjustment::class, 'stock_id', 'id');
    }

    public function priceList()
    {
        return $this->hasMany(PriceList::class, 'stock_id');
    }

    public function stockIssue()
    {
        return $this->hasMany(StockIssue::class, 'stock_id');
    }

    public function issueReturn()
    {
        return $this->hasMany(IssueReturn::class, 'stock_id');
    }

    public function productLedger()
    {
        return $this->hasMany(ProductLedger::class, 'stock_id');
    }

    public function salesDetail()
    {
        return $this->hasMany(SalesDetail::class, 'stock_id');
    }

    public function stockTracking()
    {
        return $this->hasMany(StockTracking::class, 'stock_id');
    }

}
