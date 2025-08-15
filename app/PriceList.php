<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $table = 'sales_prices';
    public $timestamps = true;

    protected $fillable = [
        'stock_id',
        'price',
        'price_category_id',
        'is_custom',
        'default_markup_percentage',
        'override_reason',
        'override_by',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'default_markup_percentage' => 'decimal:2'
    ];

    public function currentStock()
    {
        return $this->belongsTo(CurrentStock::class, 'stock_id');
    }

    public function priceCategory()
    {
        return $this->belongsTo(PriceCategory::class, 'price_category_id');
    }

    public function overriddenBy()
    {
        return $this->belongsTo(User::class, 'override_by');
    }

    public function calculateDefaultPrice()
    {
        if ($this->currentStock && $this->priceCategory) {
            $cost = $this->currentStock->unit_cost;
            $markup = $this->priceCategory->default_markup_percentage;
            
            if ($cost && $markup) {
                return $cost * (1 + ($markup / 100));
            }
        }
        return null;
    }

    public function setDefaultPrice()
    {
        if (!$this->is_custom) {
            $defaultPrice = $this->calculateDefaultPrice();
            if ($defaultPrice) {
                $this->price = $defaultPrice;
                $this->default_markup_percentage = $this->priceCategory->default_markup_percentage;
                return $this->save();
            }
        }
        return false;
    }

    public function setCustomPrice($price, $reason = null)
    {
        $this->is_custom = true;
        $this->price = $price;
        $this->override_reason = $reason;
        $this->override_by = auth()->id();
        return $this->save();
    }
}
