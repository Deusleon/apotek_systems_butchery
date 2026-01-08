<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionDistribution extends Model
{
    protected $fillable = ['production_id', 'store_id', 'meat_type', 'weight_distributed'];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
