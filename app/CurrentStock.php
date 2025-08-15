<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\StockUpdated;
use App\StockAdjustmentLog;

class CurrentStock extends Model
{
    protected $table = 'inv_current_stock';

    protected $fillable = [
        'product_id',
        'store_id',
        'quantity',
        'stock_value',
        'last_calculated_at',
        'unit_cost',
        'batch_number',
        'expiry_date',
        'shelf_number',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'expiry_date',
        'last_calculated_at'
    ];

    protected $dispatchesEvents = [
        'updated' => StockUpdated::class,
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function stockTransfer()
    {
        return $this->hasMany(StockTransfer::class, 'stock_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function stockAdjustment()
    {
        return $this->hasMany(StockAdjustment::class, 'stock_id', 'id');
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

    public function getActivePrice()
    {
        return $this->priceList()->where('status', 1)->first();
    }

    public function calculateStockValue()
    {
        $price = $this->getActivePrice() ? $this->getActivePrice()->price : $this->unit_cost;
        $this->stock_value = $this->quantity * $price;
        $this->last_calculated_at = now();
        return $this->save();
    }

    public function isLowStock()
    {
        if ($this->alert_threshold === null || $this->min_stock_level === null) {
            return false;
        }
        return $this->quantity <= $this->alert_threshold;
    }

    public function getStockStatus()
    {
        if ($this->quantity <= $this->min_stock_level) {
            return 'critical';
        } elseif ($this->isLowStock()) {
            return 'low';
        }
        return 'normal';
    }

    public function adjustStock($adjustment, $reason, $notes = null)
    {
        $this->quantity += $adjustment;
        $this->save();

        // Log the adjustment
        StockAdjustmentLog::log($this, $adjustment, $reason, $notes);

        return $this;
    }

    public function stockAdjustmentLogs()
    {
        return $this->hasMany(StockAdjustmentLog::class);
    }
}
