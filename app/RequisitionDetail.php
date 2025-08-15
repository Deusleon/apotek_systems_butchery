<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequisitionDetail extends Model
{
    public $timestamps = false;

    public function products_()
    {
        return $this->belongsTo(Product::class, 'product', 'id');
    }
}
