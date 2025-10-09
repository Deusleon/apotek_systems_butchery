<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $table = 'purchase_returns';
    public $timestamps = false;

    public function goodsReceiving(){
        return $this->belongsTo(GoodsReceiving::class,'goods_receiving_id','id')
                ->with(['product', 'supplier']);
    }
}