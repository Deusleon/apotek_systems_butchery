<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $fillable = [
        'production_date',
        'details',
        'items_received',
        'total_weight',
        'meat',
        'steak',
        'beef_fillet',
        'weight_difference',
        'beef_liver',
        'tripe'
    ];

    public function distributions()
    {
        return $this->hasMany(ProductionDistribution::class);
    }
}