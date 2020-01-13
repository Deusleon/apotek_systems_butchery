<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'inv_suppliers';

    public $timestamps = false;

    public function order()
    {
        return $this->hasMany(Order::class, 'supplier_id');
    }


}
