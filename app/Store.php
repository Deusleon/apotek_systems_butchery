<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CurrentStock as currentStock;

class Store extends Model
{
    protected $table = 'inv_stores';
    public $timestamps = false;

    public function currentStock(){
        return $this->hasMany(currentStock::class,'store_id');
    }

    public function adjustment(){
        return $this->hasMany(StockAdjustment::class,'created_by');
    }

    public function stockTransfer()
    {
        return $this->hasMany(StockTransfer::class, 'from_store');
    }

    public function stockTransferTo()
    {
        return $this->hasMany(StockTransfer::class, 'to_store');
    }

}
