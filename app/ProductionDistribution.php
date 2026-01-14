<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionDistribution extends Model
{
    protected $fillable = [
        'production_id', 
        'distribution_type',
        'store_id', 
        'customer_id',
        'order_to',
        'meat_type', 
        'weight_distributed',
        'notes'
    ];

    // Distribution type constants
    const TYPE_BRANCH = 'branch';
    const TYPE_CASH_SALE = 'cash_sale';
    const TYPE_ORDER = 'order';

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get distribution type label
     */
    public function getDistributionTypeLabelAttribute()
    {
        switch ($this->distribution_type) {
            case self::TYPE_BRANCH:
                return 'Branch';
            case self::TYPE_CASH_SALE:
                return 'Cash Sale';
            case self::TYPE_ORDER:
                return 'Order';
            default:
                return ucfirst($this->distribution_type);
        }
    }

    /**
     * Get recipient name based on distribution type
     */
    public function getRecipientNameAttribute()
    {
        switch ($this->distribution_type) {
            case self::TYPE_BRANCH:
                return $this->store ? $this->store->name : 'Unknown Branch';
            case self::TYPE_CASH_SALE:
                return $this->customer ? $this->customer->name : 'Cash Sale';
            case self::TYPE_ORDER:
                return $this->order_to ? 'Order: ' . $this->order_to : 'Order';
            default:
                return 'Unknown';
        }
    }
}

